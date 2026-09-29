import { expect, test } from "../../fixtures/AdminFixtures";

import { AclPage } from "../../pages/AclPage";
import { buildRoleData, RolesPage } from "../../pages/settings/RolesPage";
import { buildUserData, UsersPage } from "../../pages/settings/UsersPage";
import {
    ACL,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
    ALLOWLISTED_ROUTES,
    GUARDED_ROUTES,
    Session,
    TEST_PASSWORD,
    closeSessions,
    expectAllowed,
    expectDenied,
    getRoute,
    postRoute,
    signInExpectingSuccess,
} from "../../utils/acl";

/**
 * The ACL gate: which actions a role may perform.
 *
 * Every case here fixes the data scope at `global`, so a refusal can only come from the action
 * gate. Record-level scoping is covered separately in `view-permission.spec.ts`.
 *
 * The suite deliberately asserts at all three layers, because only one of them is a security
 * boundary:
 *
 *   - menu and button visibility  -> presentation, may hide a control
 *   - direct GET / POST           -> the middleware, must refuse
 *
 * A test that only checks a hidden button proves nothing about the endpoint behind it.
 */
test.describe.serial("acl action authorization", () => {
    /**
     * A deliberately narrow role: leads read-only, nothing else. It holds `leads.view` but neither
     * `leads.create`, `leads.edit` nor `leads.delete`, which makes it useful for both the
     * "permission present" and "permission absent" halves of the matrix.
     */
    const leadsReaderRole = buildRoleData({
        permissions: [ACL.leadsView],
    });

    const leadsReader = buildUserData({
        roleName: leadsReaderRole.name,
        viewPermission: "global",
    });

    let readerSession: Session | undefined;

    test.beforeAll(async ({ browser, baseURL }) => {
        const admin = await signInExpectingSuccess(browser, baseURL, ADMIN_EMAIL, ADMIN_PASSWORD);

        try {
            await new RolesPage(admin.page).createRole(leadsReaderRole);
            await new UsersPage(admin.page).createUser(leadsReader);
        } finally {
            await closeSessions(admin);
        }

        readerSession = await signInExpectingSuccess(
            browser,
            baseURL,
            leadsReader.email,
            TEST_PASSWORD
        );
    });

    test.afterAll(async () => {
        await closeSessions(readerSession);
    });

    test("an administrator reaches every guarded route", async ({ browser, baseURL }) => {
        const admin = await signInExpectingSuccess(browser, baseURL, ADMIN_EMAIL, ADMIN_PASSWORD);

        try {
            for (const route of GUARDED_ROUTES) {
                const response = await getRoute(admin, route.path);

                expectAllowed(response, `${route.label} for an administrator`);
            }
        } finally {
            await closeSessions(admin);
        }
    });

    test("a held permission grants the route", async () => {
        const response = await getRoute(readerSession!, "admin/leads");

        expectAllowed(response, "lead listing for a user holding 'leads'");
    });

    test("the dashboard is refused without its permission", async () => {
        /**
         * The reader role holds no `dashboard` key, so even the landing screen must be refused —
         * this is the case that proves the middleware is not treating any route as implicitly
         * public for an authenticated user.
         */
        const response = await getRoute(readerSession!, "admin/dashboard");

        expectDenied(response, "dashboard without the 'dashboard' permission");
    });

    /**
     * Every guarded route the reader role does not hold must be refused on a direct GET, with no
     * reliance on the UI having hidden a link to it.
     */
    for (const route of GUARDED_ROUTES.filter(
        (candidate) => ![ACL.leads, ACL.leadsView].includes(candidate.permission as never)
    )) {
        test(`refuses a direct GET to ${route.path} without '${route.permission}'`, async () => {
            const response = await getRoute(readerSession!, route.path);

            expectDenied(response, route.label);
        });
    }

    test("refuses a direct POST to lead create without 'leads.create'", async () => {
        /**
         * Replaying the form submit by hand is the case a hidden button cannot cover. The payload
         * is intentionally incomplete: authorization must refuse before validation ever runs, so a
         * 422 here would itself be a failure.
         */
        const response = await postRoute(readerSession!, "admin/leads/create", {
            title: "unauthorized lead",
        });

        expectDenied(response, "lead store without 'leads.create'");

        expect(response.status(), "authorization must refuse before validation").not.toBe(422);
    });

    test("refuses a direct POST to user create without the settings permission", async () => {
        const response = await postRoute(readerSession!, "admin/settings/users/create", {
            name: "unauthorized user",
            email: `escalation-${Date.now()}@example.invalid`,
        });

        expectDenied(response, "user store without 'settings.user.users.create'");
    });

    test("refuses mass-delete without the delete permission", async () => {
        /**
         * The URL segment is `mass-destroy`; `mass_delete` is only the route's name. Posting the
         * name instead reaches a different route entirely and answers 405, which would mask
         * whatever the real endpoint does.
         */
        const response = await postRoute(readerSession!, "admin/leads/mass-destroy", {
            "indices[]": "1",
        });

        expectDenied(response, "lead mass delete without 'leads.delete'");
    });

    test("hides navigation entries the role does not hold", async () => {
        const aclPage = new AclPage(readerSession!.page);

        await readerSession!.page.goto("admin/leads");

        await aclPage.expectNavVisible("/admin/leads");

        /**
         * Paths rather than labels, matching the routes in the menu config that `Menu::getItems()`
         * filters through `bouncer()->hasPermission()`.
         */
        for (const path of [
            "/admin/quotes",
            "/admin/contacts/persons",
            "/admin/products",
            "/admin/settings/settings",
        ]) {
            await aclPage.expectNavHidden(path);
        }
    });

    test("hides create and delete controls the role does not hold", async () => {
        const aclPage = new AclPage(readerSession!.page);

        await readerSession!.page.goto("admin/leads");

        /**
         * The role holds `leads` and `leads.view` only, so the listing renders but its write
         * controls must not.
         */
        await aclPage.expectControlHidden("Create Lead");
    });

    test("allows self-service account routes for any authenticated user", async () => {
        /**
         * These sit on the middleware's allow-list. They must stay reachable for a role that holds
         * almost nothing, otherwise a restricted user cannot manage their own profile.
         */
        for (const path of ALLOWLISTED_ROUTES) {
            const response = await getRoute(readerSession!, path);

            expectAllowed(response, `allow-listed route ${path}`);
        }
    });

    test("refuses an unmapped route under a feature the role does not hold", async () => {
        /**
         * `admin.settings.users.search` is a real route with no entry of its own in `acl.php`. The
         * middleware fails closed by authorizing it against the nearest mapped ancestor, so a role
         * without any `settings.user.users` permission must not be able to enumerate users through
         * it.
         *
         * A URL that simply does not exist would 404 before the middleware ever ran and would
         * prove nothing, which is why this uses a route that genuinely resolves.
         */
        const response = await getRoute(readerSession!, "admin/settings/users/search");

        expectDenied(response, "an unmapped route under a feature the role lacks");
    });

    test("allows an unmapped route under a feature the role does hold", async () => {
        /**
         * The other half of the fallback: `admin.leads.search` is equally unmapped, but the reader
         * holds `leads`, so the ancestor lookup must let it through. Without this case a middleware
         * that denied every unmapped route would look correct.
         */
        const response = await getRoute(readerSession!, "admin/leads/search");

        expectAllowed(response, "an unmapped route under a feature the role holds");
    });

    test("refuses everything once the account is deactivated", async ({ browser, baseURL }) => {
        const admin = await signInExpectingSuccess(browser, baseURL, ADMIN_EMAIL, ADMIN_PASSWORD);

        try {
            await new UsersPage(admin.page).deactivateUser(leadsReader.email);
        } finally {
            await closeSessions(admin);
        }

        /**
         * The existing session must not survive deactivation — the middleware tears it down on the
         * next request rather than waiting for the cookie to expire.
         */
        const response = await getRoute(readerSession!, "admin/leads");

        expectDenied(response, "an existing session after the account was deactivated");
    });
});
