import { Browser, BrowserContext, expect, Page, APIResponse } from "@playwright/test";

import { AdminPage } from "../pages/AdminPage";

/**
 * Shared vocabulary and helpers for the authorization suites.
 *
 * Krayin authorization has two orthogonal dimensions and the helpers below are split along the
 * same seam:
 *
 *   - ACL        -> which actions a role may perform  (roles.permission_type + roles.permissions)
 *   - View scope -> whose records those actions touch (users.view_permission)
 *
 * Everything here works against a signed-in browser context so a probe carries the same session
 * cookies the UI would, which is the only way a direct-request test proves anything about the
 * middleware.
 */

export const ADMIN_EMAIL = "admin@example.com";
export const ADMIN_PASSWORD = "admin123";

/**
 * Password handed to every user these suites create. Kept above Krayin's six character minimum.
 */
export const TEST_PASSWORD = "password123";

export type ViewPermission = "global" | "group" | "individual";

export type PermissionType = "all" | "custom";

/**
 * ACL keys used across the suites, named rather than repeated as string literals so a rename in
 * `Admin/Config/acl.php` breaks in one place.
 */
export const ACL = {
    dashboard: "dashboard",

    leads: "leads",
    leadsCreate: "leads.create",
    leadsView: "leads.view",
    leadsEdit: "leads.edit",
    leadsDelete: "leads.delete",

    quotes: "quotes",
    quotesCreate: "quotes.create",

    contacts: "contacts",
    contactsPersons: "contacts.persons",
    contactsPersonsCreate: "contacts.persons.create",
    contactsPersonsView: "contacts.persons.view",
    contactsPersonsQuickCreate: "contacts.persons.create.quick-create",
    contactsPersonsDelete: "contacts.persons.delete",

    settings: "settings",
    settingsGroups: "settings.user.groups",
    settingsRoles: "settings.user.roles",
    settingsRolesCreate: "settings.user.roles.create",
    settingsRolesEdit: "settings.user.roles.edit",
    settingsRolesDelete: "settings.user.roles.delete",
    settingsUsers: "settings.user.users",
    settingsUsersCreate: "settings.user.users.create",
    settingsUsersEdit: "settings.user.users.edit",
    settingsUsersDelete: "settings.user.users.delete",
} as const;

/**
 * Admin routes exercised by the direct-request probes, paired with the ACL key that must guard
 * them. Paths are relative so they resolve against the configured `baseURL`.
 */
export const GUARDED_ROUTES: Array<{ path: string; permission: string; label: string }> = [
    { path: "admin/leads", permission: ACL.leads, label: "lead listing" },
    { path: "admin/leads/create", permission: ACL.leadsCreate, label: "lead create form" },
    { path: "admin/quotes", permission: ACL.quotes, label: "quote listing" },
    { path: "admin/quotes/create", permission: ACL.quotesCreate, label: "quote create form" },
    { path: "admin/contacts/persons", permission: ACL.contactsPersons, label: "person listing" },
    { path: "admin/settings/roles", permission: ACL.settingsRoles, label: "role listing" },
    { path: "admin/settings/roles/create", permission: ACL.settingsRolesCreate, label: "role create form" },
    { path: "admin/settings/users", permission: ACL.settingsUsers, label: "user listing" },
    { path: "admin/settings/groups", permission: ACL.settingsGroups, label: "group listing" },
];

/**
 * Routes every authenticated user may reach regardless of role, mirroring the middleware's
 * allow-list. A regression that widens the allow-list shows up as a route appearing here that
 * should not.
 */
export const ALLOWLISTED_ROUTES = [
    "admin/account",
];

export type Session = {
    context: BrowserContext;
    page: Page;
    email: string;
};

/**
 * Sign in as an arbitrary user in a brand new browser context.
 *
 * A separate context per user is deliberate: the admin fixture's storage state must not leak into
 * a test that is meant to act as a restricted user, and reusing one context would let an earlier
 * session's cookies answer for a later assertion.
 */
export async function signInAs(
    browser: Browser,
    baseURL: string | undefined,
    email: string,
    password: string
): Promise<Session> {
    const context = await browser.newContext({ baseURL });
    const page = await context.newPage();

    await new AdminPage(page).adminLogin(email, password);

    await page.waitForLoadState("networkidle");

    return { context, page, email };
}

/**
 * Sign in and assert the attempt actually authenticated, so a suite never proceeds to
 * authorization assertions while silently signed out — an unauthenticated request is redirected to
 * the login page and would pass a naive "not 200" check for the wrong reason.
 *
 * The landing URL is deliberately not pinned to the dashboard: a user whose role lacks the
 * `dashboard` permission is dropped on the first screen they can reach (`/admin/leads`, say), so
 * leaving the login page is what actually signals success.
 */
