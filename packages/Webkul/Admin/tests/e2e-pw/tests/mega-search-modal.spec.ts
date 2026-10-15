import { expect, test } from "../fixtures/AdminFixtures";

/**
 * The mega search can also be opened as a modal with Ctrl/⌘ K, so it is reachable from any page
 * without first finding the header field, and the shortcut is advertised in the field itself.
 *
 * The modal reuses the component's own state — the term, the tabs, the per-section counts — rather
 * than reimplementing the search, so these tests check that the same data reaches both.
 */
test.describe("mega search modal", () => {
    /**
     * The modal's own input.
     *
     * Found inside the modal rather than by its position among the fields sharing this placeholder:
     * the header carries a desktop field and a hidden mobile one, so counting from the start landed
     * on whichever happened to come first in the markup.
     */
    const modalInput = (adminPage: any) =>
        adminPage.locator('[role="dialog"], .fixed').locator('input[placeholder="Mega Search"]').first();

    const hint = (adminPage: any) => adminPage.locator("kbd").first();

    /**
     * A term guaranteed to match, and the record that makes it so.
     *
     * The tests previously searched for a word that happened to exist in a developer's own database
     * — "need" — which matched nothing on a freshly installed one, so the modal listed no results
     * and the assertion had nothing to count. Each run now creates what it searches for.
     */
    const term = "megasearchseed";

    /**
     * Post a form from inside the page, so the browser attaches the session's current csrf token.
     *
     * Reading the token through the api context worked only while it was fresh: the saved sign-in
     * state carries one from an earlier session, and a stale token is refused — leaving the record
     * uncreated and the assertions searching for something that was never there.
     */
    async function postForm(adminPage: any, path: string, fields: Record<string, string>) {
        return adminPage.evaluate(
            async ([url, values]: [string, Record<string, string>]) => {
                const body = new FormData();

                Object.entries(values).forEach(([key, value]) => body.append(key, value));

                const token = decodeURIComponent(
                    document.cookie
                        .split("; ")
                        .find((entry) => entry.startsWith("XSRF-TOKEN="))
                        ?.split("=")[1] ?? ""
                );

                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-XSRF-TOKEN": token,
                    },
                    credentials: "same-origin",
                    body,
                });

                return response.status;
            },
            [path, fields]
        );
    }

    async function seedSearchableRecord(adminPage: any) {
        const unique = Date.now();

        await postForm(adminPage, "/admin/contacts/persons/create", {
                name: `${term} person ${unique}`,
                "emails[0][value]": `${term}${unique}@example.invalid`,
                "emails[0][label]": "work",
                entity_type: "persons",
            });

        /**
         * A lead as well as a person: the modal opens on the leads tab, and it is that tab's rows
         * the results assertion counts, so a run seeding only a person found nothing to list.
         */
        await postForm(adminPage, "/admin/leads/create", {
                title: `${term} lead ${unique}`,
                description: "seeded for the mega search modal",
                lead_value: "1000",
                "person[name]": `${term} contact ${unique}`,
                "person[emails][0][value]": `${term}lead${unique}@example.invalid`,
                "person[emails][0][label]": "work",
            });
    }

    test.beforeEach(async ({ adminPage }) => {
        await adminPage.goto("admin/dashboard");

        await seedSearchableRecord(adminPage);

        await adminPage.goto("admin/dashboard");
    });

    test("the shortcut is advertised in the search field", async ({ adminPage }) => {
        await expect(hint(adminPage)).toBeVisible();

        await expect(hint(adminPage)).toHaveText("Ctrl + K");
    });

    test("ctrl+k opens the search modal", async ({ adminPage }) => {
        await expect(modalInput(adminPage)).toBeHidden();

        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();
    });

    test("the modal takes focus so the shortcut leads straight into typing", async ({
        adminPage,
    }) => {
        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeFocused();
    });

    test("escape closes the modal", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();

        await adminPage.keyboard.press("Escape");

        await expect(modalInput(adminPage)).toBeHidden();
    });

    test("clicking the backdrop closes the modal", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();

        /**
         * The backdrop is the modal's outermost element; clicking its own corner is a click outside
         * the panel.
         */
        await adminPage.mouse.click(10, 10);

        await expect(modalInput(adminPage)).toBeHidden();
    });

    test("searching in the modal shows results and per-section counts", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        await modalInput(adminPage).fill(term);

        await expect
            .poll(() => adminPage.locator("[data-modal-section-count]").count(), {
                message: "the modal should carry the same section counts as the dropdown",
            })
            .toBeGreaterThan(0);

        await expect
            .poll(() => adminPage.locator("[data-modal-result]").count(), {
                message: "the modal should list the records it found",
            })
            .toBeGreaterThan(0);
    });

    test("a term with no matches reports that rather than showing nothing", async ({
        adminPage,
    }) => {
        await adminPage.keyboard.press("Control+k");

        await modalInput(adminPage).fill(`zz-no-such-record-${Date.now()}`);

        await expect(
            adminPage.getByText("No records match your search.").first()
        ).toBeVisible();
    });

    /**
     * The admin layout stacks the sidebar at `z-[10002]` and the header at `z-[10001]`, so a modal
     * below those rendered *behind* the sidebar: the backdrop stopped at the sidebar's edge and the
     * menu still looked clickable while the search was open.
     */
    /**
     * The component renders inside `<header>`, which sets its own z-index and therefore its own
     * stacking context. A modal left inside it has its z-index resolved against the header's
     * siblings only, so it can never rise above the sidebar however high the value — which is why
     * the sidebar stayed sharp and clickable over the blurred page. The modal is teleported to the
     * body to escape that context.
     */
    test("the modal is rendered outside the header so it can stack above the sidebar", async ({
        adminPage,
    }) => {
        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();

        /**
         * The modal's own field is the one appearing while it is open, so it is identified by not
         * belonging to the header at all. The header carries a desktop field and a hidden mobile
         * one that share this placeholder, so picking by position or by visibility found those.
         */
        const insideHeader = await adminPage.evaluate(() => {
            const inputs = Array.from(
                document.querySelectorAll('input[placeholder="Mega Search"]')
            );

            /**
             * Every field is checked: the assertion is that at least one of them — the modal's —
             * is rendered outside the header.
             */
            return ! inputs.some((candidate) => ! candidate.closest("header"));
        });

        expect(
            insideHeader,
            "the modal must not render inside the header's stacking context"
        ).toBe(false);
    });

    test("the modal covers the sidebar rather than opening behind it", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();

        const sidebarLink = adminPage.locator('aside a, nav a').first();

        const box = await sidebarLink.boundingBox();

        if (! box) {
            test.skip(true, "no sidebar link to test against on this viewport");

            return;
        }

        /**
         * Whatever the browser finds at the sidebar's own coordinates must belong to the modal, not
         * to the menu underneath it — that is what "covered" means to a user clicking there.
         */
        const topmostIsModal = await adminPage.evaluate(
            ({ x, y }) => {
                const el = document.elementFromPoint(x, y);

                if (! el) {
                    return false;
                }

                return !! el.closest('[class*="z-[10004]"]');
            },
            { x: box.x + box.width / 2, y: box.y + box.height / 2 }
        );

        expect(
            topmostIsModal,
            "the sidebar must sit behind the modal backdrop, not in front of it"
        ).toBe(true);
    });

    test("the backdrop dims the page behind the modal", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        const backdrop = adminPage.locator('[class*="z-[10004]"]').first();

        await expect(backdrop).toBeVisible();

        /**
         * The admin CSS is pre-built, so a backdrop written with a class Tailwind never compiled
         * renders fully transparent. Asserting the computed colour is what catches that.
         */
        const background = await backdrop.evaluate(
            (el) => getComputedStyle(el).backgroundColor
        );

        expect(
            background,
            "the backdrop should actually be painted, not transparent"
        ).not.toBe("rgba(0, 0, 0, 0)");
    });

    /**
     * The dropdown and the modal render the same results from the same state, so only one of them
     * may be on screen at a time — otherwise the user sees the same search stacked on itself.
     */
    test("the header dropdown is hidden while the modal is open", async ({ adminPage }) => {
        const headerInput = adminPage
            .getByRole("textbox", { name: "Mega Search" })
            .first();

        await headerInput.fill(term);

        const dropdown = adminPage.locator("[data-section-count]").first();

        await expect(dropdown, "the header dropdown should be showing first").toBeVisible();

        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();

        await expect(
            dropdown,
            "the header dropdown must yield to the modal rather than stack behind it"
        ).toBeHidden();
    });

    test("typing in the modal does not reopen the header dropdown", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        await modalInput(adminPage).fill(term);

        await expect
            .poll(() => adminPage.locator("[data-modal-section-count]").count(), {
                message: "the modal should have run its own search",
            })
            .toBeGreaterThan(0);

        /**
         * `search()` is shared, so without a guard each keystroke in the modal would also open the
         * dropdown underneath it.
         */
        await expect(
            adminPage.locator("[data-section-count]").first(),
            "the shared search must not open the dropdown behind the modal"
        ).toBeHidden();
    });

    test("the dropdown works again once the modal is closed", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        await expect(modalInput(adminPage)).toBeVisible();

        await adminPage.keyboard.press("Escape");

        await expect(modalInput(adminPage)).toBeHidden();

        await adminPage.getByRole("textbox", { name: "Mega Search" }).first().fill(term);

        await expect(
            adminPage.locator("[data-section-count]").first(),
            "closing the modal must not leave the header search broken"
        ).toBeVisible();
    });

    test("the page behind the modal is blurred", async ({ adminPage }) => {
        await adminPage.keyboard.press("Control+k");

        const backdrop = adminPage.locator('[class*="z-[10004]"]').first();

        await expect(backdrop).toBeVisible();

        /**
         * Asserted on the computed style because the admin CSS is pre-built: Tailwind's
         * `backdrop-blur` utility was never compiled into it, so the class alone would leave the
         * page behind perfectly sharp.
         */
        const filter = await backdrop.evaluate(
            (el) => getComputedStyle(el).backdropFilter || (getComputedStyle(el) as any).webkitBackdropFilter
        );

        expect(filter, "the backdrop should blur what is behind it").toContain("blur");
    });

    test("the hint is hidden once a term is typed", async ({ adminPage }) => {
        await expect(hint(adminPage)).toBeVisible();

        await adminPage
            .getByRole("textbox", { name: "Mega Search" })
            .first()
            .fill(term);

        /**
         * The badge sits inside the field, so leaving it visible would put it on top of the text.
         */
        await expect(hint(adminPage)).toBeHidden();
    });
});
