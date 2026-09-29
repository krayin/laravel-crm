import { expect, Locator, Page } from "@playwright/test";

import { SettingsPage } from "../SettingsPage";
import { generateDescription, generateFirstName } from "../../utils/faker";
import { PermissionType, runSuffix } from "../../utils/acl";

export type RoleData = {
    name: string;
    description: string;
    permissionType: PermissionType;

    /**
     * ACL keys to tick in the access-control tree. Only read when `permissionType` is `custom`.
     */
    permissions?: string[];
};

/**
 * Build role data with a unique name, so repeated runs never collide in the listing.
 */
export function buildRoleData(overrides: Partial<RoleData> = {}): RoleData {
    return {
        name: `${generateFirstName()} Role ${runSuffix()}`,
        description: generateDescription().slice(0, 60),
        permissionType: "custom",
        permissions: [],
        ...overrides,
    };
}

export class RolesPage extends SettingsPage {
    readonly page: Page;

    readonly createRoleButton: Locator;
    readonly saveRoleButton: Locator;

    readonly nameInput: Locator;
    readonly descriptionTextarea: Locator;
    readonly permissionTypeSelect: Locator;

    readonly createSuccessMessage: Locator;
    readonly updateSuccessMessage: Locator;
    readonly deleteSuccessMessage: Locator;

    readonly permissionsExceedOwnError: Locator;
    readonly currentRoleEditError: Locator;
    readonly currentRoleDeleteError: Locator;
    readonly beingUsedError: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;

        this.createRoleButton = page.getByRole("link", { name: "Create Roles" });
        this.saveRoleButton = page.getByRole("button", { name: "Save Role" });

        this.nameInput = page.locator('input[name="name"]');
        this.descriptionTextarea = page.locator('textarea[name="description"]');
        this.permissionTypeSelect = page.locator('select[name="permission_type"]');

        this.createSuccessMessage = page.getByText("Role created successfully.");
        this.updateSuccessMessage = page.getByText("Role updated successfully.");
        this.deleteSuccessMessage = page.getByText("Role deleted successfully.");

        this.permissionsExceedOwnError = page.getByText(
            "You can only create or edit roles whose permissions are the same as or fewer than your own."
        );
        this.currentRoleEditError = page.getByText("Can not edit the role assigned to your own account.");
        this.currentRoleDeleteError = page.getByText("Can not delete role assigned to the current user.");
        this.beingUsedError = page.getByText("Role can not be deleted, as this is being used in admin user.");
    }

    /**
     * The access-control tree renders each ACL key as a hidden checkbox carrying the key as its
     * `value`.
     *
     * The `id` is not usable as an anchor: the tree component prefixes it with a token regenerated
     * on every render (`mttr4p8q_leads.edit`), so only `value` is stable across page loads. The
     * input itself is `hidden`, so the wrapping label is what a user actually clicks.
     */
    permissionCheckbox(key: string): Locator {
        return this.page.locator(`input[name="permissions[]"][value="${key}"]`);
    }

    permissionLabel(key: string): Locator {
        return this.page.locator(`label:has(input[name="permissions[]"][value="${key}"])`);
    }

    async navigateToCreateRole(): Promise<void> {
        await this.page.goto("admin/settings/roles/create");
    }

    /**
     * Tick an ACL key in the tree.
     *
     * Selection is hierarchical in both directions: ticking a key also ticks its ancestors AND its
     * whole subtree. Ticking `leads` therefore grants `leads.delete` too.
     *
     * Pass only the most specific keys a role should hold and let the ancestors follow — listing a
     * parent alongside its child silently widens the role far beyond what the test intended.
     */
    async selectPermission(key: string): Promise<void> {
        const label = this.permissionLabel(key);

        await expect(label, `permission "${key}" should be offered in the tree`).toBeVisible();

        if (!(await this.permissionCheckbox(key).isChecked())) {
            await label.click();
        }
    }

    async deselectPermission(key: string): Promise<void> {
        if (await this.permissionCheckbox(key).isChecked()) {
            await this.permissionLabel(key).click();
        }
    }

    async selectPermissions(keys: string[]): Promise<void> {
        for (const key of keys) {
            await this.selectPermission(key);
        }
    }

    /**
     * Whether an ACL key is offered at all. `Acl::getAuthorizedItems()` prunes the tree to the
     * acting user's own permissions, so a key being absent is itself the assertion in the
     * escalation suites.
     */
    async isPermissionOffered(key: string): Promise<boolean> {
        return (await this.permissionLabel(key).count()) > 0;
    }

    async fillRoleForm(role: RoleData): Promise<void> {
        await this.nameInput.fill(role.name);
        await this.descriptionTextarea.fill(role.description);
        await this.permissionTypeSelect.selectOption(role.permissionType);

        if (role.permissionType === "custom") {
            await this.selectPermissions(role.permissions ?? []);
        }
    }

    /**
     * Create a role through the form and confirm it lands in the listing.
     */
    async createRole(role: RoleData): Promise<void> {
        await this.navigateToCreateRole();
        await this.fillRoleForm(role);
        await this.saveRoleButton.click();

        await expect(this.createSuccessMessage.first()).toBeVisible();

        await this.searchRole(role.name);

        await expect(this.page.getByText(role.name).first()).toBeVisible();
    }

    /**
     * Submit the create form without asserting the outcome, for cases where the expected result is
     * a refusal rather than a new role.
     */
    async submitRoleForm(role: RoleData): Promise<void> {
        await this.navigateToCreateRole();
        await this.fillRoleForm(role);
        await this.saveRoleButton.click();
    }

    async searchRole(name: string): Promise<void> {
        await this.navigateToRoles();
        await this.searchInputExact.fill(name);
        await this.page.keyboard.press("Enter");
        await this.page.waitForLoadState("networkidle");
    }

    /**
     * Open a named role's edit form by clicking its row's edit icon after searching for it, so the
     * test never depends on the role's position in an unfiltered listing.
     */
    async openRoleForEdit(name: string): Promise<void> {
        await this.searchRole(name);
        await this.firstEditIcon.click();
        await this.page.waitForLoadState("networkidle");
    }

    async updateRole(name: string, changes: Partial<RoleData>): Promise<void> {
        await this.openRoleForEdit(name);

        if (changes.name) {
            await this.nameInput.fill(changes.name);
        }

        if (changes.description) {
            await this.descriptionTextarea.fill(changes.description);
        }

        if (changes.permissionType) {
            await this.permissionTypeSelect.selectOption(changes.permissionType);
        }

        if (changes.permissions?.length) {
            await this.selectPermissions(changes.permissions);
        }

        await this.saveRoleButton.click();
    }

    async deleteRole(name: string): Promise<void> {
        await this.searchRole(name);
        await this.firstDeleteIcon.click();
        await this.agreeButton.click();
    }
}
