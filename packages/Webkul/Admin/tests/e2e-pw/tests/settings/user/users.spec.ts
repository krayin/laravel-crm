import { test, expect } from "../../../setup";
import { generateFullName, generateEmail } from "../../../utils/faker";
import { confirmModal } from "../../../utils/components";

test.describe("user management", () => {
    test("should create a user", async ({ adminPage }) => {
        /**
         * Reaching to the user listing page.
         */
        await adminPage.goto("admin/settings/users");

        /**
         * Opening create user form in modal.
         */
        await adminPage.getByRole("button", { name: "Create User" }).click();

        /**
         * Filling the form with user details.
         */
        await adminPage
            .locator('input[name="name"]')
            .fill(generateFullName());
        await adminPage
            .locator('input[name="email"]')
            .fill(generateEmail());
        await adminPage
            .locator('input[name="password"]')
            .fill("admin123");
        await adminPage
            .locator('input[name="confirm_password"]')
            .fill("admin123");
        await adminPage
            .locator('select[name="role_id"]')
            .selectOption("1");
        await adminPage
            .locator('select[name="view_permission"]')
            .selectOption("global");
        await adminPage
            .locator('select[name="groups[]"]')
            .selectOption("1");

        // Clicking on the status toggler to make the user active.
        await adminPage.click('label[for="status"]');

        /**
         * Save user and close the modal.
         */
        await adminPage.getByRole("button", { name: "Save User" }).click();

        await expect(
            adminPage.getByText("User created successfully.")
        ).toBeVisible();
    });

    test("should edit a users", async ({ adminPage }) => {
        /**
         * Generating new name and email for the user.
         */
        const updatedName = generateFullName();
        const updatedEmail = generateEmail();

        /**
         * Reaching to the user listing page.
         */
        await adminPage.goto("admin/settings/users");

        /**
         * Clicking on the edit button for the first user opens the modal.
         */
        await adminPage.waitForSelector("span.cursor-pointer.icon-edit", {
            state: "visible",
        });
        const iconEdit = await adminPage.$$("span.cursor-pointer.icon-edit");
        await iconEdit[0].click();

        await adminPage.locator('input[name="name"]').fill(updatedName);
        await adminPage.locator('input[name="email"]').fill(updatedEmail);

        /**
         * Saving user and closing the modal.
         */
        await adminPage.getByRole("button", { name: "Save User" }).click();

        await expect(
            adminPage.getByText("User updated successfully.")
        ).toBeVisible();
        await expect(adminPage.getByText(updatedName)).toBeVisible();
        await expect(adminPage.getByText(updatedEmail)).toBeVisible();
    });

    test("should delete a user", async ({ adminPage }) => {
        /**
         * Reaching to the user listing page.
         */
        await adminPage.goto("admin/settings/users");

        /**
         * Delete the first user.
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
            adminPage.getByText("User deleted successfully.")
        ).toBeVisible();
    });
});
