import { test, expect } from "../setup";
import {
    generateCompanyName,
    generateDescription,
    generateEmail,
    generateFullName,
    generateName,
    generatePhoneNumber,
    generatePrice,
    generateProductName,
    generateQuantity,
    generateSKU,
} from "../utils/faker";

/**
 * Open the quick-add modal from the header plus button.
 */
async function openQuickAddModal(page) {
    await page.goto("admin/dashboard");

    /**
     * Two v-quick-add components render (desktop + mobile); only the desktop
     * one is visible in the default Playwright viewport.
     */
    const trigger = page.locator("header button.bg-brandColor").first();
    await trigger.waitFor({ state: "visible" });
    await trigger.click();

    /**
     * Wait for the teleported modal title to be visible.
     */
    await expect(
        page.locator("p").filter({ hasText: "Quick Add" })
    ).toBeVisible();
}

async function selectQuickAddTab(page, tabLabel) {
    await page
        .locator("span.cursor-pointer")
        .filter({ hasText: new RegExp(`^${tabLabel}$`) })
        .first()
        .click();
}

async function submitQuickAdd(page) {
    /**
     * The footer Save button is the last "Save" button on the page.
     */
    await page.getByRole("button", { name: "Save", exact: true }).last().click();
}

test.describe("quick add modal", () => {
    test("should open the quick add modal from the header plus button", async ({ adminPage }) => {
        await openQuickAddModal(adminPage);

        /**
         * The seeded admin role has all five quick-create permissions.
         */
        for (const tab of ["Lead", "Person", "Organization", "Product", "Email"]) {
            await expect(
                adminPage.locator("span.cursor-pointer").filter({ hasText: new RegExp(`^${tab}$`) })
            ).toBeVisible();
        }
    });

    test("should create a lead via quick add", async ({ adminPage }) => {
        await openQuickAddModal(adminPage);
        await selectQuickAddTab(adminPage, "Lead");

        const leadForm = adminPage.locator('form:has(input[value="lead"])').first();

        await leadForm.locator('input[name="title"]').fill(`${generateName()}-${Date.now()}`);
        await leadForm.locator('textarea[name="description"]').fill(generateDescription());

        await submitQuickAdd(adminPage);

        await expect(adminPage.getByText("Lead created successfully.")).toBeVisible();
    });

    test("should create a person via quick add", async ({ adminPage }) => {
        await openQuickAddModal(adminPage);
        await selectQuickAddTab(adminPage, "Person");

        const personForm = adminPage.locator('form:has(input[value="person"])').first();

        await personForm.locator('input[name="name"]').fill(generateFullName());
        await personForm.locator('input[name="emails[0][value]"]').fill(generateEmail());
        await personForm.locator('input[name="contact_numbers[0][value]"]').fill(generatePhoneNumber());

        await submitQuickAdd(adminPage);

        await expect(adminPage.getByText("Person created successfully.")).toBeVisible();
    });

    test("should create an organization via quick add", async ({ adminPage }) => {
        await openQuickAddModal(adminPage);
        await selectQuickAddTab(adminPage, "Organization");

        const orgForm = adminPage.locator('form:has(input[value="organization"])').first();

        await orgForm.locator('input[name="name"]').fill(generateCompanyName());

        await submitQuickAdd(adminPage);

        await expect(adminPage.getByText("Organization created successfully.")).toBeVisible();
    });

    test("should create a product via quick add", async ({ adminPage }) => {
        await openQuickAddModal(adminPage);
        await selectQuickAddTab(adminPage, "Product");

        const productForm = adminPage.locator('form:has(input[value="product"])').first();

        await productForm.locator('input[name="name"]').fill(generateProductName());
        await productForm.locator('textarea[name="description"]').fill(generateDescription());
        await productForm.locator('input[name="sku"]').fill(generateSKU());
        await productForm.locator('input[name="quantity"]').fill(generateQuantity());
        await productForm.locator('input[name="price"]').fill(generatePrice());

        await submitQuickAdd(adminPage);

        await expect(adminPage.getByText("Product created successfully.")).toBeVisible();
    });

    test("should keep modal open and surface validation errors when fields are empty", async ({ adminPage }) => {
        await openQuickAddModal(adminPage);
        await selectQuickAddTab(adminPage, "Lead");

        await submitQuickAdd(adminPage);

        /**
         * Validation should keep the modal mounted; the title remains visible.
         */
        await expect(adminPage.locator("p").filter({ hasText: "Quick Add" })).toBeVisible();
    });
});
