import { test, expect } from '../../setup';
import { generateEmail, generateEmailSubject, generateDescription } from '../../utils/faker';

async function composeMail(adminPage, ccMail = false, bccMail = false) {
    /**
     * Reaching to the mail listing page.
     */
    await adminPage.goto("admin/mail/inbox");

    /**
     * Opening compose mail in modal.
     */
    await adminPage.getByRole('button', { name: 'Compose Mail' }).click();
    await adminPage.fill('input[name="temp-reply_to"]', generateEmail());
    await adminPage.fill('input[name="subject"]', generateEmailSubject());
    const frameElementHandle = await adminPage.waitForSelector(
        "iframe.tox-edit-area__iframe"
    );

    const frame = await frameElementHandle.contentFrame();

    await frame.waitForSelector("body");
    await frame.fill("body", generateDescription());

    /**
     * Sending mail and closing the modal.
     */
    await adminPage.getByRole("button", { name: "Send" }).click();

    await expect(adminPage.getByText("Email sent successfully.")).toBeVisible();
}

test.describe("mail management", () => {
    /**
     * Should be able to compose a mail.
     */
    test("should compose a mail", async ({ adminPage }) => {
        await composeMail(adminPage);
    });

    /**
     * Should be able to compose a mail with CC.
     */
    test("should compose a mail with CC", async ({ adminPage }) => {
        const ccMail = true;

        await composeMail(adminPage, ccMail);
    });

    /**
     * Should be able to compose a mail with BCC.
     */
    test("should compose a mail with BCC", async ({ adminPage }) => {
        const bccMail = true;

        await composeMail(adminPage, bccMail);
    });

    /**
     * Should be able to compose a mail with CC & BCC.
     */
    test("should compose a mail with CC & BCC", async ({ adminPage }) => {
        const ccMail = true;

        const bccMail = true;

        await composeMail(adminPage, ccMail, bccMail);
    });
});
