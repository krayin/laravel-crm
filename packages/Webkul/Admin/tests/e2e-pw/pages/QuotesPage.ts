import { expect, Locator, Page } from "@playwright/test";
import CoreLocators from "../locator/CoreLocators";
import { productData, ProductData } from "./ProductPage";
import { generateDescription, generateFullName } from "../utils/faker";
import PersonsPage, { PersonData, personData } from "./PersonsPage";
export type QuoteData = {
    subject: string;
    description: string;
    salesOwnerId: string;
    expiredAt: string; // expiration date string
    person: PersonData;
    leadName: string;
    address: string;
    countryCode: string; // e.g. 'IN'
    stateCode: string;   // e.g. 'DL'
    city: string;
    postcode: string;
    product: ProductData;
    // Add quote items type here if needed
};

export const quoteData: QuoteData = {
    subject: generateFullName(),
    description: generateDescription(),
    salesOwnerId: "1",
    expiredAt: "2026-02-28",
    person: personData,
    leadName: "Corporate Website Lead",
    address: "Plot 45, Industrial Area",
    countryCode: "IN",
    stateCode: "DL",
    city: "New Delhi",
    postcode: "110020",
    product: productData,
    // Optionally add quote items array if needed
};




export class QuotesPage extends CoreLocators {
    readonly page: Page;

    // Navigation links/buttons
    readonly createQuoteLink: Locator;
    readonly saveQuoteButton: Locator;

    // Form fields
    readonly subjectTextbox: Locator;
    readonly descriptionTextbox: Locator;
    readonly salesOwnerSelect: Locator;
    readonly expiredAtTextbox: Locator;

    // Person search and selection
    readonly searchTextbox: Locator;

    // Billing address fields
    readonly billingAddressTextarea: Locator;
    readonly billingCountrySelect: Locator;
    readonly billingStateSelect: Locator;
    readonly billingCityInput: Locator;
    readonly billingPostcodeInput: Locator;

    // Shipping address fields
    readonly shippingAddressTextarea: Locator;
    readonly shippingCountrySelect: Locator;
    readonly shippingStateSelect: Locator;
    readonly shippingCityInput: Locator;
    readonly shippingPostcodeInput: Locator;

    // Confirmation/Sucess notification locator

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.subjectTextbox = page.getByRole("textbox", { name: "Subject *" });
        this.descriptionTextbox = page.getByRole("textbox", { name: "Description" });
        this.searchTextbox = page.getByRole("textbox", { name: "Search..." });

        this.expiredAtTextbox = page.getByRole("textbox", { name: "Expired At *" });

        this.billingAddressTextarea = page.locator('textarea[name="billing_address\\[address\\]"]');
        this.shippingAddressTextarea = page.locator('textarea[name="shipping_address\\[address\\]"]');

        this.billingCityInput = page.locator('input[name="billing_address\\[city\\]"]');
        this.billingPostcodeInput = page.locator('input[name="billing_address\\[postcode\\]"]');
        this.shippingCityInput = page.locator('input[name="shipping_address\\[city\\]"]');
        this.shippingPostcodeInput = page.locator('input[name="shipping_address\\[postcode\\]"]');

        this.salesOwnerSelect = page.getByLabel("Sales Owner");
        this.billingCountrySelect = page.locator('select[name="billing_address\\[country\\]"]');
        this.billingStateSelect = page.locator('select[name="billing_address\\[state\\]"]');
        this.shippingCountrySelect = page.locator('select[name="shipping_address\\[country\\]"]');
        this.shippingStateSelect = page.locator('select[name="shipping_address\\[state\\]"]');

        this.saveQuoteButton = page.getByRole("button", { name: "Save Quote" });

        this.createQuoteLink = page.getByRole("link", { name: "Create Quote" });


        this.createQuoteLink = page.getByRole("link", { name: "Create Quote" });


    }
    async navigateToQuotesPage() {
        await this.page.goto("admin/quotes");
    }

    async createQuote(quoteData: QuoteData) {


        await this.navigateToQuotesPage();
        // Fill Quote Basics
        await this.createQuoteLink.click();
        await this.subjectTextbox.fill(quoteData.subject);
        await this.descriptionTextbox.fill(quoteData.description);
        await this.salesOwnerSelect.selectOption(quoteData.salesOwnerId);
        await this.expiredAtTextbox.fill(quoteData.expiredAt);

        // Link to Person - Click to add and select person by name
        await this.quoteAddPersonButton.click();
        await this.searchTextbox.fill(quoteData.person.name);
        await this.page.waitForTimeout(1000);
        await this.quoteSelectListPerson.click(); // or select existing if applicable

        // // Link to Lead - Click to add and select lead by name
        // await this.quoteLinkToLeadButton.click();
        // await this.searchTextbox.fill(quoteData.leadName);
        // await this.addAsNewButton.click(); // or select existing

        // Fill Billing Address
        await this.billingAddressTextarea.fill(quoteData.address);
        await this.billingCountrySelect.selectOption(quoteData.countryCode);
        await this.billingStateSelect.selectOption(quoteData.stateCode);
        await this.billingCityInput.fill(quoteData.city);
        await this.billingPostcodeInput.fill(quoteData.postcode);

        // Fill Shipping Address
        await this.shippingAddressTextarea.fill(quoteData.address);
        await this.shippingCountrySelect.selectOption(quoteData.countryCode);
        await this.shippingStateSelect.selectOption(quoteData.stateCode);
        await this.shippingCityInput.fill(quoteData.city);
        await this.shippingPostcodeInput.fill(quoteData.postcode);

        // Quote item filling
        await this.page.getByText('Click to Add', { exact: true }).first().click();
        await this.page.getByPlaceholder("Search Products").fill(quoteData.product.name);
        await this.page.waitForTimeout(1000);
        await this.page.getByText(quoteData.product.name, { exact: true }).first().click();

        // Save the quote
        await this.saveQuoteButton.click();

    }
    async updateQuote(quoteData:QuoteData)
    {
        await this.firstEditIcon.click();
        await this.subjectTextbox.fill(quoteData.subject);
        await this.descriptionTextbox.fill(quoteData.description);
        await this.salesOwnerSelect.selectOption(quoteData.salesOwnerId);
        await this.expiredAtTextbox.fill(quoteData.expiredAt);

        // Link to Person - Click to add and select person by name
        await this.quoteAddPersonButton.click();
        await this.searchTextbox.fill(quoteData.person.name);
        await this.page.waitForTimeout(1000);
        await this.quoteSelectListPerson.click(); // or select existing if applicable

        // Link to Lead - Click to add and select lead by name
        // await this.quoteLinkToLeadButton.click();
        // await this.searchTextbox.fill(quoteData.leadName);
        // await this.addAsNewButton.click(); // or select existing

        // Fill Billing Address
        await this.billingAddressTextarea.fill(quoteData.address);
        await this.billingCountrySelect.selectOption(quoteData.countryCode);
        await this.billingStateSelect.selectOption(quoteData.stateCode);
        await this.billingCityInput.fill(quoteData.city);
        await this.billingPostcodeInput.fill(quoteData.postcode);

      

    


        // Save the quote
        await this.saveQuoteButton.click();


    }
    async deleteQuote()
    {
        await this.firstDeleteIcon.click();
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }
}
