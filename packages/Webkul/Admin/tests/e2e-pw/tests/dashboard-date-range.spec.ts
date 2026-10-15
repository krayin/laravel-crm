import { expect, test } from "../fixtures/AdminFixtures";

/**
 * The dashboard's date filter is a single range control rather than a pair of start/end inputs.
 *
 * The pair made the common case — "last 30 days", "this month" — a manual job of picking two dates
 * and knowing today's, and neither input showed the range as a whole. The control now carries the
 * range on its face and opens a panel with the usual periods beside a two-month calendar.
 */
test.describe("dashboard date range filter", () => {
    const control = (adminPage: any) =>
        adminPage.locator("button:has(.icon-calendar)").first();

    const preset = (adminPage: any, label: string) =>
        adminPage.locator(`button:text-is("${label}")`).first();

    test.beforeEach(async ({ adminPage }) => {
        await adminPage.goto("admin/dashboard");
    });

    test("the control shows the range it currently holds", async ({ adminPage }) => {
        await expect(control(adminPage)).toBeVisible();

        /**
         * Both ends on one control, which is what the pair of inputs could not show.
         */
        await expect(control(adminPage)).toHaveText(/\w+ \d+, \d{4}\s*–\s*\w+ \d+, \d{4}/);
    });

    test("opening the control reveals the presets and a two-month calendar", async ({
        adminPage,
    }) => {
        await control(adminPage).click();

        for (const label of ["Today", "Yesterday", "Last 30 Days", "This Month", "This Year"]) {
            await expect(
                preset(adminPage, label),
                `the ${label} preset should be offered`
            ).toBeVisible();
        }

        /**
         * Two months are shown so a range crossing a month boundary is picked in one go.
         */
        await expect(adminPage.locator(".flatpickr-month")).toHaveCount(2);
    });

    test("choosing a preset updates the range", async ({ adminPage }) => {
        const before = await control(adminPage).textContent();

        await control(adminPage).click();

        await preset(adminPage, "This Month").click();

        await expect
            .poll(() => control(adminPage).textContent(), {
                message: "the control should report the preset's range",
            })
            .not.toBe(before);

        /**
         * "This Month" runs from the first of the month to today, so the start must be a 1st.
         */
        await expect(control(adminPage)).toHaveText(/\w+ 1, \d{4}\s*–/);
    });

    test("the selected preset is marked in the list", async ({ adminPage }) => {
        await control(adminPage).click();

        await preset(adminPage, "This Month").click();

        await control(adminPage).click();

        await expect(
            preset(adminPage, "This Month"),
            "the active period should be distinguishable from the rest"
        ).toHaveClass(/font-semibold/);
    });

    test("the panel closes when clicking away", async ({ adminPage }) => {
        await control(adminPage).click();

        await expect(preset(adminPage, "Today")).toBeVisible();

        await adminPage.mouse.click(5, 400);

        await expect(preset(adminPage, "Today")).toBeHidden();
    });

    /**
     * The reports redraw from the `reporting-filter-updated` event, so a range change has to reach
     * them — a control that only updated its own label would look right and show stale figures.
     */
    test("changing the range reloads the reports", async ({ adminPage }) => {
        const requests: string[] = [];

        adminPage.on("request", (request: any) => {
            if (request.url().includes("/admin/dashboard/stats")) {
                requests.push(request.url());
            }
        });

        await control(adminPage).click();

        await preset(adminPage, "Last 30 Days").click();

        await expect
            .poll(() => requests.length, {
                message: "the reports should refetch for the new range",
            })
            .toBeGreaterThan(0);
    });

    /**
     * A range picked by hand on the calendar must reach the reports, not just the control's label.
     *
     * The component did not declare `change` as its own event, so Vue also let the calendar input's
     * native DOM `change` fall through to the same listener. That event arrived last and carried no
     * range, overwriting both dates with `undefined`: the request went out with no `start` or `end`
     * and every report silently fell back to its default period, showing current figures for a
     * range the user had picked in the past.
     */
    test("a hand-picked range reaches the reports", async ({ adminPage }) => {
        const requests: string[] = [];

        adminPage.on("request", (request: any) => {
            if (request.url().includes("/admin/dashboard/stats")) {
                requests.push(request.url());
            }
        });

        await control(adminPage).click();

        const days = adminPage.locator(".flatpickr-days").first();
        const selectable = '.flatpickr-day:not(.prevMonthDay):not(.nextMonthDay):not(.flatpickr-disabled)';

        await days.locator(selectable).nth(3).click();
        await days.locator(selectable).nth(8).click();

        await expect
            .poll(() => requests.length, { message: "the reports should refetch" })
            .toBeGreaterThan(0);

        for (const url of requests) {
            expect(url, "every report must receive the picked range").toMatch(
                /start=\d{4}-\d{2}-\d{2}/
            );
            expect(url).toMatch(/end=\d{4}-\d{2}-\d{2}/);
        }
    });

    /**
     * The dashboard reports on what has already happened, and the server clamps a future end date
     * back to today. Offering future days therefore produced a range whose end preceded its start,
     * which the reports labelled "29 Nov - 14 Oct".
     */
    test("future dates cannot be picked", async ({ adminPage }) => {
        await control(adminPage).click();

        const disabled = adminPage.locator(".flatpickr-day.flatpickr-disabled");

        await expect(
            disabled.first(),
            "days after today should not be selectable"
        ).toBeAttached();
    });

    test("a range sent entirely in the future is reported in order", async ({ adminPage }) => {
        await adminPage.goto("admin/dashboard");

        const body = await adminPage.evaluate(async () => {
            const response = await fetch(
                "/admin/dashboard/stats?type=total-leads&start=2099-11-29&end=2099-11-30",
                { headers: { "X-Requested-With": "XMLHttpRequest" } }
            );

            return await response.json();
        });

        const [start, end] = body.date_range.split(" - ");

        expect(
            new Date(`${start} 2099`) <= new Date(`${end} 2099`) || start === end,
            `the range should not be inverted, got "${body.date_range}"`
        ).toBeTruthy();
    });

    test("the export link carries the selected range", async ({ adminPage }) => {
        await control(adminPage).click();

        await preset(adminPage, "This Month").click();

        const href = await adminPage
            .locator('a:has-text("Export PDF")')
            .first()
            .getAttribute("href");

        expect(href, "the export should use the range on screen").toMatch(/start=\d{4}-\d{2}-\d{2}/);
        expect(href).toMatch(/end=\d{4}-\d{2}-\d{2}/);
    });
});
