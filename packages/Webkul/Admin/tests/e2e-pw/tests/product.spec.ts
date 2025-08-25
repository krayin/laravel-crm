import { test, expect } from "../setup";
import { generateName, generateSKU, generateDescription } from "../utils/faker";

test.describe("product management", () => {
    test("should create a product", async ({ adminPage }) => {
        /**
         * Go to the product listing page.
         */
        await adminPage.goto("admin/products");

        /**
         * Create Product.
         */
        await adminPage.getByRole("link", { name: "Create Product" }).click();

        /**
         * Fill the product form.
         */
        const name = generateName();
        const description = generateDescription();
        await adminPage.waitForSelector('input[name="name"]', { state: 'visible' });
        await adminPage.getByRole('textbox', { name: 'Name *' }).click();
        await adminPage.locator('#name').clear();
        await adminPage.getByRole('textbox', { name: 'Name *' }).fill(name);
        await adminPage.locator('textarea[name="description"]').clear();
        await adminPage
            .locator('textarea[name="description"]')
            .type(description);
        await adminPage.fill('input[name="sku"]', generateSKU());
        await adminPage.waitForSelector('input[name="price"]', {
            state: "visible",
        });
        await adminPage.fill('input[name="price"]', "100");

        await adminPage.waitForSelector('input[name="quantity"]', {
            state: "visible",
        });
        await adminPage.fill('input[name="quantity"]', "50");

        /**
         * Save Product.
         */
        await adminPage.getByRole("button", { name: "Save Products" }).click();

        /**
         * sucess message appear.
         */
        await expect(adminPage.locator("#app")).toContainText(
            "Product created successfully."
        );
    });

  test("should edit a product", async ({ adminPage }) => {
        /**
         * Go to the product listing page.
         */
        await adminPage.goto("admin/products");
        await adminPage.waitForSelector("a.primary-button", {
            state: "visible",
        });

        /**
         * Clicking on the edit icon.
         */
        await adminPage.locator("span.icon-edit").first().click();
        await adminPage.waitForSelector('form[action*="/admin/products/edit"]');

        /**
         * Edit the product Detail
         */
        await adminPage.fill('input[name="name"]', generateName());
        await adminPage.fill('input[name="price"]', "1000");
        await adminPage.fill('input[name="quantity"]', "500");
        await adminPage.click('button:has-text("Save Products")');
        await expect(adminPage.locator("#app")).toContainText(
            "Product updated successfully." );
    });

    test("should delete a product", async ({ adminPage }) => {
        /**
         * Go to the product listing page.
         */
        await adminPage.goto("admin/products");
        await adminPage.waitForSelector("a.primary-button", {
            state: "visible",
        });

        /**
         * Clicking on the delete icon.
         */
        await adminPage.waitForSelector("span.cursor-pointer.icon-delete", {
            state: "visible",
        });
        await adminPage.locator("span.icon-delete").first().click();
        await adminPage.click(
            "button.transparent-button + button.primary-button:visible"
        );
        await expect(adminPage.locator("#app")).toContainText(
            "Product deleted successfully."
        );
    });
});
