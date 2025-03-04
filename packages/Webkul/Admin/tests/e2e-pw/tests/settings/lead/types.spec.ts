import { test, expect } from "../../../setup";
import { generateFullName } from "../../../utils/faker";
import { confirmModal } from "../../../utils/components";

test.describe("type management", () => {
    test("should create a type", async ({ adminPage }) => {
        /**
         * Reaching to the types listing page.
         */
        await adminPage.goto("admin/settings/types");

        /**
         * Opening create type form in modal.
         */
        await adminPage.getByRole("button", { name: "Create Type" }).click();

        /**
         * Filling the form with type details.
         */
        await adminPage
            .locator('input[name="name"]')
            .fill(generateFullName());

        /**
         * Save type and close the modal.
         */
        await adminPage.getByRole("button", { name: "Save Type" }).click();

        await expect(
            adminPage.getByText("Type created successfully.")
        ).toBeVisible();
    });

    test("should edit a type", async ({ adminPage }) => {
        /**
         * Generating new name and email for the type.
         */
        const updatedName = generateFullName();

        /**
         * Reaching to the types listing page.
         */
        await adminPage.goto("admin/settings/types");

        /**
         * Clicking on the edit button for the first type opens the modal.
         */
        await adminPage.waitForSelector("span.cursor-pointer.icon-edit", {
            state: "visible",
        });
        const iconEdit = await adminPage.$$("span.cursor-pointer.icon-edit");
        await iconEdit[0].click();

        await adminPage.locator('input[name="name"]').fill(updatedName);

        /**
         * Saving type and closing the modal.
         */
        await adminPage.getByRole("button", { name: "Save Type" }).click();

        await expect(
            adminPage.getByText("Type updated successfully.")
        ).toBeVisible();
    });

    test("should delete a type", async ({ adminPage }) => {
        /**
         * Reaching to the type listing page.
         */
        await adminPage.goto("admin/settings/types");

        /**
         * Delete the first type.
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
            adminPage.getByText("Type deleted successfully.")
        ).toBeVisible();
    });
});
