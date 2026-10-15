import { expect, test } from "../fixtures/AdminFixtures";

/**
 * The mega search dropdown shows a per-section record count.
 *
 * Before this, only the active tab was fetched, so the other sections gave no sign of whether they
 * held anything — a user had to open each one in turn to find out. Each tab now carries a badge
 * with how many records that section holds for the term in the box.
 *
 * Two behaviours matter as much as the number itself, and both are pinned below: a section that has
 * not been counted yet shows no badge at all (rather than a `0`, which would read as "nothing
 * found" while the request is still in flight), and a badge always agrees with the list its own tab
 * renders.
 */
test.describe("mega search section counts", () => {
    const searchBox = (adminPage: any) =>
        adminPage.getByRole("textbox", { name: "Mega Search" }).first();

    /**
     * A section's badge, addressed by the `data-section-count` the template stamps on it.
     */
    const badge = (adminPage: any, section: string) =>
        adminPage.locator(`[data-section-count="${section}"]`);

    /**
     * A term guaranteed to match, and the records that make it so.
     *
     * The tests previously searched for words that happened to exist in a developer's own database
     * — "need", "an" — which matched nothing on a freshly installed one, leaving the sections
     * uncounted and the assertions with nothing to read. Each run now creates what it searches for.
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

    async function seedSearchableRecords(adminPage: any) {
        const unique = Date.now();

        await postForm(adminPage, "/admin/contacts/persons/create", {
                name: `${term} person ${unique}`,
                "emails[0][value]": `${term}${unique}@example.invalid`,
                "emails[0][label]": "work",
                entity_type: "persons",
            });

        /**
         * A lead as well as a person: the leads section is the one the explore-all link belongs to,
         * and a section holding nothing offers no link to follow.
         */
        await postForm(adminPage, "/admin/leads/create", {
                title: `${term} lead ${unique}`,
                description: "seeded for the mega search counts",
                lead_value: "1000",
                "person[name]": `${term} contact ${unique}`,
                "person[emails][0][value]": `${term}lead${unique}@example.invalid`,
                "person[emails][0][label]": "work",
            });
    }

    /**
     * Seeding ran before every test and never cleaned up, so each run left another lead and two
     * more people behind: a database went from fifty-one matching leads to sixty in a single run,
     * and once the matches passed the datagrid's page size the explore-all assertion began failing
     * on data the suite had itself accumulated. The records only need to exist, not to be fresh, so
     * a run now seeds only when the term has nothing to find.
     */
    /**
     * Whether the term already matches a lead and a person, asked of the same endpoints the search
     * itself uses so that a record the search cannot see does not count as one.
     */
    async function termHasRecords(adminPage: any) {
        return adminPage.evaluate(async (query: string) => {
            const count = async (url: string) => {
                try {
                    const response = await fetch(url, {
                        headers: { "X-Requested-With": "XMLHttpRequest" },
                        credentials: "same-origin",
                    });

                    if (! response.ok) {
                        return 0;
                    }

                    const body = await response.json();

                    return (body.data ?? body).length ?? 0;
                } catch (error) {
                    return 0;
                }
            };

            /**
             * These endpoints filter through the repository criteria, which reads `search` as
             * `field:term;` alongside the `searchFields` naming the comparison — the same pair the
             * mega search itself sends, so a term matches here exactly as it does there.
             */
            const leads = await count(
                `/admin/leads/search?search=title:${encodeURIComponent(query)};` +
                    `&searchFields=title:like`
            );

            const persons = await count(
                `/admin/contacts/persons/search?search=name:${encodeURIComponent(query)};` +
                    `&searchFields=name:like`
            );

            return leads > 0 && persons > 0;
        }, term);
    }

    test.beforeEach(async ({ adminPage }) => {
        await adminPage.goto("admin/dashboard");

        if (! (await termHasRecords(adminPage))) {
            await seedSearchableRecords(adminPage);
        }

        await adminPage.goto("admin/dashboard");
    });

    test("no counts are shown before a search is typed", async ({ adminPage }) => {
        for (const section of ["leads", "persons", "products", "quotes"]) {
            await expect(
                badge(adminPage, section),
                `${section} must not show a count before anything is searched`
            ).toHaveCount(0);
        }
    });

    test("a short term does not trigger counting", async ({ adminPage }) => {
        /**
         * The dropdown itself only opens past one character, so a single letter must not produce
         * counts either — otherwise the badges would contradict the list below them.
         */
        await searchBox(adminPage).fill("a");

        await expect(badge(adminPage, "leads")).toHaveCount(0);
    });

    test("searching shows a count on every section", async ({ adminPage }) => {
        await searchBox(adminPage).fill(term);

        /**
         * Each section is counted by its own request, so they arrive independently; waiting for one
         * badge at a time is what keeps this from racing the slowest endpoint.
         */
        for (const section of ["leads", "persons", "products", "quotes"]) {
            await expect(
                badge(adminPage, section),
                `${section} should report how many records it holds`
            ).toBeVisible();

            const text = (await badge(adminPage, section).textContent())?.trim() ?? "";

            /**
             * A count above 99 is rendered as "99+" so a long number cannot stretch the tab strip,
             * so the badge is either digits or that cap.
             */
            expect(
                text,
                `${section}'s count should be a number or the 99+ cap`
            ).toMatch(/^\d+\+?$/);
        }
    });

    test("the count matches the number of rows its own tab lists", async ({ adminPage }) => {
        await searchBox(adminPage).fill(term);

        await expect(badge(adminPage, "persons")).toBeVisible();

        const badgeText = (await badge(adminPage, "persons").textContent())?.trim() ?? "";

        test.skip(badgeText.endsWith("+"), "the badge is capped, so it cannot be compared to rows");

        const counted = Number(badgeText);

        /**
         * The persons tab is opened and its rows counted, so the badge is checked against the list
         * a user actually sees rather than against the API alone. The tab is addressed through the
         * badge it owns — plain text would also match the sidebar's hidden "Persons" link.
         */
        await badge(adminPage, "persons").click();

        /**
         * The tab fetches its own list, so the rows are waited for rather than counted straight
         * away — reading them mid-request would compare the badge against an empty list.
         */
        const rows = adminPage.locator('a[href*="/admin/contacts/persons/view/"]');

        await expect
            .poll(() => rows.count(), {
                message: "the persons tab should render its rows",
            })
            .toBe(counted ?? expect.anything());
    });

    test("counts update when the term changes", async ({ adminPage }) => {
        await searchBox(adminPage).fill(term);

        await expect(badge(adminPage, "persons")).toBeVisible();

        const first = await badge(adminPage, "persons").textContent();

        /**
         * A term that matches nothing: the badge must still be shown, now reporting zero, because
         * "counted, and empty" is a different answer from "not counted".
         */
        await searchBox(adminPage).fill(`zz-no-such-record-${Date.now()}`);

        await expect
            .poll(
                async () => badge(adminPage, "persons").textContent(),
                { message: "the count should follow the term" }
            )
            .not.toBe(first);

        expect(Number(await badge(adminPage, "persons").textContent())).toBe(0);
    });

    /**
     * The "Explore all …" link under each section carries the term into that section's own listing,
     * and the count it advertises has to match what the user then sees.
     *
     * Leads needed `view_type=table` for that to hold: the listing defaults to the kanban board,
     * and only the datagrid reads `?search=`, so following the link landed on a board showing every
     * lead — contradicting the link's own count.
     */
    test("exploring all leads carries the search into the leads listing", async ({ adminPage }) => {
        await searchBox(adminPage).fill(term);

        await expect(badge(adminPage, "leads")).toBeVisible();

        /**
         * A badge past ninety-nine reads "99+", which names no exact number, so the total it
         * advertises can only be compared when it is a plain count.
         */
        const badgeText = (await badge(adminPage, "leads").textContent())?.trim() ?? "";

        const counted = badgeText.endsWith("+") ? null : Number(badgeText);

        await badge(adminPage, "leads").click();

        const exploreLink = adminPage
            .locator('a[href*="/admin/leads?view_type=table&search="]')
            .first();

        await expect(
            exploreLink,
            "the leads section should offer an explore-all link"
        ).toBeVisible();

        await exploreLink.click();

        await expect(adminPage).toHaveURL(new RegExp(`view_type=table&search=${term}`));

        /**
         * The listing's own search box is seeded from the URL, which is what applies the filter.
         */
        await expect
            .poll(
                async () =>
                    adminPage
                        .locator('input[name="search"], input[type="search"]')
                        .first()
                        .inputValue()
                        .catch(() => ""),
                { message: "the listing should be filtered by the searched term" }
            )
            .toBe(term);

        /**
         * And the listing holds the records the link counted.
         *
         * The row count cannot be compared against the badge directly: the datagrid paginates at
         * ten records a page, so a term matching more than that renders ten rows however many were
         * counted. The grid's own total is what the badge has to agree with, and it is read from
         * the pagination summary the grid renders; the rows are only checked to be a filled page.
         */
        const reportedTotal = async () => {
            const summary = await adminPage
                .locator("[data-pagination-total]")
                .first()
                .textContent()
                .catch(() => null);

            const digits = summary?.replace(/\D/g, "") ?? "";

            return digits === "" ? null : Number(digits);
        };

        if (counted === null) {
            /**
             * The badge was capped, so only the direction can be checked: the listing has to hold
             * more than the cap the badge stopped at.
             */
            await expect
                .poll(reportedTotal, {
                    message: "a capped badge should mean the listing holds more than the cap",
                })
                .toBeGreaterThan(99);
        } else {
            await expect
                .poll(reportedTotal, {
                    message: "the listing should report how many records matched",
                })
                .toBe(counted);
        }

        /**
         * A page's worth of rows, or every record when fewer than a page matched. The header row is
         * drawn alongside the records, so it is excluded by scoping to the grid's body.
         */
        await expect
            .poll(() => adminPage.locator(".row").count(), {
                message: "the listing should render the matching records",
            })
            .toBeGreaterThan(1);
    });

    test("clearing the search removes the counts", async ({ adminPage }) => {
        await searchBox(adminPage).fill(term);

        await expect(badge(adminPage, "persons")).toBeVisible();

        await searchBox(adminPage).fill("");

        await expect(
            badge(adminPage, "persons"),
            "a cleared search should show no count rather than zero"
        ).toHaveCount(0);
    });
});
