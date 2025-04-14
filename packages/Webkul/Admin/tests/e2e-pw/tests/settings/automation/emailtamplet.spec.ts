import { test } from "../../../setup";
import { generateDescription, generateFullName } from "../../../utils/faker";


test.describe("email template management", () => {

    test("should create an email template", async ({ adminPage }) => {
        /**
         * Reaching to the email template listing page.
         */
        await adminPage.goto("admin/settings/email-templates");
        /**
         * click on the create email template button
         */
        await adminPage.getByRole('link', { name: 'Create Email Template' }).click();

        /**
         * Filling the form with email template details.
         */
        await adminPage.getByRole('textbox', { name: 'Subject' }).fill(generateFullName());
        await adminPage.locator('#placeholder').selectOption('{%leads.title%}');
        await adminPage.getByRole('textbox', { name: 'Content' }).fill(generateDescription());
        await adminPage.getByRole('textbox', { name: 'Name' }).fill('name');

        /**
         * Save email template and close the modal.
         */
        await adminPage.getByRole('button', { name: 'Save Email Template' }).click();
        
    });
    test("should edit an email template", async ({ adminPage }) => {


        /**
         * Reaching to the email template listing page.
         */
        await adminPage.goto("admin/settings/email-templates");

        /**
         * Clicking on the edit button for the first email template opens the modal.
         */
        await adminPage.locator('//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-edit"]').first().click();

        /**
         * Fill the form with the email template details.
         */
        await adminPage.getByRole('textbox', { name: 'Subject' }).fill(generateFullName());
        await adminPage.getByRole('textbox', { name: 'Content' }).fill(generateDescription());

        /**
         * Save email template and close the modal.
         */
        await adminPage.getByRole('button', { name: 'Save Email Template' }).click();
    }   );

    test("should delete an email template", async ({ adminPage }) => {
        /**
         * Reaching to the email template listing page.
         */
        await adminPage.goto("admin/settings/email-templates");

        /**
         * Clicking on the delete button for the first email template opens the modal.
         */
        await adminPage.locator('//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-delete"]').first().click();

        /**
         * Confirming the deletion of the email template.
         */
        await adminPage.getByRole('button', { name: 'Delete' }).click();
    }
    );



});