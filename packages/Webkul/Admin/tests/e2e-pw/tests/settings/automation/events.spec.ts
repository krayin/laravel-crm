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

        /**
         * Save event and close the modal.
         */
        await adminPage.getByRole("button", { name: "Save Activity" }).click();

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
        await adminPage.waitForSelector("span.cursor-pointer.icon-edit", {
            state: "visible",
        });
        const iconEdit = await adminPage.$$("span.cursor-pointer.icon-edit");
        await iconEdit[0].click();

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
        await adminPage.getByRole("button", { name: "Save Activity" }).click();

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
        await adminPage.waitForSelector("span.cursor-pointer.icon-delete");
        const iconDelete = await adminPage.$$(
            "span.cursor-pointer.icon-delete"
        );
        await iconDelete[0].click();

        /**
         * Delete confirmation modal.
         */
        await confirmModal("Are you sure", adminPage);

        await expect(
            adminPage.getByText("Event deleted successfully.")
        ).toBeVisible();
    });
});
