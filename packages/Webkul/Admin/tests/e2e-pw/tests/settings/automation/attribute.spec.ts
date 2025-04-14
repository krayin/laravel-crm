import { expect, test } from "../../../setup";
import { generateFirstName, generateFullName, generateSKU } from "../../../utils/faker";

test.describe("attribute management", async () => {

    test("should create an attribute", async ({ adminPage }) => {

        /**
         * Reaching to the attribute listing page.
         */
        await adminPage.goto("admin/settings/attributes");

        /**
         * Opening create attribute form in modal.
         */
        await adminPage.getByRole('link', { name: 'Create Attribute' }).click();

        /**
         * Filling the form with attribute details.
         */

        await adminPage.getByRole('textbox', { name: 'Name' }).click();
        await adminPage.getByRole('textbox', { name: 'Name' }).fill(generateFullName());
        await adminPage.getByRole('textbox', { name: 'Code' }).click();
        await adminPage.getByRole('textbox', { name: 'Code' }).fill(generateSKU());
        await adminPage.getByRole('textbox', { name: 'Code' }).press('Tab');
        await adminPage.locator('#type').selectOption('text');
        await adminPage.locator('#entity_type').selectOption('persons');
        await adminPage.locator('#validation').selectOption('numeric');

        /**
         * Save attribute and close the modal.
         */
        await adminPage.getByRole('button', { name: 'Save Attribute' }).click();
           /**
         * Checking if the attribute is save successfully.
         */
           await expect(adminPage.getByText('Success', { exact: true })).toBeVisible();

    })
    test("should edit an attribute", async ({ adminPage }) => {
        /**
         * Reaching to the attribute listing page.
         */
        await adminPage.goto("admin/settings/attributes");


        /**
         * Clicking on the edit button for the first attribute opens the modal.
         */
        await adminPage.locator('//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-edit"]').first().click();


        /**
         * Fill the form with the attribute details.
         */
        await adminPage.getByRole('textbox', { name: 'Name' }).click();
        await adminPage.getByRole('textbox', { name: 'Name' }).fill(generateFullName());
        await adminPage.getByRole('textbox', { name: 'Code' }).press('Tab');
        await adminPage.locator('#entity_type').selectOption('persons');

        /**
         * Save attribute and close the modal.
         */
        await adminPage.getByRole('button', { name: 'Save Attribute' }).click();
        /**
         * Checking if the attribute is updated successfully.
         */
        await expect(adminPage.getByText('Success', { exact: true })).toBeVisible();


    })
    test("should delete an attribute", async ({ adminPage }) => {
        /**
         * Reaching to the attribute listing page.
         */
        await adminPage.goto("admin/settings/attributes");


        /**
         * Clicking on the delete button for the first attribute opens the modal.
         */
        await adminPage.locator('//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-delete"]').first().click();
        /**
         * Delete confirmation modal.
         */
        await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();


        /**
         * Checking if the attribute is deleted successfully.
         */

        await expect(adminPage.getByText('Attribute deleted successfully.')).toBeVisible();

    })

})