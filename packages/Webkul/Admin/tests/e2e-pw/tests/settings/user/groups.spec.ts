import { test, expect } from "../../../setup";
import { generateFullName, generateDescription } from "../../../utils/faker";

test.describe("group management", () => {
    test("should create a group", async ({ adminPage }) => {
        /**
         * Reaching to the group listing page.
         */
        await adminPage.goto("admin/settings/groups");

        /**
         * Opening create group form in modal.
         */
        await adminPage.getByRole("button", { name: "Create group" }).click();


        /**
         * Filling the form with group details.
         */
        await adminPage
            .locator('input[name="name"]')
            .fill(generateFullName());
        await adminPage
            .locator('textarea[name="description"]')
            .fill(generateDescription(240));

        /**
         * Save group and close the modal.
         */
        await adminPage.getByRole("button", { name: "Save Group" }).click();

        await expect(
            adminPage.getByText("Group created successfully.")
        ).toBeVisible();
    });
});
