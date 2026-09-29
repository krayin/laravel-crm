import { expect, test } from "../../fixtures/AdminFixtures";
import { buildRoleData, RolesPage } from "../../pages/settings/RolesPage";
import { ACL } from "../../utils/acl";

/**
 * Role management CRUD, plus the access-control tree behaviour the ACL suites depend on.
 *
 * These run as an administrator, so nothing here is an authorization assertion — the escalation
 * rules are covered in `tests/security/privilege-escalation.spec.ts`.
 */
test.describe.serial("role management", () => {
    const role = buildRoleData({
        permissions: [ACL.leads, ACL.leadsView],
    });

    test("creates a custom role with selected permissions", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        await rolesPage.createRole(role);
    });

    test("creates a full administrator role", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        const adminRole = buildRoleData({ permissionType: "all" });

        await rolesPage.createRole(adminRole);

        /**
         * Selecting `all` must hide the permission tree entirely — the checkboxes are only
         * meaningful for a custom role.
         */
        await rolesPage.navigateToCreateRole();
        await rolesPage.permissionTypeSelect.selectOption("all");

        await expect(rolesPage.permissionLabel(ACL.leads)).toHaveCount(0);
    });

    test("requires a name and a description", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        await rolesPage.navigateToCreateRole();
        await rolesPage.saveRoleButton.click();

        /**
         * The form must not submit — staying on the create URL is what proves validation fired.
         */
        await expect(adminPage).toHaveURL(/\/admin\/settings\/roles\/create/);
    });

    test("requires at least one permission for a custom role", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        const emptyRole = buildRoleData({ permissions: [] });

        await rolesPage.submitRoleForm(emptyRole);

        await expect(rolesPage.createSuccessMessage).toHaveCount(0);
    });

    test("selecting a child permission also selects its parent", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        await rolesPage.navigateToCreateRole();
        await rolesPage.permissionTypeSelect.selectOption("custom");
        await rolesPage.selectPermission(ACL.leadsDelete);

        /**
         * The tree's default selection type is hierarchical, so a role can never hold
         * `leads.delete` without also holding `leads`. Middleware route resolution relies on this.
         */
        await expect(rolesPage.permissionCheckbox(ACL.leads)).toBeChecked();
    });

    test("updates a role", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        await rolesPage.updateRole(role.name, {
            description: "Updated by the authorization suite",
            permissions: [ACL.leadsEdit],
        });

        await expect(rolesPage.updateSuccessMessage.first()).toBeVisible();
    });

    test("refuses to delete a role that is assigned to a user", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        /**
         * The seeded Administrator role is assigned to the signed-in account, so it is covered by
         * both the "in use" guard and the "your own role" guard.
         */
        await rolesPage.deleteRole("Administrator");

        await expect(rolesPage.deleteSuccessMessage).toHaveCount(0);
    });

    test("refuses to edit the role assigned to the current account", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        await rolesPage.openRoleForEdit("Administrator");

        /**
         * The guard redirects back to the listing with a flash rather than opening the form, so a
         * user can never widen their own role.
         */
        await expect(rolesPage.currentRoleEditError.first()).toBeVisible();
    });

    test("deletes an unused role", async ({ adminPage }) => {
        const rolesPage = new RolesPage(adminPage);

        await rolesPage.deleteRole(role.name);

        await expect(rolesPage.deleteSuccessMessage.first()).toBeVisible();
    });
});
