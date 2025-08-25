import { test, expect } from "../../../setup";
import { confirmModal } from "../../../utils/components";

test.describe("warehouse management", () => {
    test("should create a warehouse", async ({ adminPage }) => {
        /**
         * Reaching to the warehouses listing page.
         */
        await adminPage.goto("admin/settings/warehouses");

        // Add code for creating a warehouse.

    });

    test("should edit a warehouse", async ({ adminPage }) => {
        /**
         * Reaching to the warehouses listing page.
         */
        await adminPage.goto("admin/settings/warehouses");

        /**
         * Clicking on the edit button for the first warehouse opens the modal.
         */
        // await adminPage.waitForSelector("span.cursor-pointer.icon-edit", {
        //     state: "visible",
        // });
        // const iconEdit = await adminPage.$$("span.cursor-pointer.icon-edit");
        // await iconEdit[0].click();

        // Add code to edit the warehouse.
    });

    test("should delete a warehouse", async ({ adminPage }) => {
        /**
         * Reaching to the warehouse listing page.
         */
        await adminPage.goto("admin/settings/warehouses");

        /**
         * Delete the first warehouse.
         */
        // await adminPage.waitForSelector("span.cursor-pointer.icon-delete");
        // const iconDelete = await adminPage.$$(
        //     "span.cursor-pointer.icon-delete"
        // );
        // await iconDelete[0].click();

        // /**
        //  * Delete confirmation modal.
        //  */
        // await confirmModal("Are you sure", adminPage);

        // await expect(
        //     adminPage.getByText("Warehouse deleted successfully.")
        // ).toBeVisible();
    });
}); 