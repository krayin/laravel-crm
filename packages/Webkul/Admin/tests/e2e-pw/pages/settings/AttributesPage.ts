import { expect, Locator, Page } from "playwright/test";
import { SettingsPage } from "../SettingsPage";
import { generateFirstName, generateRandomNumericString } from "../../utils/faker";

export type AttributeData = {
    name: string,
    code: string,
}
export const attributeData: AttributeData = {
    name: generateFirstName(),
    code: generateRandomNumericString(),

}
export class AttributesPage extends SettingsPage {


    readonly page: Page;
    readonly createAttributeLink: Locator;
    readonly nameTextbox: Locator;
    readonly codeTextbox: Locator;
    readonly typeDropdown: Locator;
    readonly entityTypeDropdown: Locator;
    readonly validationDropdown: Locator;
    readonly saveAttributeButton: Locator;
    readonly successMessage: Locator;
    readonly firstEditButton: Locator;
    readonly firstDeleteButton: Locator;
    readonly deleteConfirmButton: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.createAttributeLink = page.getByRole('link', { name: 'Create Attribute' });
        this.nameTextbox = page.getByRole('textbox', { name: 'Name' });
        this.codeTextbox = page.getByRole('textbox', { name: 'Code' });
        this.typeDropdown = page.locator('#type');
        this.entityTypeDropdown = page.locator('#entity_type');
        this.validationDropdown = page.locator('#validation');
        this.saveAttributeButton = page.getByRole('button', { name: 'Save Attribute' });
        this.successMessage = page.getByText('Success', { exact: true });
        this.firstEditButton = page.locator('//span[contains(@class,"icon-edit")]').first();
        this.firstDeleteButton = page.locator('//span[contains(@class,"icon-delete")]').first();
        this.deleteConfirmButton = page.getByRole('button', { name: 'Agree', exact: true });
    }

    async createAttribute(attributeData: AttributeData) {
        await this.createAttributeLink.click();
        await this.nameTextbox.fill(attributeData.name);
        await this.codeTextbox.fill(attributeData.name.toLowerCase());
        await this.typeDropdown.selectOption({ value: 'textarea' });
        await this.entityTypeDropdown.selectOption({ value: 'leads' });
        await this.saveAttributeButton.click();
        await this.searchAttribute(attributeData.name);
        await expect(this.page.getByText(attributeData.name).first()).toBeVisible();

    }
    async updateAttribute(attributeData: AttributeData) {
        await this.firstEditButton.click();
        await this.nameTextbox.fill(attributeData.name);

        await this.saveAttributeButton.click();
        await this.searchAttribute(attributeData.name);
        await expect(this.page.getByText(attributeData.name).first()).toBeVisible();
    }
    async searchAttribute(name: string) {
        await this.searchInputExact.fill(name);
        await this.page.keyboard.press('Enter');

    }
    async deleteAttribute()
    {
        await this.firstDeleteButton.click();
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }


}
