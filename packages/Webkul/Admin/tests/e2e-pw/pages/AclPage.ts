import { expect, Locator, Page } from "@playwright/test";

import CoreLocators from "../locator/CoreLocators";
import { QuickAddPage } from "./QuickAddPage";

/**
 * Page object for asserting what a signed-in user is allowed to see and reach.
 *
 * The three enforcement layers are kept as separate methods on purpose, because they answer
 * different questions and a suite that conflates them proves less than it looks:
 *
 *   - `expectNavVisible` / `expectNavHidden` — menu filtering (presentation only)
 *   - `expectControlHidden`                  — Blade `bouncer()->hasPermission()` (presentation only)
 *   - the route probes in `utils/acl.ts`     — the middleware (the real security boundary)
 */
export class AclPage extends CoreLocators {
    readonly page: Page;

    readonly unauthorizedHeading: Locator;
    readonly datagridRows: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;

        this.unauthorizedHeading = page.getByText("401 Unauthorized");
        this.datagridRows = page.locator("div.row.grid.grid-cols-\\[2fr_1fr\\]");
    }

    /**
     * A left-navigation entry, matched by the path it links to.
     *
     * The label is not a usable anchor: each sidebar link wraps an icon span and a `<p>`, so its
     * accessible name does not match the menu label cleanly. The href comes straight from the menu
     * config's route, which is also what `Menu::getItems()` filters on — so this asserts against
     * the same thing the code decides.
     */
    navItem(path: string): Locator {
        return this.page.locator(`nav a[href$="${path}"], aside a[href$="${path}"]`);
    }

    async expectNavVisible(path: string): Promise<void> {
        await expect(
            this.navItem(path).first(),
            `a navigation entry for ${path} should appear for a user holding its permission`
        ).toBeVisible();
    }

    async expectNavHidden(path: string): Promise<void> {
        await expect(
            this.navItem(path),
            `a navigation entry for ${path} must not appear without its permission`
        ).toHaveCount(0);
    }

    /**
     * Assert a control is absent from the page. Used for create/edit/delete buttons that Blade
     * wraps in a permission check.
     */
    async expectControlHidden(name: string): Promise<void> {
        await expect(
            this.page.getByRole("button", { name }),
            `"${name}" must not be rendered without its permission`
        ).toHaveCount(0);
    }

    async expectControlVisible(name: string): Promise<void> {
        await expect(
            this.page.getByRole("button", { name }).first(),
            `"${name}" should be rendered for a user holding its permission`
        ).toBeVisible();
    }

    /**
     * Assert the browser landed on the unauthorized page rather than the requested screen.
     */
    async expectUnauthorizedPage(): Promise<void> {
        await expect(this.unauthorizedHeading.first()).toBeVisible();
    }

    /**
     * Visit a listing and return the visible row text, so a scope assertion can check which
     * records the grid actually returned.
     */
    async visibleRowText(path: string): Promise<string> {
        await this.page.goto(path);
        await this.page.waitForLoadState("networkidle");

        return (await this.page.locator("body").innerText()).toLowerCase();
    }

    /**
     * Assert a listing contains a record's identifying text.
     */
    async expectRecordListed(path: string, title: string): Promise<void> {
        const body = await this.visibleRowText(path);

        expect(body, `"${title}" should be visible in ${path} for this data scope`).toContain(
            title.toLowerCase()
        );
    }

    /**
     * Assert a listing does not contain a record's identifying text — the core view-permission
     * assertion.
     */
    async expectRecordNotListed(path: string, title: string): Promise<void> {
        const body = await this.visibleRowText(path);

        expect(body, `"${title}" must be hidden in ${path} for this data scope`).not.toContain(
            title.toLowerCase()
        );
    }

    /**
     * Create a person owned by whoever is signed in.
     *
     * Quick add is used rather than the full create form for one reason: `PersonController::store()`
     * assigns `user_id` from the acting user only on the `quick_add` path. Submitting the standard
     * form without picking a Sales Owner saves the person with a null owner, which makes it
     * invisible to every non-global user — including its own creator — and would make these scope
     * assertions fail for a reason that has nothing to do with `view_permission`.
     */
    async createPerson(name: string, email: string, phone?: string): Promise<void> {
        const quickAdd = new QuickAddPage(this.page);

        await this.page.goto("admin/dashboard").catch(() => undefined);

        await quickAdd.createPerson({
            name,
            email,
            /**
             * Email and phone are both unique across persons, so a shared literal would fail
             * validation with "The value has already been taken." from the second person onward.
             */
            contactNumber: phone ?? AclPage.uniquePhone(),
        });
    }

    /**
     * A phone number unique to this call, within the 10 digits the form accepts.
     */
    static uniquePhone(): string {
        return `9${Date.now().toString().slice(-6)}${Math.floor(Math.random() * 900 + 100)}`;
    }
}
