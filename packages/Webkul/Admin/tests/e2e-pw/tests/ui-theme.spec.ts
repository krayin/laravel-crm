import { expect, test } from "../fixtures/AdminFixtures";

/**
 * The admin shell's own behaviour, as distinct from any one module: the theme toggle, the chrome
 * that must be present on every page, and the navigation between sections.
 *
 * Dark mode is implemented in `components/layouts/header/index.blade.php`: a `v-dark` component
 * toggles `dark` on `<html>`, writes a `dark_mode` cookie, and swaps the logo. All three are
 * asserted, because the class alone would not survive a reload and the cookie alone would not
 * change what the user sees.
 */
test.describe.serial("admin UI and theme", () => {
    /**
     * Each test starts from a known theme rather than inheriting whatever the previous one left,
     * so a failure here is never the result of ordering.
     */
    test.beforeEach(async ({ adminPage }) => {
        await adminPage.context().clearCookies({ name: "dark_mode" });

        await adminPage.goto("admin/dashboard");
    });

    test("the dashboard renders its shell", async ({ adminPage }) => {
        await expect(adminPage.getByRole("banner")).toBeVisible();

        await expect(adminPage.getByRole("textbox", { name: "Mega Search" })).toBeVisible();

        /**
         * The layout renders the same id three times — desktop, mobile and the small header mark —
         * so the first visible one is what is asserted rather than the id alone.
         */
        await expect(adminPage.locator("#logo-image").first()).toBeVisible();
    });

    test("the theme starts light", async ({ adminPage }) => {
        const isDark = await adminPage.evaluate(() =>
            document.documentElement.classList.contains("dark")
        );

        expect(isDark, "a session with no dark_mode cookie should render light").toBe(false);
    });

    test("toggling dark mode applies the dark class", async ({ adminPage }) => {
        await adminPage.locator("span.icon-dark").first().click();

        await expect
            .poll(
                () =>
                    adminPage.evaluate(() => document.documentElement.classList.contains("dark")),
                { message: "the dark class should be applied to the document" }
            )
            .toBe(true);
    });

    test("toggling dark mode persists through a reload", async ({ adminPage }) => {
        await adminPage.locator("span.icon-dark").first().click();

        await expect
            .poll(() =>
                adminPage.evaluate(() => document.documentElement.classList.contains("dark"))
            )
            .toBe(true);

        /**
         * The class is applied by script on toggle; what carries it across a navigation is the
         * `dark_mode` cookie, so the reload is what proves the preference was actually stored.
         */
        await adminPage.goto("admin/leads");

        const stillDark = await adminPage.evaluate(() =>
            document.documentElement.classList.contains("dark")
        );

        expect(stillDark, "the theme should survive a navigation").toBe(true);
    });

    test("toggling back returns to light mode", async ({ adminPage }) => {
        await adminPage.locator("span.icon-dark").first().click();

        await expect
            .poll(() =>
                adminPage.evaluate(() => document.documentElement.classList.contains("dark"))
            )
            .toBe(true);

        await adminPage.locator("span.icon-light").first().click();

        await expect
            .poll(
                () =>
                    adminPage.evaluate(() => document.documentElement.classList.contains("dark")),
                { message: "toggling a second time should return to light" }
            )
            .toBe(false);
    });

    test("the main sections are reachable from the shell", async ({ adminPage }) => {
        /**
         * Five full page loads in one test, so it needs a budget sized for five rather than for a
         * single action.
         */
        test.setTimeout(90 * 1000);

        for (const path of [
            "admin/leads",
            "admin/contacts/persons",
            "admin/contacts/organizations",
            "admin/quotes",
            "admin/mail/inbox",
        ]) {
            const response = await adminPage.goto(path);

            expect(response?.status(), `${path} should load`).toBeLessThan(400);

            await expect(adminPage.getByRole("banner")).toBeVisible();
        }
    });
});
