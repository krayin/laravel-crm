import { test, expect } from "../../fixtures/AdminFixtures";

/**
 * Security regressions around admin authentication, exercised through the browser.
 *
 * Each case here maps to a fix: brute-force throttling that counts only failures, a logout that
 * tears the whole session down, a forgot-password form that does not disclose which addresses are
 * registered, authentication actually being required, and the installer staying shut on an
 * installed application.
 */

const LOGIN_URL = "admin/login";

/**
 * Submit the login form and hand back the POST response, so the status code can be asserted
 * directly rather than inferred from what the page happens to render.
 */
async function submitLogin(page, email: string, password: string) {
    await page.goto(LOGIN_URL);

    await page.locator('input[name="email"]').fill(email);
    await page.locator('input[name="password"]').fill(password);

    const [response] = await Promise.all([
        page.waitForResponse(
            (r) => r.url().includes("/admin/login") && r.request().method() === "POST"
        ),
        page.getByRole("button", { name: "Sign In" }).click(),
    ]);

    return response;
}

test.describe("admin authentication hardening", () => {
    test("throttles repeated failed logins", async ({ page }) => {
        /**
         * An address that cannot match an account, so a real administrator is never locked out by
         * this test and no account state is touched.
         */
        const email = `brute-force-probe-${Date.now()}@example.invalid`;

        for (let attempt = 1; attempt <= 5; attempt++) {
            const response = await submitLogin(page, email, "wrong-password");

            expect(response.status(), `attempt ${attempt} should not be throttled yet`).not.toBe(429);
        }

        const blocked = await submitLogin(page, email, "wrong-password");

        expect(blocked.status()).toBe(429);
    });

    test("does not throttle a correct sign-in", async ({ browser, baseURL }) => {
        /**
         * Counting every attempt rather than only the failures would throttle this suite itself,
         * which signs in for each of its cases from a single address.
         *
         * Each sign-in runs in its own browser context: visiting the login page while already
         * signed in redirects to the dashboard, so reusing one context would leave no form to fill.
         */
        for (let attempt = 1; attempt <= 3; attempt++) {
            const context = await browser.newContext({ baseURL });
            const page = await context.newPage();

            try {
                const response = await submitLogin(page, "admin@example.com", "admin123");

                expect(response.status(), `sign-in ${attempt} should not be throttled`).not.toBe(429);

                await expect(page).toHaveURL(/\/admin\/dashboard/);
            } finally {
                await context.close();
            }
        }
    });

    test("requires authentication for admin pages", async ({ page }) => {
        await page.context().clearCookies();

        await page.goto("admin/dashboard");

        await expect(page).toHaveURL(/\/admin\/login/);
    });

    test("ends the session on logout so the dashboard is no longer reachable", async ({ adminPage }) => {
        await expect(adminPage).toHaveURL(/\/admin\/dashboard/);

        const profileToggle = adminPage.getByRole("banner").getByRole("button").last();

        await profileToggle.click();
        await adminPage.getByRole("link", { name: "Sign Out" }).click();

        await expect(adminPage).toHaveURL(/\/admin\/login/);

        /**
         * Returning to a page that required authentication must not serve it again — the session
         * is invalidated on logout, not merely forgotten.
         */
        await adminPage.goto("admin/dashboard");

        await expect(adminPage).toHaveURL(/\/admin\/login/);
    });

    test("does not disclose whether an email belongs to an account", async ({ page }) => {
        await page.goto("admin/forget-password");

        await page.locator('input[name="email"]').fill(`unknown-${Date.now()}@example.invalid`);
        await page.getByRole("button", { name: "Reset" }).click();

        /**
         * The old behaviour answered "this email does not exist", turning the form into a way of
         * testing which addresses are registered users.
         */
        await expect(page.getByText(/does not exist|not exist|no user|not found/i)).toHaveCount(0);
    });
});

test.describe("installer lockdown", () => {
    /**
     * The installer API overwrites the administrator account and re-runs migrations against live
     * data. On an installed application every one of these must be refused.
     */
    const installerEndpoints = [
        "install/api/admin-config-setup",
        "install/api/run-migration",
        "install/api/run-seeder",
        "install/api/env-file-setup",
    ];

    test("redirects the installer page away on an installed application", async ({ page }) => {
        await page.goto("install");

        await expect(page).not.toHaveURL(/\/install$/);
    });

    for (const endpoint of installerEndpoints) {
        test(`refuses ${endpoint}`, async ({ page }) => {
            const response = await page.request.post(endpoint, {
                failOnStatusCode: false,
                maxRedirects: 0,
            });

            expect(
                response.status(),
                `${endpoint} must not execute on an installed application`
            ).not.toBe(200);
        });
    }
});
