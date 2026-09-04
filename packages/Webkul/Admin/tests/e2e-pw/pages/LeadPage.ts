import { Page, Locator, expect } from "@playwright/test";
import CoreLocators from "../locator/CoreLocators";
import { personData, PersonData } from "./PersonsPage";
import { productData, ProductData, ProductPage } from "./ProductPage";
import { generateDescription, generateName } from "../utils/faker";


export type LeadData = {
    title: string;
    description: string;
    value: string;
    expectedCloseDate: string; // ISO date string
    person: PersonData;
    organizationName: string;
    product: ProductData;
};

export const leadData: LeadData = {
    title: generateName(),
    description: generateDescription(),
    value: (Math.floor(Math.random() * 10000)).toString(),
    expectedCloseDate: "2028-12-31",
    person: personData,
    product: productData,
    organizationName: personData.organizationName
};

export class LeadPage extends CoreLocators {
    readonly page: Page;

    constructor(page: Page) {
        super(page);
        this.page = page;
    }
    async navigateToLeadList() {
        await this.page.goto("admin/leads");
    }
    async getLeadByTitle(title: string) {
        return this.page.getByRole('link', { name: ` ${title}` });
    }
    async createLead(leadData: LeadData) {
        const productPage = new ProductPage(this.page);
        await productPage.navigateToProductPage();
        await productPage.createProductLink.click()
        await productPage.productForm(leadData.product);

        await this.navigateToLeadList();
        await this.createLeadButton.click();

        await this.titleInput.fill(leadData.title);
        await this.descriptionTextarea.fill(leadData.description);
        await this.sourceDropdown.selectOption("1");
        await this.typeDropdown.selectOption("1");
        await this.userDropdown.selectOption("1");
        await this.leadValueInput.fill(leadData.value);

        await this.addPersonButton.click();
        await this.leadPersonSearchInput.fill(leadData.person.name);
        await this.page.waitForTimeout(1000);
        const personItem = (await this.selectListItmeByName(leadData.person.name)).first();
        await this.page.waitForTimeout(1000);
        const isPersonAlreadyPresent = await personItem.isVisible();

        if (isPersonAlreadyPresent) {
            await personItem.click();
            console.log("person allready present");
        }
        else {

            await this.addAsNewButton.click();
            await this.personEmailInput.fill(leadData.person.emails);
            await this.personPhoneInput.fill(leadData.person.contactNumber);
            await this.addOrganizationButton.click();                 // this an issue add organiztion is not working if person allready present so that i have added this into else condition.
            await this.searchInputField.fill(leadData.title);
            await this.addAsNewButton.click();

        }
        // await this.leadProductAddMoreButton.click();
        await this.leadProductSelect.click();
        await this.leadProductSearchInput.fill(leadData.product.name);
        await this.page.waitForTimeout(1000);
        await this.page.locator(`//li[@class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"]`).first().click();
        (await this.getElementByTypeAndName('button', "Save")).click();
        await this.searchInput.fill(leadData.title);
        await this.page.keyboard.press('Enter');
        await expect(this.page.getByText(leadData.title).first()).toBeVisible();


        await expect(this.leadSuccessToast).toBeVisible();

    }
    async updateLead(leadData: LeadData) {


        // Now update the lead with new data

        // Fill updated lead data

        // Use locators from LeadPage via page1 context to fill fields
        await this.titleInput.fill(leadData.title);
        await this.descriptionTextarea.fill(leadData.description);
        await this.sourceDropdown.selectOption("1");
        await this.typeDropdown.selectOption("1");
        await this.userDropdown.selectOption("1");
        await this.leadValueInput.fill("1000");
             // await this.leadProductAddMoreButton.click();
        await this.leadProductSelect.click();
        await this.leadProductSearchInput.fill(leadData.product.name);
        await this.page.waitForTimeout(1000);
        await this.page.locator(`//li[@class="cursor-pointer px-4 py-2 text-gray-800 transition-colors hover:bg-blue-100 dark:text-white dark:hover:bg-gray-900"]`).first().click();
        (await this.getElementByTypeAndName('button', "Save")).click();
        await this.searchInput.fill(leadData.title);
        await this.page.keyboard.press('Enter');
        await expect(((await this.getLeadByTitle(leadData.title)).first())).toBeVisible();

        await expect(this.leadSuccessToast).toBeVisible();

    }

    async deleteLead(leadData: LeadData) {
        await this.listViewButton.click();
        await this.page.waitForTimeout(1000);

        await this.listViewLeadSearch.fill(leadData.title);
        await this.page.keyboard.press('Enter');
        await this.page.waitForTimeout(1000);

        await this.deleteLeadButton.isVisible();
        await this.deleteLeadButton.click();
        await this.agreeButton.click();

        await this.listViewLeadSearch.fill(leadData.title);
        await this.page.keyboard.press('Enter');
        await expect(this.deleteLeadButton).not.toBeVisible();
    }
    async searchLead(title: string) {
        await this.navigateToLeadList();
        await this.searchInput.fill(title);
        await this.page.keyboard.press('Enter');
        await this.page.waitForTimeout(1000);

        await (await this.getLeadByTitle(title)).first().click();
        await this.page.waitForLoadState('domcontentloaded');

        const page1Promise = this.page.waitForEvent('popup');
        await this.editLeadButton.click();
        const page1 = await page1Promise;
        const leadPage = new LeadPage(page1);
        return leadPage;
    }
    async listView() {
        await this.page.goto('admin/leads?view_type=table');
        await this.page.waitForLoadState('domcontentloaded');
    }


}
