import { expect, test } from "../fixtures/AdminFixtures";
import { mailData, MailPage } from "../pages/MailPage";

test.describe("mail management", async () => {

    test("should compose and send an email", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.composeEmail(mailData,'normal');
    });
    test("should compose and send an email with cc", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.composeEmail(mailData,'cc');
    });
    test("should compose and send an email with bcc", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.composeEmail(mailData,'bcc');
    });
    test("should compose and send an email with cc and bcc", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.composeEmail(mailData,'both');
    });
    test("should able to save normal draft email", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.searchEmail(mailData.subject);
        await mailPage.saveDraftEmail(mailData,'normal');

    });
    test("should able to save cc draft email", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.searchEmail(mailData.subject);
        await mailPage.saveDraftEmail(mailData,'cc');

    });
    test("should able to save bcc draft email", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.searchEmail(mailData.subject);
        await mailPage.saveDraftEmail(mailData,'bcc');

    });     
    test("should able to save cc and bcc draft email", async ({ adminPage }) => {
        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.searchEmail(mailData.subject);
        await mailPage.saveDraftEmail(mailData,'both');

    });
    test("should able to move email to trash", async ({ adminPage }) => {

        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.saveDraftEmail(mailData,'normal');

        await mailPage.navigateToMailDraftPage();
        await mailPage.searchEmail(mailData.subject);
        await mailPage.movedEmailToTrash();
        await mailPage.navigateToMailTrashedPage();
        await expect(adminPage.getByRole('paragraph').filter({ hasText: mailData.subject }).first()).toBeVisible();

    });
    test("should able to delete email from trash", async ({ adminPage }) => {
        const mailPage = new MailPage(adminPage);
        await mailPage.navigateToMailInboxPage();
        await mailPage.saveDraftEmail(mailData,'normal');

        await mailPage.navigateToMailDraftPage();
        await mailPage.searchEmail(mailData.subject);
        await mailPage.movedEmailToTrash();

        await mailPage.navigateToMailTrashedPage();
        await mailPage.deleteEmailFromTrash(mailData.subject);
        await expect(adminPage.getByRole('paragraph').filter({ hasText: mailData.subject }).first()).not.toBeVisible();

    });

});