import { expect, test } from "../fixtures/AdminFixtures";

import { ADMIN_EMAIL, ADMIN_PASSWORD, runSuffix } from "../utils/acl";

/**
 * The signed-in user's own account screen (`/admin/account`).
 *
 * Two things are worth pinning here. The ordinary one is that a profile edit works and is gated on
 * the current password. The security one is that this form is self-service — the user editing it is
 * the subject — so it must not be a route to privileges: `AccountController::update` reads a fixed
 * list of fields and refuses outright when `role_id` or `view_permission` is submitted, which is
 * what stops a low-privileged user promoting themselves by posting an extra field.
 *
 * The admin's own name is restored after each test, so the suite can run repeatedly and leaves the
 * account as it found it.
 */
test.describe.serial("account management", () => {
    const suffix = runSuffix();

    const originalName = "Example";

    async function csrf(adminPage: any): Promise<string> {
        const cookie = (await adminPage.context().cookies()).find(
            (candidate: any) => candidate.name === "XSRF-TOKEN"
        );

        return cookie ? decodeURIComponent(cookie.value) : "";
    }

    test("the account screen loads with the signed-in user's details", async ({ adminPage }) => {
        await adminPage.goto("admin/account");

        await expect(adminPage.locator('input[name="name"]')).toBeVisible();

        await expect(adminPage.locator('input[name="email"]')).toHaveValue(ADMIN_EMAIL);

        /**
         * The current password field is what gates every change on this screen, so its presence is
         * part of the contract rather than incidental.
         */
        await expect(adminPage.locator('input[name="current_password"]')).toBeVisible();
    });

    test("a profile edit is saved when the current password is correct", async ({ adminPage }) => {
        await adminPage.goto("admin/account");

        const updatedName = `Example ${suffix}`;

        await adminPage.locator('input[name="name"]').fill(updatedName);
        await adminPage.locator('input[name="current_password"]').fill(ADMIN_PASSWORD);

        await adminPage.getByRole("button", { name: "Save Account" }).click();

        await expect(adminPage.getByText("Account updated successfully").first()).toBeVisible();

        await adminPage.goto("admin/account");

        await expect(adminPage.locator('input[name="name"]')).toHaveValue(updatedName);
    });

    test("an edit is refused when the current password is wrong", async ({ adminPage }) => {
        await adminPage.goto("admin/account");

        await adminPage.locator('input[name="name"]').fill(`Should Not Save ${suffix}`);
        await adminPage.locator('input[name="current_password"]').fill("definitely-not-the-password");

        await adminPage.getByRole("button", { name: "Save Account" }).click();

        await expect(
            adminPage.getByText("The current password you entered is incorrect.").first()
        ).toBeVisible();

        await adminPage.goto("admin/account");

        await expect(
            adminPage.locator('input[name="name"]'),
            "a rejected edit must not have been written"
        ).not.toHaveValue(`Should Not Save ${suffix}`);
    });

    /**
     * NOT COVERED HERE: the privilege-escalation guard in `AccountController::update`, which
     * refuses the request when `role_id` or `view_permission` is submitted.
     *
     * The guard was confirmed to work while writing this suite — posting those fields leaves
     * `users.name`, `role_id` and `view_permission` unchanged in the database — but driving it from
     * the browser proved unreliable to assert: the guard answers with `redirect()->back()`, so an
     * XHR without a Referer errors rather than being refused, and after a real form submit the
     * rendered input still carries the client-side value, which says nothing about what was stored.
     *
     * It is covered properly by `tests/security/privilege-escalation.spec.ts`, which drives the
     * same guard with sessions built for the purpose. Leaving a flaky duplicate here would be worse
     * than leaving it to that suite.
     */

    test("@security the account update requires a csrf token", async ({ adminPage }) => {
        await adminPage.goto("admin/account");

        const response = await adminPage.request.fetch("admin/account/update", {
            method: "PUT",
            failOnStatusCode: false,
            maxRedirects: 0,
            headers: { "X-Requested-With": "XMLHttpRequest" },
            form: {
                name: `No token ${suffix}`,
                email: ADMIN_EMAIL,
                current_password: ADMIN_PASSWORD,
            },
        });

        expect(
            response.status(),
            "a state-changing request without a token must be refused"
        ).toBe(419);
    });

    test.afterAll(async ({ browser, baseURL }) => {
        /**
         * Put the admin's name back, so a repeated run starts from the same state it did the first
         * time and the account is not left carrying a test's value.
         */
        const context = await browser.newContext({ baseURL, storageState: undefined });

        try {
            const page = await context.newPage();

            await page.goto("admin/login");
            await page.locator('input[name="email"]').fill(ADMIN_EMAIL);
            await page.locator('input[name="password"]').fill(ADMIN_PASSWORD);
            await page.getByRole("button", { name: "Sign In" }).click();
            await page.waitForURL(/admin\/(dashboard|leads)/);

            await page.goto("admin/account");
            await page.locator('input[name="name"]').fill(originalName);
            await page.locator('input[name="current_password"]').fill(ADMIN_PASSWORD);
            await page.getByRole("button", { name: "Save Account" }).click();
        } catch {
            // Restoring the name is housekeeping; failing it must not fail the suite.
        } finally {
            try {
                await context.close();
            } catch {
                // Teardown noise, not a test failure.
            }
        }
    });
});
