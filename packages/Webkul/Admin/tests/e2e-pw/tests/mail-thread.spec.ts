import { expect, test } from "../fixtures/AdminFixtures";

import { MailPage, mailData } from "../pages/MailPage";
import { runSuffix } from "../utils/acl";

/**
 * A mail body is attacker-supplied — it arrives from whoever sent the mail, not from the CRM — and
 * the thread view prints it into an authenticated admin page through the `v-safe-html` directive,
 * which is DOMPurify. That directive is the only thing between a crafted body and the admin's
 * session, so it is what this suite pins.
 *
 * Two facts about the environment shape how the suite is written:
 *
 * 1. Mail is composed as a draft rather than sent. Delivery needs a reachable SMTP host, which a
 *    test installation does not have, and a failed send leaves the record in `draft`.
 * 2. The draft route answers with JSON (`EmailController::view` returns a resource for the draft
 *    folder), so there is no rendered body to inspect there. The mail is moved to the inbox first,
 *    which is the folder whose view renders HTML — the same record, through the real view.
 */
test.describe.serial("mail thread and reply", () => {
    const suffix = runSuffix();

    const threadSubject = `Thread ${suffix}`;

    /**
     * Compose a mail carrying `body`, then move it into the inbox so the rendering path under test
     * is the HTML view rather than the draft folder's JSON.
     */
    async function seedInboxMail(adminPage: any, subject: string, body: string) {
        const mailPage = new MailPage(adminPage);

        await mailPage.navigateToMailInboxPage();

        await mailPage.saveDraftEmail({ ...mailData, subject, body }, "normal");

        /**
         * The draft's own id, read from the grid the mailbox itself uses. The rows are click
         * handlers rather than links, so there is no href to parse — the datagrid JSON is where the
         * id actually lives.
         */
        const grid = await adminPage.request.get(
            `admin/mail/draft?pagination[page]=1&pagination[per_page]=200`,
            { headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" } }
        );

        const record = ((await grid.json()).records ?? []).find((row: any) =>
            String(row.subject ?? "").includes(subject)
        );

        expect(record, "the seeded draft should be findable by its subject").toBeTruthy();

        const id = record.id;

        /**
         * `mass-update` moves the record into the inbox, which is the folder whose view renders
         * HTML — the draft route answers with JSON and would expose no rendered body to inspect.
         *
         * The request is issued from the page rather than through the api context, so the browser
         * attaches the session's current csrf cookie itself. Reading that cookie and replaying it
         * as a header worked only while it was fresh: the saved sign-in state carries one from an
         * earlier session, and a stale token is refused.
         */
        const status = await adminPage.evaluate(async (mailId: number) => {
            const body = new FormData();

            body.append("indices[0]", String(mailId));
            body.append("folders[0]", "inbox");
            body.append("value", "inbox");

            /**
             * Read from `document.cookie` so the token is the one this session currently holds,
             * whatever the saved sign-in state was carrying.
             */
            const token = decodeURIComponent(
                document.cookie.split("; ").find((entry) => entry.startsWith("XSRF-TOKEN="))?.split("=")[1] ?? ""
            );

            const response = await fetch("/admin/mail/mass-update", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-XSRF-TOKEN": token,
                },
                credentials: "same-origin",
                body,
            });

            return response.status;
        }, id);

        expect(status, "the mail should move into the inbox").toBeLessThan(400);
    }

    async function openInboxMail(adminPage: any, subject: string) {
        const mailPage = new MailPage(adminPage);

        await mailPage.navigateToMailInboxPage();
        await mailPage.searchEmail(subject);

        const row = adminPage.getByRole("paragraph").filter({ hasText: subject }).first();

        await expect(row).toBeVisible();

        await row.click();
    }

    test("a composed mail is stored and listed by its subject", async ({ adminPage }) => {
        const mailPage = new MailPage(adminPage);

        await mailPage.navigateToMailInboxPage();

        await mailPage.saveDraftEmail({ ...mailData, subject: threadSubject }, "normal");

        await mailPage.navigateToMailDraftPage();
        await mailPage.searchEmail(threadSubject);

        await expect(
            adminPage.getByRole("paragraph").filter({ hasText: threadSubject }).first()
        ).toBeVisible();
    });

    /**
     * The security half. A body carrying a script must reach the page as inert content, never as
     * something the browser runs.
     */
    test("@security a script in a mail body is not executed when the mail is opened", async ({
        adminPage,
    }) => {
        const payloadSubject = `XSS probe ${suffix}`;

        await seedInboxMail(
            adminPage,
            payloadSubject,
            `<img src=x onerror="window.__xss_executed = true">`
        );

        await openInboxMail(adminPage, payloadSubject);

        const executed = await adminPage.evaluate(() => (window as any).__xss_executed === true);

        expect(executed, "a mail body must never execute script in the admin's session").toBe(false);
    });

    test("@security an inline event handler is stripped from the rendered body", async ({
        adminPage,
    }) => {
        const payloadSubject = `Handler probe ${suffix}`;

        await seedInboxMail(
            adminPage,
            payloadSubject,
            `<a href="#" onclick="window.__xss_clicked = true">click</a>`
        );

        await openInboxMail(adminPage, payloadSubject);

        /**
         * The assertion is scoped to the rendered body rather than the whole page: the admin layout
         * has handlers of its own, and counting those would make this pass or fail for reasons that
         * have nothing to do with the mail's content.
         *
         * DOMPurify keeps safe markup but drops the handler, so the anchor survives and its
         * `onclick` does not.
         */
        const body = adminPage.locator('[v-safe-html], .dark\\:text-gray-300').first();

        const handlersInBody = await body
            .locator("[onclick], [onerror], [onload]")
            .count();

        expect(
            handlersInBody,
            "no inline event handler may survive into the rendered mail body"
        ).toBe(0);
    });

    /**
     * The thread view must render for a mail the admin may read. This one is seeded into the inbox
     * like the security cases above, because the draft folder answers with JSON rather than a
     * rendered page.
     */
    test("the mail view renders a thread the admin is allowed to read", async ({ adminPage }) => {
        const viewSubject = `Thread view ${suffix}`;

        await seedInboxMail(adminPage, viewSubject, "A plain body with no markup.");

        await openInboxMail(adminPage, viewSubject);

        await expect(adminPage.getByRole("paragraph").first()).toBeVisible();
    });
});
