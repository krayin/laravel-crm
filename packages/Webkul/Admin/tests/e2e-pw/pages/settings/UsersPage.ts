import { expect, Locator, Page } from "@playwright/test";

import { SettingsPage } from "../SettingsPage";
import { generateEmail, generateFullName } from "../../utils/faker";
import { TEST_PASSWORD, ViewPermission, runSuffix } from "../../utils/acl";

export type UserData = {
    name: string;
    email: string;
    password: string;
    roleName: string;
    viewPermission: ViewPermission;

    /**
     * Group names to associate. Required by the form when `viewPermission` is `group`.
     */
    groups?: string[];

    status?: boolean;
};

/**
 * Build user data with a unique email, so a re-run never trips the unique constraint.
 */
export function buildUserData(overrides: Partial<UserData> = {}): UserData {
    const suffix = runSuffix();

    return {
        name: `${generateFullName()} ${suffix}`,
        email: `acl.${suffix}.${generateEmail()}`,
        password: TEST_PASSWORD,
        roleName: "Administrator",
        viewPermission: "global",
        groups: [],
        status: true,
        ...overrides,
    };
}

export class UsersPage extends SettingsPage {
    readonly page: Page;

    readonly createUserButton: Locator;
    readonly saveUserButton: Locator;

    readonly nameInput: Locator;
    readonly emailInput: Locator;
    readonly passwordInput: Locator;
    readonly confirmPasswordInput: Locator;
    readonly roleSelect: Locator;
    readonly viewPermissionSelect: Locator;
    readonly groupSelect: Locator;
    readonly statusCheckbox: Locator;

    readonly createSuccessMessage: Locator;
    readonly updateSuccessMessage: Locator;
    readonly deleteSuccessMessage: Locator;

    readonly unauthorizedMessage: Locator;
    readonly roleExceedsOwnMessage: Locator;
    readonly scopeExceedsOwnMessage: Locator;
    readonly ownPrivilegesMessage: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;

        this.createUserButton = page.getByRole("button", { name: "Create User" });
        this.saveUserButton = page.getByRole("button", { name: "Save User" });

        this.nameInput = page.locator('input[name="name"]');
        this.emailInput = page.locator('input[name="email"]');
        this.passwordInput = page.locator('input[name="password"]');
        this.confirmPasswordInput = page.locator('input[name="confirm_password"]');
        this.roleSelect = page.locator('select[name="role_id"]');
        this.viewPermissionSelect = page.locator('select[name="view_permission"]');
        this.groupSelect = page.locator('select[name="groups[]"]');
        /**
         * The form ships a hidden `status` input carrying `0` alongside the real checkbox, so the
         * name alone is ambiguous — the id is what identifies the control the user toggles.
         */
        this.statusCheckbox = page.locator('input#status[type="checkbox"]');

        this.createSuccessMessage = page.getByText("User created successfully.");
        this.updateSuccessMessage = page.getByText("User updated successfully.");
        this.deleteSuccessMessage = page.getByText("User deleted successfully.");

        this.unauthorizedMessage = page.getByText("This action is unauthorized.");
        this.roleExceedsOwnMessage = page.getByText(
            "You can only assign a role whose permissions are the same as or fewer than your own."
        );
        this.scopeExceedsOwnMessage = page.getByText("You cannot grant a data scope wider than your own.");
        this.ownPrivilegesMessage = page.getByText("You cannot change your own role or data scope.");
    }

    async openCreateModal(): Promise<void> {
        await this.navigateToUsers();
        await this.createUserButton.click();
        await expect(this.nameInput).toBeVisible();
    }

    async fillUserForm(user: UserData): Promise<void> {
        await this.nameInput.fill(user.name);
        await this.emailInput.fill(user.email);
        await this.passwordInput.fill(user.password);
        await this.confirmPasswordInput.fill(user.password);

        await this.roleSelect.selectOption({ label: user.roleName });
        await this.viewPermissionSelect.selectOption(user.viewPermission);

        /**
         * The group field is only rendered for the `group` and `individual` scopes, and is
         * mandatory for `group`.
         */
        if (user.groups?.length) {
            await this.groupSelect.selectOption(user.groups.map((label) => ({ label })));
        }

        /**
         * Status is set explicitly rather than only when disabling: the create modal renders the
         * toggle unchecked, so a user left at the default is created inactive and cannot sign in
         * at all — which would fail every downstream authorization assertion for the wrong reason.
         */
        await this.setStatus(user.status ?? true);
    }

    /**
     * Toggle the status control to a known state rather than blind-clicking, so a form that
     * already defaults to the wanted value is not flipped away from it.
     */
    async setStatus(active: boolean): Promise<void> {
        /**
         * The checkbox is `sr-only`, so it is toggled through its own element with `force` rather
         * than by a normal click, which Playwright would refuse as targeting a hidden element.
         */
        if (active) {
            await this.statusCheckbox.check({ force: true });
        } else {
            await this.statusCheckbox.uncheck({ force: true });
        }
    }

    /**
     * Create a user through the modal and confirm the success flash.
     */
    async createUser(user: UserData): Promise<void> {
        await this.openCreateModal();
        await this.fillUserForm(user);
        await this.saveUserButton.click();

        await expect(this.createSuccessMessage.first()).toBeVisible();
    }

    /**
     * Submit the modal without asserting success, for cases where a refusal is the expectation.
     */
    async submitUserForm(user: UserData): Promise<void> {
        await this.openCreateModal();
        await this.fillUserForm(user);
        await this.saveUserButton.click();
    }

    async searchUser(term: string): Promise<void> {
        await this.navigateToUsers();
        await this.searchInputExact.fill(term);
        await this.page.keyboard.press("Enter");
        await this.page.waitForLoadState("networkidle");
    }

    async openUserForEdit(term: string): Promise<void> {
        await this.searchUser(term);
        await this.firstEditIcon.click();
        await expect(this.nameInput).toBeVisible();
    }

    /**
     * Change an existing user's role and/or scope through the edit modal.
     */
    async updateUser(term: string, changes: Partial<UserData>): Promise<void> {
        await this.openUserForEdit(term);

        if (changes.name) {
            await this.nameInput.fill(changes.name);
        }

        if (changes.roleName) {
            await this.roleSelect.selectOption({ label: changes.roleName });
        }

        if (changes.viewPermission) {
            await this.viewPermissionSelect.selectOption(changes.viewPermission);
        }

        if (changes.groups?.length) {
            await this.groupSelect.selectOption(changes.groups.map((label) => ({ label })));
        }

        if (changes.status !== undefined) {
            await this.setStatus(changes.status);
        }

        await this.saveUserButton.click();
    }

    /**
     * Deactivate a user, which the middleware treats as a session-level revocation on that user's
     * next request.
     */
    async deactivateUser(term: string): Promise<void> {
        await this.updateUser(term, { status: false });
        await expect(this.updateSuccessMessage.first()).toBeVisible();
    }

    async deleteUser(term: string): Promise<void> {
        await this.searchUser(term);
        await this.firstDeleteIcon.click();
        await this.agreeButton.click();
    }

    /**
     * Role names offered in the assignment dropdown. A non-administrator must never be offered a
     * role broader than their own.
     */
    async offeredRoleNames(): Promise<string[]> {
        return this.roleSelect.locator("option").allInnerTexts();
    }

    /**
     * Scope values offered in the view-permission dropdown.
     */
    async offeredScopes(): Promise<string[]> {
        return this.viewPermissionSelect.locator("option").evaluateAll((options) =>
            options.map((option) => (option as HTMLOptionElement).value)
        );
    }
}
