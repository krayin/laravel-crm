import { test, expect } from "../setup";
import { generateName, generateSKU, generateDescription, generateDate, generateEmail, generatePhoneNumber } from '../utils/faker';

test.describe("lead management", () => {
    test("should create a lead", async ({ adminPage }) => {
        /**
         * Go to the lead listing page.
         */
        await adminPage.goto("admin/leads");

        await adminPage.getByRole('link', { name: 'Create Lead' }).click();

        /**
         * Fill the lead form.
         */
        await adminPage.fill('input[name="title"]', generateName());
        await adminPage.fill('textarea[name="description"]', generateDescription());
        await adminPage
            .locator('select[name="lead_source_id"]')
            .selectOption("1");

        await adminPage.fill('input[name="expected_close_date"]', generateDate());

        await adminPage
            .locator('select[name="lead_type_id"]')
            .selectOption("1");
        
        await adminPage
            .locator('select[name="user_id"]')
            .selectOption("1");
        await adminPage.fill('input[name="lead_value"]', '1000');

        await adminPage.locator('div').filter({ hasText: /^Click to Add$/ }).nth(1).click();
        await adminPage.getByRole('textbox', { name: 'Search...' }).click();
        await adminPage.getByRole('textbox', { name: 'Search...' }).fill(generateName());
        await adminPage.getByText('Add as New').click();

        await adminPage.fill('input[name="person[emails][0][value]"]', generateEmail());
        await adminPage.fill('input[name="person[contact_numbers][0][value]"]', generatePhoneNumber());

        await adminPage.locator('div').filter({ hasText: /^Click to add$/ }).nth(2).click();
        await adminPage.getByRole('textbox', { name: 'Search...' }).click();
        await adminPage.getByRole('textbox', { name: 'Search...' }).fill(generateName());
        await adminPage.getByText('Add as New').click();


        await adminPage.getByRole('button', { name: 'Save' }).click();

        await expect(adminPage.getByText('Lead created successfully.', { exact: true })).toBeVisible();
    });
});