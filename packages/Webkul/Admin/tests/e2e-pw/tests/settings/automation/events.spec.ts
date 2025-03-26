import { test, expect } from "../../../setup";
import { generateFullName, generateDescription, generateDate } from "../../../utils/faker";
import { confirmModal } from "../../../utils/components";

test.describe("event management", () => {
    test("should create a event", async ({ adminPage }) => {
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

    test("should edit a event", async ({ adminPage }) => {
        /**
         * Reaching to the events listing page.
         */
        await adminPage.goto("admin/settings/marketing/events");

        /**
         * Clicking on the edit button for the first event opens the modal.
         */
        await adminPage.locator('.row > div:nth-child(6) > a').first().click();

        /**
         * Fill the form with the event details.
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

        /**
         * Saving event and closing the modal.
         */
        await adminPage.getByRole('button', { name: 'Save Event' }).click();
        await expect(
            adminPage.getByText("Event updated successfully.")
        ).toBeVisible();
    });

    test("should delete a event", async ({ adminPage }) => {
        /**
         * Reaching to the event listing page.
         */
        await adminPage.goto("admin/settings/marketing/events");

        /**
         * Delete the first event.
         */
        await adminPage.locator('div:nth-child(6) > a:nth-child(2)').first().click();

        /**
         * Delete confirmation modal.
         */
        await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();

        await expect(
            adminPage.getByText("Event deleted successfully.")
        ).toBeVisible();
    });
});
