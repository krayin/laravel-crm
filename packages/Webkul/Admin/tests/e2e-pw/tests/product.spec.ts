import { test, expect } from "../setup";
import { generateName, generateSKU, generateDescription } from '../utils/faker';

test.describe("product management", () => {
    test("should create a product", async ({ adminPage }) => {
        /**
         * Go to the product listing page.
         */
        await adminPage.goto("admin/products");

        await adminPage.getByRole('link', { name: 'Create Product' }).click();

        /**
         * Fill the product form.
         */
        await adminPage.fill('input[name="name"]', generateName());
        await adminPage.fill('textarea[name="description"]', generateDescription());
        await adminPage.fill('input[name="sku"]', generateSKU());
        await adminPage.fill('input[name="price"]', "100");

        await adminPage.getByRole('button', { name: 'Save Products' }).click();

        await expect(adminPage.getByText('Product created successfully.', { exact: true })).toBeVisible();
    });

    test("should edit a product", async ({ adminPage }) => {
        /**
         * Go to the product listing page.
         */
        await adminPage.goto("admin/products");
        await adminPage.waitForSelector('a.primary-button', { state: 'visible' });

        /**
         * Clicking on the edit icon.
         */
        await adminPage.waitForSelector('span.cursor-pointer.icon-edit', { state: 'visible' });
        const iconEdit = await adminPage.$$('span.cursor-pointer.icon-edit');
        await iconEdit[0].click();

        await adminPage.waitForSelector('form[action*="/admin/products/edit"]');

        // Content will be added here. Currently just checking the general save button.

        await adminPage.click('button:has-text("Save Products")');

        await expect(adminPage.getByText('Product updated successfully.', { exact: true })).toBeVisible();
    });

    test('should delete a product', async ({ adminPage }) => {
         /**
         * Go to the product listing page.
         */
        await adminPage.goto('admin/products');
        await adminPage.waitForSelector('a.primary-button', { state: 'visible' });

        /**
         * Clicking on the delete icon.
         */
        await adminPage.waitForSelector('span.cursor-pointer.icon-delete', { state: 'visible' });
        const iconDelete = await adminPage.$$('span.cursor-pointer.icon-delete');
        await iconDelete[0].click();

        await adminPage.click('button.transparent-button + button.primary-button:visible');

        await expect(adminPage.getByText('Product deleted successfully.', { exact: true })).toBeVisible();
    });
});
