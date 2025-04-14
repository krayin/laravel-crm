import { expect, test } from "../../../setup";
import { generateName } from "../../../utils/faker";


test.describe("tags management", () => {

    test("should create a tag", async ({ adminPage }) => {
        
        /**
         * Reaching to the tags listing page.
         */
        await adminPage.goto("admin/settings/tags");
        /**
         * Clicking on the create tag button opens the modal.
         */
        await adminPage.getByRole('button', { name: 'Create Tag' }).click();
        /**
         * Fill the form with the tag details.
         */
        await adminPage.getByRole('textbox', { name: 'Name' }).click();
        await adminPage.getByRole('textbox', { name: 'Name' }).fill(generateName());
        await adminPage.locator('span:nth-child(6) > .block').click();
        
        /**
         * Save tag and close the modal.
         */

        await adminPage.getByRole('button', { name: 'Save Tag' }).click();
        /**
         * Checking if the tag is created successfully.
         */
        await expect(adminPage.getByText('Tag created successfully')).toBeVisible();



    })
    test("should edit a tag", async ({ adminPage }) => {
        /**
         * Reaching to the tags listing page.
         */
        
        await adminPage.goto("admin/settings/tags");
        /**
         * Clicking on the edit button for the first tag opens the modal.
         */
        await adminPage.locator('//span[@class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"]').first().click();
        /**
         * Fill the form with the tag details.
         */
        await adminPage.getByRole('textbox', { name: 'Name' }).click();
        await adminPage.getByRole('textbox', { name: 'Name' }).fill(generateName());

        /**
         * Save tag and close the modal.
         */
        await adminPage.getByRole('button', { name: 'Save Tag' }).click();
        /**
         * Checking if the tag is updated successfully.
         */
        await expect(adminPage.getByText('Tag updated successfully.').first()).toBeVisible();

    })

    test("should delete a tag", async ({ adminPage }) => {  
        
        /**
         * Reaching to the tags listing page.
         */
        await adminPage.goto("admin/settings/tags");
        /**
         * Clicking on the delete button for the first tag opens the modal.
         */
        await adminPage.locator('//span[@class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"]').first().click();
        /**
         * Clicking on the delete button in the modal.
         */
        await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();
        /**
         * Checking if the tag is deleted successfully.
         */
        await expect(adminPage.getByText('Tag deleted successfully')).toBeVisible();
        
        /**
         * Closing the modal.
         */

        await adminPage.close();

    })





})