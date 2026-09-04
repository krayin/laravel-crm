import { test } from "../../fixtures/AdminFixtures";
import { webhookData, WebhookPage } from "../../pages/settings/WebhooksPage";

test.describe("webhook management", () => {
    test("verify create a webhook", async ({ adminPage }) => {
        const webhookPage = new WebhookPage(adminPage);
        await webhookPage.navigateToWebhooks();
        await webhookPage.createWebhook(webhookData);
    });
    test("verify update a webhook", async ({ adminPage }) => {
        const webhookPage = new WebhookPage(adminPage);
        await webhookPage.navigateToWebhooks();
        await webhookPage.searchWebhook(webhookData.name);
        await webhookPage.updateFirstWebhook(webhookData);
    });

    test("verify delete a webhook", async ({ adminPage }) => {
        const webhookPage = new WebhookPage(adminPage);
        await webhookPage.navigateToWebhooks();
        await webhookPage.searchWebhook(webhookData.name);      
        await webhookPage.deleteWebhook();
    });
})