import { expect, test } from "../fixtures/AdminFixtures";

/**
 * The leads listing has two views, and each has a settings control that does the same job: card
 * settings on the kanban board, column settings in the table. They were rendered on opposite sides
 * of the toolbar — card settings beside the search on the left, column settings out on the right —
 * so the icon jumped across the screen when the user switched views.
 *
 * The position is asserted rather than the markup: which slot the control sits in is an
 * implementation detail, but where the user's eye finds it is the behaviour that broke.
 */
test.describe("leads toolbar layout", () => {
    /**
     * The settings control for whichever view is open.
     *
     * The sidebar's own "Settings" menu carries the same `icon-setting` class and sits at a fixed
     * position, so matching on the class alone found that instead and reported no movement at all.
     * The control is taken from the toolbar itself, which is what actually differs between views.
     */
    const settingsIcon = (adminPage: any) =>
        adminPage.locator('.toolbarLeft .icon-setting, .toolbarRight .icon-setting, [class*="toolbar"] .icon-setting').first();

    async function iconLeftEdge(adminPage: any, viewType: string): Promise<number> {
        await adminPage.goto(`admin/leads?view_type=${viewType}`);

        await expect(settingsIcon(adminPage)).toBeVisible();

        const box = await settingsIcon(adminPage).boundingBox();

        expect(box, `the ${viewType} view should render a settings control`).not.toBeNull();

        return box!.x;
    }

    test("the settings control stays in place when switching between views", async ({
        adminPage,
    }) => {
        const kanbanX = await iconLeftEdge(adminPage, "kanban");

        const tableX = await iconLeftEdge(adminPage, "table");

        /**
         * A few pixels of difference would come from the two toolbars' own padding; a jump across
         * the toolbar is what this guards against.
         */
        expect(
            Math.abs(kanbanX - tableX),
            "the settings icon must not move when the view changes"
        ).toBeLessThan(40);
    });

    test("the settings control sits on the left in both views", async ({ adminPage }) => {
        const viewport = adminPage.viewportSize();

        for (const viewType of ["kanban", "table"]) {
            const x = await iconLeftEdge(adminPage, viewType);

            expect(
                x,
                `the ${viewType} settings control should sit in the left half of the toolbar`
            ).toBeLessThan((viewport?.width ?? 1280) / 2);
        }
    });

    test("the view switcher stays on the right in both views", async ({ adminPage }) => {
        const viewport = adminPage.viewportSize();

        for (const viewType of ["kanban", "table"]) {
            await adminPage.goto(`admin/leads?view_type=${viewType}`);

            const switcher = adminPage.locator('a[href*="view_type="]').first();

            await expect(switcher).toBeVisible();

            const box = await switcher.boundingBox();

            expect(
                box!.x,
                `the ${viewType} view switcher should stay in the right half`
            ).toBeGreaterThan((viewport?.width ?? 1280) / 2);
        }
    });
});
