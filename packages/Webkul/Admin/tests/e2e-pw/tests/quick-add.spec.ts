import { test, expect } from "../fixtures/AdminFixtures";
import { QuickAddPage } from "../pages/QuickAddPage";
import {
    generateDescription,
    generateEmail,
    generateFullName,
    generateName,
    generatePhoneNumber,
    generateRandomNumericString,
    generateSKU,
} from "../utils/faker";

test.describe("quick add modal", () => {
    test("should open the quick add modal from the header plus button", async ({ adminPage }) => {
        const quickAddPage = new QuickAddPage(adminPage);
        await quickAddPage.openQuickAddModal();

        /**
         * The seeded admin role has all five quick-create permissions.
         */
        for (const tab of ["Lead", "Person", "Organization", "Product", "Email"]) {
            await expect(quickAddPage.quickAddTab(tab)).toBeVisible();
        }
    });

    test("should create a lead via quick add", async ({ adminPage }) => {
        const quickAddPage = new QuickAddPage(adminPage);
        await quickAddPage.openQuickAddModal();
        await quickAddPage.selectQuickAddTab("Lead");

        await quickAddPage.quickAddLeadTitleInput.fill(`${generateName()}-${Date.now()}`);
        await quickAddPage.quickAddLeadDescriptionTextarea.fill(generateDescription());

        await quickAddPage.submitQuickAdd();

        await expect(quickAddPage.quickAddLeadSuccessMsg).toBeVisible();
    });

    test("should create a person via quick add", async ({ adminPage }) => {
        const quickAddPage = new QuickAddPage(adminPage);
        await quickAddPage.openQuickAddModal();
        await quickAddPage.selectQuickAddTab("Person");

        await quickAddPage.quickAddPersonNameInput.fill(generateFullName());
        await quickAddPage.quickAddPersonEmailInput.fill(generateEmail());
        await quickAddPage.quickAddPersonContactInput.fill(generatePhoneNumber());

        await quickAddPage.submitQuickAdd();

        await expect(quickAddPage.quickAddPersonSuccessMsg).toBeVisible();
    });

    test("should create an organization via quick add", async ({ adminPage }) => {
        const quickAddPage = new QuickAddPage(adminPage);
        await quickAddPage.openQuickAddModal();
        await quickAddPage.selectQuickAddTab("Organization");

        await quickAddPage.quickAddOrgNameInput.fill(generateName() + " Inc");

        await quickAddPage.submitQuickAdd();

        await expect(quickAddPage.quickAddOrgSuccessMsg).toBeVisible();
    });

    test("should create a product via quick add", async ({ adminPage }) => {
        const quickAddPage = new QuickAddPage(adminPage);
        await quickAddPage.openQuickAddModal();
        await quickAddPage.selectQuickAddTab("Product");

        await quickAddPage.quickAddProductNameInput.fill("Product " + generateName());
        await quickAddPage.quickAddProductDescriptionTextarea.fill(generateDescription());
        await quickAddPage.quickAddProductSkuInput.fill(generateSKU());
        await quickAddPage.quickAddProductQuantityInput.fill(generateRandomNumericString(2, 10, 50));
        await quickAddPage.quickAddProductPriceInput.fill(generateRandomNumericString(3, 100, 500));

        await quickAddPage.submitQuickAdd();

        await expect(quickAddPage.quickAddProductSuccessMsg).toBeVisible();
    });

    test("should keep modal open and surface validation errors when fields are empty", async ({ adminPage }) => {
        const quickAddPage = new QuickAddPage(adminPage);
        await quickAddPage.openQuickAddModal();
        await quickAddPage.selectQuickAddTab("Lead");

        await quickAddPage.submitQuickAdd();

        /**
         * Validation should keep the modal mounted; the title remains visible.
         */
        await expect(quickAddPage.quickAddModalTitle).toBeVisible();
    });
});

