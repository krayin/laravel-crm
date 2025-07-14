import { expect, test } from "../../../setup";
import { generateCampaignName, generateDate, generateDescription, generateFullName } from "../../../utils/faker";

test.describe("campaign management", () => {
   test("should create a event for campaign", async ({ adminPage }) => {
        /**
         * Reaching to the events listing page.
         */
        await adminPage.goto("admin/settings/marketing/events");

        /**
         * Opening create event form in modal.
         */
        await adminPage.getByRole("button", { name: "Create Event" }).click();

        /**
         * Filling the form with event details.
         */
        await adminPage
            .locator('input[name="name"]')
            .fill(generateFullName());
        await adminPage
            .locator('textarea[name="description"]')
            .fill(generateDescription());
        await adminPage
            .locator('input[name="date"]')
            .fill(generateDate());
        await adminPage.getByRole('textbox', { name: 'Date *' }).press('Enter');

        /**
         * Save event and close the modal.
         */
         await adminPage.getByRole('button', { name: 'Save Event' }).click();

        await expect(
            adminPage.getByText("Event created successfully.")
        ).toBeVisible();
    });
    test("should create a campaign", async ({ adminPage }) => {
        /* Navigate to the campaigns listing page */
        await adminPage.goto("admin/settings/marketing/campaigns");

        /* Click the 'Create Campaign' button to open the form modal */
        await adminPage.getByRole("button", { name: "Create Campaign" }).click();

        /* Fill in the campaign details */
        await adminPage.locator('input[name="name"]').fill("Test Campaign"); // Campaign name
        await adminPage.getByRole('textbox', { name: 'Subject *' }).fill(generateCampaignName()); // First subject fill
        await adminPage.getByRole('textbox', { name: 'Subject *' }).click(); // Trigger validation (if needed)
        await adminPage.getByRole('textbox', { name: 'Subject *' }).fill(generateCampaignName()); // Second subject fill with description

        /* Select an email template */
        await adminPage.getByText('Email Template').click();
        await adminPage.getByLabel('Email Template').selectOption('1');

        /* Select an event from the dropdown */
        await adminPage.getByLabel('Event').click();
        await adminPage.selectOption('select#marketing_event_id', { index: 0 });

        /* Toggle the active status */
        await adminPage.locator('.peer.h-5').click();

        /* Submit the form and create the campaign */
        await adminPage.getByRole('button', { name: 'Save Activity' }).click();

        /* Assert the success message */
        await expect(
            adminPage.getByText("Campaign created successfully.")
        ).toBeVisible();
    });

    test("should edit a campaign", async ({ adminPage }) => {
        /* Navigate to the campaigns listing page */
        await adminPage.goto("admin/settings/marketing/campaigns");

        /* Click the edit icon/button for the first campaign */
        await adminPage.locator('.row > div:nth-child(6) > a').first().click();

        /* Update campaign name */
        await adminPage.locator('input[name="name"]').fill("Updated Campaign Name");

        /* Update subject field */
        await adminPage.getByRole('textbox', { name: 'Subject *' }).fill("Updated Subject Name.");
        await adminPage.getByRole('textbox', { name: 'Subject *' }).click();
        await adminPage.getByRole('textbox', { name: 'Subject *' }).fill('subject');

        /* Update the email template */
        await adminPage.getByText('Email Template').click();
        await adminPage.getByLabel('Email Template').selectOption('1');

        /* Update the associated event */
        await adminPage.getByLabel('Event').click();
        await adminPage.selectOption('select#marketing_event_id', { index: 0 });

        /* Toggle the active status */
        await adminPage.locator('.peer.h-5').click();

        /* Save changes */
        await adminPage.getByRole('button', { name: 'Save Activity' }).click();

        /* Assert the update success message */
        await expect(
            adminPage.getByText("Campaign updated successfully.")
        ).toBeVisible();

    });
    test("should delete a campaign", async ({ adminPage }) => {
        /* Navigate to the campaigns listing page */
        await adminPage.goto("admin/settings/marketing/campaigns");

        /* Click the delete icon/button for the first campaign */
        await adminPage.locator('xpath=(//span[contains(@class, "icon-delete")])[1]').click();

        /* Confirm the deletion */
        await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();

        /* Assert the deletion success message */
        await expect(
            adminPage.getByText("Campaign deleted successfully.")
        ).toBeVisible();
    }); 

});
