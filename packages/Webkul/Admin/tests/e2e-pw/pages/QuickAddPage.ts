import { Page, expect } from "@playwright/test";
import CoreLocators from "../locator/CoreLocators";
import {
    generateDescription,
    generateEmail,
    generateFullName,
    generateName,
    generatePhoneNumber,
    generateRandomNumericString,
    generateSKU,
} from "../utils/faker";

export type QuickAddTab = "Lead" | "Person" | "Organization" | "Product" | "Email";

export type QuickAddLeadData = {
    title: string;
    description: string;
};

export type QuickAddPersonData = {
    name: string;
    email: string;
    contactNumber: string;
};

export type QuickAddOrganizationData = {
    name: string;
};

export type QuickAddProductData = {
    name: string;
    description: string;
    sku: string;
    quantity: string;
    price: string;
};

export type QuickAddMailData = {
    replyTo: string;
    subject: string;
    body: string;
};

export const quickAddLeadData: QuickAddLeadData = {
    title: generateName(),
    description: generateDescription(),
};

export const quickAddPersonData: QuickAddPersonData = {
    name: generateFullName(),
    email: generateEmail(),
    contactNumber: generatePhoneNumber(),
};

export const quickAddOrganizationData: QuickAddOrganizationData = {
    name: generateName() + " Inc",
};

export const quickAddProductData: QuickAddProductData = {
    name: "Product " + generateName(),
    description: generateDescription(),
    sku: generateSKU(),
    quantity: generateRandomNumericString(2, 10, 50),
    price: generateRandomNumericString(3, 100, 500),
};

export const quickAddMailData: QuickAddMailData = {
    replyTo: generateEmail(),
    subject: generateName(),
    body: generateDescription().slice(0, 50),
};

export class QuickAddPage extends CoreLocators {
    readonly page: Page;

    constructor(page: Page) {
        super(page);
        this.page = page;
    }

    async openQuickAddModal() {
        await this.page.goto("admin/dashboard");
        await this.quickAddTrigger.waitFor({ state: "visible" });
        await this.quickAddTrigger.click();
        await expect(this.quickAddModalTitle).toBeVisible();
    }

    async selectQuickAddTab(tabLabel: QuickAddTab) {
        await this.quickAddTab(tabLabel).click();
    }

    async submitQuickAdd() {
        await this.quickAddSaveButton.click();
    }

    /**
     * Every quick add tab exposed by the seeded admin role must be reachable.
     */
    async verifyQuickAddTabs(tabs: QuickAddTab[]) {
        await this.openQuickAddModal();

        for (const tab of tabs) {
            await expect(this.quickAddTab(tab)).toBeVisible();
        }
    }

    async createLead(leadData: QuickAddLeadData) {
        await this.openQuickAddModal();
        await this.selectQuickAddTab("Lead");

        await this.quickAddLeadTitleInput.fill(leadData.title);
        await this.quickAddLeadDescriptionTextarea.fill(leadData.description);

        await this.submitQuickAdd();

        await expect(this.quickAddLeadSuccessMsg).toBeVisible();

        await this.page.goto("admin/leads");
        await this.searchInput.fill(leadData.title);
        await this.page.keyboard.press("Enter");
        await expect(this.page.getByText(leadData.title).first()).toBeVisible();
    }

    async createPerson(personData: QuickAddPersonData) {
        await this.openQuickAddModal();
        await this.selectQuickAddTab("Person");

        await this.quickAddPersonNameInput.fill(personData.name);
        await this.quickAddPersonEmailInput.fill(personData.email);
        await this.quickAddPersonContactInput.fill(personData.contactNumber);

        await this.submitQuickAdd();

        await expect(this.quickAddPersonSuccessMsg).toBeVisible();

        await this.page.goto("admin/contacts/persons");
        await this.searchByName(personData.name);
        await expect(this.page.getByText(personData.name).first()).toBeVisible();
    }

    async createOrganization(organizationData: QuickAddOrganizationData) {
        await this.openQuickAddModal();
        await this.selectQuickAddTab("Organization");

        await this.quickAddOrgNameInput.fill(organizationData.name);

        await this.submitQuickAdd();

        await expect(this.quickAddOrgSuccessMsg).toBeVisible();

        await this.page.goto("admin/contacts/organizations");
        await this.searchByName(organizationData.name);
        await expect(this.page.getByText(organizationData.name).first()).toBeVisible();
    }

    async createProduct(productData: QuickAddProductData) {
        await this.openQuickAddModal();
        await this.selectQuickAddTab("Product");

        await this.quickAddProductNameInput.fill(productData.name);
        await this.quickAddProductDescriptionTextarea.fill(productData.description);
        await this.quickAddProductSkuInput.fill(productData.sku);
        await this.quickAddProductQuantityInput.fill(productData.quantity);
        await this.quickAddProductPriceInput.fill(productData.price);

        await this.submitQuickAdd();

        await expect(this.quickAddProductSuccessMsg).toBeVisible();

        await this.page.goto("admin/products");
        await this.searchByName(productData.name);
        await expect(this.page.getByText(productData.name).first()).toBeVisible();
    }

    async createMail(mailData: QuickAddMailData) {
        await this.openQuickAddModal();
        await this.selectQuickAddTab("Email");

        await this.quickAddMailReplyToInput.fill(mailData.replyTo);
        await this.quickAddMailSubjectInput.fill(mailData.subject);
        await this.quickAddMailEditorFrame.locator("body").fill(mailData.body);

        await this.submitQuickAdd();

        await expect(this.quickAddMailSuccessMsg).toBeVisible();
    }

    /**
     * Validation errors must keep the modal mounted instead of closing it.
     */
    async submitEmptyForm(tabLabel: QuickAddTab) {
        await this.openQuickAddModal();
        await this.selectQuickAddTab(tabLabel);

        await this.submitQuickAdd();

        await expect(this.quickAddModalTitle).toBeVisible();
    }
}
