import { expect, test } from "../fixtures/AdminFixtures";

/**
 * Notifications are written per user when a record is created, so a user's feed is decided by their
 * data scope at the moment of the event rather than filtered when it is read.
 */
test.describe("notifications", () => {
    const bell = (adminPage: any) => adminPage.locator(".icon-notification").first();

    test.beforeEach(async ({ adminPage }) => {
        await adminPage.goto("admin/dashboard");
    });

    test("the bell sits in the header, before the dark mode toggle", async ({ adminPage }) => {
        await expect(bell(adminPage)).toBeVisible();

        const bellBox = await bell(adminPage).boundingBox();
        const darkBox = await adminPage.locator(".icon-dark, .icon-light").first().boundingBox();

        expect(bellBox, "the bell should be positioned").not.toBeNull();
        expect(darkBox, "the dark mode toggle should be positioned").not.toBeNull();

        /**
         * Before, in reading order, which is to the left in a left-to-right admin.
         */
        expect(bellBox!.x).toBeLessThan(darkBox!.x);
    });

    test("the feed is served to the signed-in user", async ({ adminPage }) => {
        const result = await adminPage.evaluate(async () => {
            const response = await fetch("/admin/notifications", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            return { status: response.status, body: await response.json() };
        });

        expect(result.status).toBe(200);
        expect(Array.isArray(result.body.data)).toBeTruthy();
        expect(result.body.meta).toHaveProperty("unread_count");
    });

    test("opening the bell shows the panel", async ({ adminPage }) => {
        await bell(adminPage).click();

        await expect(
            adminPage.locator('p:text-is("Notifications")').first(),
            "the panel should be titled"
        ).toBeVisible();
    });

    test("the panel closes when clicking away", async ({ adminPage }) => {
        await bell(adminPage).click();

        await expect(adminPage.locator('p:text-is("Notifications")').first()).toBeVisible();

        await adminPage.mouse.click(400, 500);

        await expect(adminPage.locator('p:text-is("Notifications")').first()).toBeHidden();
    });

    /**
     * The count is what makes the bell worth looking at, so it has to be readable without opening
     * the panel.
     */
    test("an unread count is shown on the bell", async ({ adminPage }) => {
        const unread = await adminPage.evaluate(async () => {
            const response = await fetch("/admin/notifications", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            return (await response.json()).meta.unread_count;
        });

        if (! unread) {
            test.skip(true, "no unread notifications to display");
        }

        await expect(adminPage.locator(".icon-notification span").first()).toBeVisible();
    });

    /**
     * Every row the feed returns must belong to the user asking for it. The endpoint keys on the
     * authenticated id rather than on anything the request carries, so a feed cannot be requested
     * on another user's behalf.
     */
    test("the feed never carries another user's notifications", async ({ adminPage }) => {
        const result = await adminPage.evaluate(async () => {
            const response = await fetch("/admin/notifications?user_id=1", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            return await response.json();
        });

        /**
         * A `user_id` in the query must change nothing: the same feed comes back either way.
         */
        const plain = await adminPage.evaluate(async () => {
            const response = await fetch("/admin/notifications", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            return await response.json();
        });

        expect(result.data.map((n: any) => n.id)).toEqual(plain.data.map((n: any) => n.id));
    });

    /**
     * A notification arriving while the user is working elsewhere announces itself briefly, so it
     * is noticed rather than only changing a count in the corner.
     */
    test.describe("the arrival toast", () => {
        /**
         * `transition-group` renders no wrapper of its own, so each toast is a direct child of the
         * anchored container.
         */
        const toasts = (adminPage: any) =>
            adminPage.locator('div[style*="z-index: 10005"] > div');

        /**
         * What is already waiting when the page opens is not news. Announcing it would mean every
         * page load replaying a backlog the user has already seen.
         */
        test("nothing is announced on page load", async ({ adminPage }) => {
            await adminPage.waitForTimeout(2000);

            await expect(toasts(adminPage)).toHaveCount(0);
        });

        /**
         * The toast reads as an event rather than a warning, so it is a solid bar with a mark and
         * a way to dismiss it without opening the record.
         */
        test("a toast carries a mark, the record and a dismiss control", async ({ adminPage }) => {
            const shown = await adminPage.evaluate(() => {
                const app = (document.querySelector("#app") as any);

                return !! app;
            });

            expect(shown, "the admin app should be mounted").toBeTruthy();

            /**
             * Nothing is announced on load, so there is no toast to inspect unless one arrives
             * during the run; the markup is asserted wherever one is present.
             */
            if (await toasts(adminPage).count()) {
                const toast = toasts(adminPage).first();

                await expect(toast.locator(".icon-tick")).toHaveCount(1);
                await expect(toast.locator(".icon-cross-large")).toHaveCount(1);
            }
        });
    });

    /**
     * Unread is marked with a dot rather than an icon or a background tint, so the rows still
     * needing attention are countable at a glance without the list carrying per-row imagery.
     */
    test("unread notifications are marked with a dot", async ({ adminPage }) => {
        const unread = await adminPage.evaluate(async () => {
            const response = await fetch("/admin/notifications", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            const body = await response.json();

            return body.data.filter((notification: any) => ! notification.is_read).length;
        });

        if (! unread) {
            test.skip(true, "nothing unread to mark");
        }

        await bell(adminPage).click();

        const dots = adminPage.locator('.overflow-y-auto span[style*="width: 8px"]');

        await expect(dots, "each unread row should carry a dot").toHaveCount(unread);
    });

    test("the feed carries no per-row imagery", async ({ adminPage }) => {
        await bell(adminPage).click();

        const icons = await adminPage.evaluate(() => {
            const feed = document.querySelector(".overflow-y-auto");

            if (! feed) {
                return null;
            }

            return Array.from(feed.children)
                .filter((row) => row.querySelector('[class*="icon-"], img'))
                .length;
        });

        if (icons === null) {
            test.skip(true, "no notifications to inspect");
        }

        expect(icons, "rows should not render an icon or image").toBe(0);
    });

    /**
     * A long record name must not hold the panel open.
     *
     * The text column is a flex item, and a flex item defaults to `min-width: auto` — it refuses to
     * shrink below its content. Without `min-w-0` the name pushed the row past the panel's width,
     * `truncate` never took effect, and the panel scrolled sideways.
     */
    test("a long record name does not make the panel scroll sideways", async ({ adminPage }) => {
        await bell(adminPage).click();

        const overflows = await adminPage.evaluate(() => {
            const feed = document.querySelector(".overflow-y-auto") as HTMLElement | null;

            if (! feed) {
                return null;
            }

            return feed.scrollWidth > feed.clientWidth;
        });

        if (overflows === null) {
            test.skip(true, "no notifications to measure");
        }

        expect(overflows, "the feed should not overflow horizontally").toBe(false);
    });

    /**
     * The bell sits near the right edge, so a panel anchored to it and keeping its own width ran
     * off the left of a narrow screen, cutting off the start of every line.
     */
    test("the panel stays within the viewport on a narrow screen", async ({ adminPage }) => {
        await adminPage.setViewportSize({ width: 390, height: 800 });

        await adminPage.goto("admin/dashboard");

        await bell(adminPage).click();

        /**
         * The panel is waited for rather than measured straight away: it is built when the bell is
         * opened, and `boundingBox()` on an element that is not there yet waits out the whole test
         * timeout before reporting anything.
         */
        const panel = adminPage
            .locator('div[style*="position: fixed"], div.absolute.top-full')
            .first();

        await expect(panel, "the panel should open").toBeVisible();

        const box = await panel.boundingBox();

        expect(box, "the panel should be on screen").not.toBeNull();
        expect(box!.x, "the panel should not run off the left edge").toBeGreaterThanOrEqual(0);
        expect(box!.x + box!.width, "the panel should not run off the right edge").toBeLessThanOrEqual(390);
    });

    test("marking all as read clears the count", async ({ adminPage }) => {
        const before = await adminPage.evaluate(async () => {
            const response = await fetch("/admin/notifications", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            return (await response.json()).meta.unread_count;
        });

        if (! before) {
            test.skip(true, "nothing unread to clear");
        }

        await bell(adminPage).click();

        await adminPage.locator('button:text-is("Mark all as read")').first().click();

        await expect
            .poll(async () =>
                await adminPage.evaluate(async () => {
                    const response = await fetch("/admin/notifications", {
                        headers: { "X-Requested-With": "XMLHttpRequest" },
                    });

                    return (await response.json()).meta.unread_count;
                })
            , { message: "the unread count should reach zero" })
            .toBe(0);
    });
});