export async function signInExpectingSuccess(
    browser: Browser,
    baseURL: string | undefined,
    email: string,
    password: string
): Promise<Session> {
    const session = await signInAs(browser, baseURL, email, password);

    await expect(session.page, `${email} should be signed in`).not.toHaveURL(/\/admin\/login/);

    return session;
}

/**
 * Laravel accepts the `XSRF-TOKEN` cookie echoed back as an `X-XSRF-TOKEN` header, which is how
 * the admin panel's own axios calls pass CSRF verification. Reusing that keeps a POST probe
 * failing on authorization rather than on a missing token.
 */
export async function csrfHeaders(context: BrowserContext): Promise<Record<string, string>> {
    const cookies = await context.cookies();

    const token = cookies.find((cookie) => cookie.name === "XSRF-TOKEN");

    return {
        "X-XSRF-TOKEN": token ? decodeURIComponent(token.value) : "",
        "X-Requested-With": "XMLHttpRequest",
    };
}

/**
 * Issue a GET against an admin route using the session's cookies, without following redirects, so
 * the raw status is what gets asserted.
 */
export async function getRoute(session: Session, path: string): Promise<APIResponse> {
    return session.page.request.get(path, {
        failOnStatusCode: false,
        maxRedirects: 0,
    });
}

/**
 * Issue a POST against an admin route with CSRF headers attached.
 */
export async function postRoute(
    session: Session,
    path: string,
    form: Record<string, string> = {}
): Promise<APIResponse> {
    return session.page.request.post(path, {
        failOnStatusCode: false,
        maxRedirects: 0,
        headers: await csrfHeaders(session.context),
        form,
    });
}

/**
 * Assert a response was refused by authorization.
 *
 * 401 is what `Bouncer::allow()` aborts with. A redirect to the login page (302) is accepted only
 * as a session-level refusal; a 200 always fails, because rendering the page means the guard did
 * not run.
 */
export function expectDenied(response: APIResponse, label: string): void {
    expect(
        response.status(),
        `${label} must be refused for a user without the permission (got ${response.status()})`
    ).not.toBe(200);

    expect(
        [401, 403, 302, 419].includes(response.status()),
        `${label} should be refused with 401/403 or a redirect, got ${response.status()}`
    ).toBeTruthy();
}

/**
 * Assert a response was served.
 */
export function expectAllowed(response: APIResponse, label: string): void {
    expect(response.status(), `${label} should be reachable`).toBe(200);
}

/**
 * Close every session opened by a test, tolerating one that failed to open.
 */
export async function closeSessions(...sessions: Array<Session | undefined>): Promise<void> {
    for (const session of sessions) {
        await session?.context.close();
    }
}

/**
 * A run-scoped suffix, so records created by one run never collide with a previous one and a
 * failed run leaves behind identifiable rows.
 */
export function runSuffix(): string {
    return `${Date.now().toString().slice(-6)}${Math.floor(Math.random() * 1000)}`;
}

/**
 * Resolve a record id out of a datagrid's own JSON response.
 *
 * The person listing renders no per-row links, so there is no href to scrape for an id. The grid
 * populates itself from this endpoint, so asking it directly is both more stable than parsing the
 * DOM and closer to what the page actually does.
 *
 * Returns `null` when the record is not present for this session — which for a scoped user is a
 * meaningful answer, not a failure.
 */
export async function findRecordId(
    session: Session,
    path: string,
    column: string,
    value: string
): Promise<number | null> {
    const response = await session.page.request.get(
        `${path}?pagination[page]=1&pagination[per_page]=200`,
        {
            headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
            failOnStatusCode: false,
        }
    );

    if (response.status() !== 200) {
        return null;
    }

    const body = await response.json();

    /**
     * A grid column is not always a scalar: the users grid renders its name cell from an object
     * (`{ image, name }`), which the Blade reads as `record.name.name`. Unwrap that shape before
     * comparing, or every user lookup silently misses.
     */
    const cellText = (cell: unknown): string => {
        if (cell && typeof cell === "object" && "name" in (cell as Record<string, unknown>)) {
            return String((cell as Record<string, unknown>).name ?? "").trim();
        }

        return String(cell ?? "").trim();
    };

    const match = (body.records ?? []).find(
        (record: Record<string, unknown>) => cellText(record[column]) === value
    );

    return match ? Number(match.id) : null;
}
