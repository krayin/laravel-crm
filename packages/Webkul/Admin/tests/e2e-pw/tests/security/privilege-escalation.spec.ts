import { expect, test } from "../../fixtures/AdminFixtures";

import { GroupPage } from "../../pages/settings/GroupsPage";
import { buildRoleData, RolesPage } from "../../pages/settings/RolesPage";
import { buildUserData, UsersPage } from "../../pages/settings/UsersPage";
import {
    ACL,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
    Session,
    TEST_PASSWORD,
    closeSessions,
    expectDenied,
    findRecordId,
    postRoute,
    runSuffix,
    signInExpectingSuccess,
} from "../../utils/acl";

/**
 * Privilege escalation: a delegated administrator must never be able to grant, to anyone including
 * themselves, more authority than they already hold.
 *
 * The acting user throughout is a "manager" who can administer roles and users but holds only a
 * narrow slice of the lead permissions. Every case asks the same question from a different angle:
 * can that manager end up with `leads.delete`, a full administrator role, or a wider data scope
 * than the `group` scope they were given?
 */
test.describe.serial("privilege escalation guards", () => {
    const suffix = runSuffix();

    /**
     * The manager can administer roles and users, and holds leads read + edit — but deliberately
     * not `leads.delete`, which becomes the permission every escalation attempt tries to obtain.
     */
    const managerRole = buildRoleData({
        name: `ACL Manager Role ${suffix}`,
        /**
         * Leaf keys only. Listing `leads` here would tick the whole subtree and hand the manager
         * the very `leads.delete` every case below is trying to prove they cannot obtain.
         */
        permissions: [
            ACL.dashboard,
            ACL.leadsView,
            ACL.leadsEdit,
            ACL.settingsRolesCreate,
            ACL.settingsRolesEdit,
            ACL.settingsUsersCreate,
            ACL.settingsUsersEdit,
        ],
    });

    /**
     * The manager is scoped to `group`, which the user form requires a group for
     * (`required_if:view_permission,group`) — so the group has to exist before the account does.
     */
    const managerGroup = {
        name: `ACL Manager Group ${suffix}`,
        description: "Group backing the escalation suite's manager account",
    };

    const manager = buildUserData({
        name: `ACL Manager ${suffix}`,
        roleName: managerRole.name,
        viewPermission: "group",
        groups: [managerGroup.name],
    });

    let managerSession: Session | undefined;

    /**
     * The manager's own role id, needed to build payloads that are *valid* — the controller
     * validates before it authorizes, so an incomplete payload answers 422 and never reaches the
     * escalation guard the test is trying to exercise.
     */
    let managerRoleId: number | null = null;

    /**
     * The manager's own user id, so the self-edit cases target the real account. Resolved from the
     * users datagrid JSON, because the listing renders its edit control without a scrapable id.
     */
    let managerUserId: number | null = null;

    /**
     * The manager's group id, required to keep a `group`-scoped update payload valid.
     */
    let managerGroupId: number | null = null;

    test.beforeAll(async ({ browser, baseURL }) => {
        const admin = await signInExpectingSuccess(browser, baseURL, ADMIN_EMAIL, ADMIN_PASSWORD);

        try {
            const groupPage = new GroupPage(admin.page);

            await groupPage.navigateToGroups();
            await groupPage.createGroup(managerGroup);

            await new RolesPage(admin.page).createRole(managerRole);
            await new UsersPage(admin.page).createUser(manager);

            managerRoleId = await findRecordId(
                admin,
                "admin/settings/roles",
                "name",
                managerRole.name
            );

            managerUserId = await findRecordId(
                admin,
                "admin/settings/users",
                "name",
                manager.name
            );

            managerGroupId = await findRecordId(
                admin,
                "admin/settings/groups",
                "name",
                managerGroup.name
            );
        } finally {
            await closeSessions(admin);
        }

        managerSession = await signInExpectingSuccess(browser, baseURL, manager.email, TEST_PASSWORD);
    });

    test.afterAll(async () => {
        await closeSessions(managerSession);
    });

    test.describe("the access-control tree is pruned to the actor's own permissions", () => {
        test("offers permissions the manager holds", async () => {
            const rolesPage = new RolesPage(managerSession!.page);

            await rolesPage.navigateToCreateRole();
            await rolesPage.permissionTypeSelect.selectOption("custom");

            expect(
                await rolesPage.isPermissionOffered(ACL.leadsEdit),
                "a permission the manager holds should be grantable"
            ).toBeTruthy();
        });

        test("hides permissions the manager does not hold", async () => {
            const rolesPage = new RolesPage(managerSession!.page);

            await rolesPage.navigateToCreateRole();
            await rolesPage.permissionTypeSelect.selectOption("custom");

            /**
             * `Acl::getAuthorizedItems()` prunes the tree, so `leads.delete` must not even be
             * offered. The tree is only the first line — the server-side cases below prove it is
             * not the only one.
             */
            expect(
                await rolesPage.isPermissionOffered(ACL.leadsDelete),
                "a permission the manager lacks must not be offered"
            ).toBeFalsy();
        });
    });

    test("a manager may create a role that is a subset of their own", async () => {
        const rolesPage = new RolesPage(managerSession!.page);

        const subsetRole = buildRoleData({
            name: `ACL Subset Role ${suffix}`,
            permissions: [ACL.leads, ACL.leadsView],
        });

        await rolesPage.createRole(subsetRole);
    });

    test("refuses a role carrying a permission the manager lacks", async () => {
        /**
         * The tree never offers `leads.delete`, so this posts the form directly — the case that
         * matters, because a pruned UI is not an authorization control.
         */
        const response = await postRoute(managerSession!, "admin/settings/roles/create", {
            name: `Escalated Role ${suffix}`,
            description: "attempts to grant a permission the actor lacks",
            permission_type: "custom",
            "permissions[]": ACL.leadsDelete,
        });

        expectDenied(response, "a role granting 'leads.delete' from an actor who lacks it");
    });

    test("refuses promoting a role to full administrator", async () => {
        const response = await postRoute(managerSession!, "admin/settings/roles/create", {
            name: `Escalated Admin Role ${suffix}`,
            description: "attempts to create a full administrator role",
            permission_type: "all",
        });

        expectDenied(response, "a full administrator role created by a non-administrator");
    });

    test("refuses editing a role broader than the manager's own", async () => {
        const rolesPage = new RolesPage(managerSession!.page);

        /**
         * The seeded Administrator role holds `permission_type = all`, so it is strictly broader
         * than the manager's. Editing it would be the shortest path to full control.
         */
        await rolesPage.searchRole("Administrator");

        /**
         * The update route is a PUT, so the verb is spoofed through `_method` exactly as the admin
         * panel's own forms do — posting without it answers 405 and never reaches the guard.
         */
        const response = await postRoute(managerSession!, "admin/settings/roles/edit/1", {
            _method: "put",
            name: "Administrator",
            description: "tampered by a non-administrator",
            permission_type: "all",
        });

        expectDenied(response, "editing a role broader than the actor's own");
    });

    test("refuses assigning the administrator role to a new user", async () => {
        /**
         * `individual` scope keeps the payload valid without a group (the form requires one only
         * for `group`), so the only thing left for the controller to object to is the role.
         */
        const response = await postRoute(managerSession!, "admin/settings/users/create", {
            name: `Escalated User ${suffix}`,
            email: `escalated.${suffix}@example.test`,
            password: TEST_PASSWORD,
            confirm_password: TEST_PASSWORD,
            role_id: "1",
            view_permission: "individual",
        });

        expectDenied(response, "assigning the administrator role by a non-administrator");
    });

    test("refuses granting a data scope wider than the manager's own", async () => {
        /**
         * The manager's scope is `group`. Creating a `global` user would hand that account
         * visibility the manager does not have, and is refused by the scope ceiling.
         */
        const response = await postRoute(managerSession!, "admin/settings/users/create", {
            name: `Wider Scope User ${suffix}`,
            email: `wider.${suffix}@example.test`,
            password: TEST_PASSWORD,
            confirm_password: TEST_PASSWORD,
            role_id: String(managerRoleId ?? ""),
            view_permission: "global",
        });

        expectDenied(response, "granting 'global' scope from a 'group'-scoped actor");
    });

    test("allows granting a scope at or below the manager's own", async () => {
        const usersPage = new UsersPage(managerSession!.page);

        const narrowerUser = buildUserData({
            name: `Narrower Scope User ${suffix}`,
            roleName: managerRole.name,
            viewPermission: "individual",
        });

        /**
         * The ceiling narrows, it does not forbid delegation outright — a manager must still be
         * able to create accounts at or below their own authority, or role administration becomes
         * unusable.
         */
        await usersPage.createUser(narrowerUser);
    });

    test("refuses changing the manager's own role", async () => {
        expect(managerUserId, "the manager's own user id should resolve").not.toBeNull();

        const response = await postRoute(
            managerSession!,
            `admin/settings/users/edit/${managerUserId}`,
            {
                _method: "put",
                name: manager.name,
                email: manager.email,
                role_id: "1",
                view_permission: "group",
                "groups[]": String(managerGroupId ?? ""),
            }
        );

        expectDenied(response, "a user changing their own role");
    });

    test("refuses widening the manager's own data scope", async () => {
        expect(managerUserId, "the manager's own user id should resolve").not.toBeNull();

        const response = await postRoute(
            managerSession!,
            `admin/settings/users/edit/${managerUserId}`,
            {
                _method: "put",
                name: manager.name,
                email: manager.email,
                role_id: String(managerRoleId ?? ""),
                view_permission: "global",
            }
        );

        expectDenied(response, "a user widening their own data scope");
    });

    test("refuses editing the role assigned to the manager's own account", async () => {
        const rolesPage = new RolesPage(managerSession!.page);

        await rolesPage.openRoleForEdit(managerRole.name);

        /**
         * Nobody, administrators included, may edit the role attached to their own account — the
         * one guard that closes the loop on every other ceiling in this suite.
         */
        await expect(rolesPage.currentRoleEditError.first()).toBeVisible();
    });

    test("the manager never gains the permission every attempt was after", async () => {
        /**
         * The closing assertion: after all of the above, a direct mass-delete must still be
         * refused. If any earlier guard leaked, this is where it shows.
         */
        const response = await postRoute(managerSession!, "admin/leads/mass-destroy", {
            "indices[]": "1",
        });

        expectDenied(response, "lead mass delete after every escalation attempt");
    });
});
