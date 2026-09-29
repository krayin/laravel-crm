import { test } from "../../fixtures/AdminFixtures";
import { emailTemplateData, EmailTemplatePage } from "../../pages/settings/EmailTemplatesPage";

test.describe("email template management", () => {
    test("verify create an email template", async ({ adminPage }) => {
        const emailTemplatePage = new EmailTemplatePage(adminPage);
        await emailTemplatePage.navigateToEmailTemplates();
        await emailTemplatePage.createTemplate(emailTemplateData);
    });

    test("verify update an email template", async ({ adminPage }) => {
        const emailTemplatePage = new EmailTemplatePage(adminPage);
        await emailTemplatePage.navigateToEmailTemplates();
        await emailTemplatePage.updateEmailTemplate(emailTemplateData); 
    });

    test("verify delete an email template", async ({ adminPage }) => {
        const emailTemplatePage = new EmailTemplatePage(adminPage);
        await emailTemplatePage.navigateToEmailTemplates();
        await emailTemplatePage.deleteEmailTemplate();
    });
});