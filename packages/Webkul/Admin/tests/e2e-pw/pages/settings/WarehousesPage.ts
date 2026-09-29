// pages/WarehousePage.ts
import { Page, Locator, expect } from '@playwright/test';
import { SettingsPage } from '../SettingsPage';
import { generateDescription, generateEmail, generateLastName, generateLocation, generateName, generatePhoneNumber, generateRandomNumericString } from '../../utils/faker';

export type WarehouseData = {
    name: string;
    description: string;
    contactName: string;
    contactEmail: string;
    contactAddress: string;
    country: string;
    state: string;
    city: string;
    zipcode: string;
    phone: string;
};

export const warehouseData: WarehouseData = {
    name: generateLastName(),
    description: generateDescription().slice(0,20),
    contactName: generateLastName(),
    contactEmail:generateEmail(),
    contactAddress: 'Plot 123, Industrial Area, Meerut, UP 250001',
    country: 'India',
    state: 'Uttar Pradesh',
    city: 'Meerut',
    zipcode: '250001',
    phone: generatePhoneNumber(),
};

export class WarehousePage extends SettingsPage {
    readonly page: Page;
    readonly warehouseCreateLink: Locator;
    readonly warehouseNameInput: Locator;
    readonly warehouseAddressInput: Locator;
    readonly saveButton: Locator;
    readonly firstEditButton: Locator;
    readonly firstDeleteButton: Locator;
    readonly successMessage: Locator;
    readonly warehousesTable: Locator;
    readonly contactNameInput: Locator;
    readonly contactEmailInput: Locator;
    readonly contactPhoneInput: Locator;
    readonly contactAddressTextarea: Locator;
    readonly countrySelect: Locator;
    readonly stateSelect: Locator;
    readonly cityInput: Locator;
    readonly zipcodeInput: Locator;
    readonly warehouseDescriptionInput: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.warehouseCreateLink = page.getByRole('link', { name: 'Create Warehouse' })
        this.warehouseNameInput = page.getByRole('textbox', { name: 'Name *', exact: true });
        this.warehouseAddressInput = page.locator('textarea[name="contact_address[address]"]');
        this.saveButton = page.getByRole('button', { name: 'Save Warehouse' })
        this.firstEditButton = page.locator('span.cursor-pointer.icon-edit').first();
        this.firstDeleteButton = page.locator('span.cursor-pointer.icon-delete').first();
        this.successMessage = page.getByText(/Warehouse.*successfully/);
        this.warehousesTable = page.getByRole('table');
        this.contactNameInput = page.getByRole('textbox', { name: 'Contact Name *' });
        this.contactEmailInput = page.getByRole('textbox', { name: 'Contact Emails *' })
        this.contactPhoneInput = page.getByRole('textbox', { name: 'Contact Numbers' })
        this.contactAddressTextarea = page.locator('textarea[name="contact_address[address]"]');
        this.countrySelect = page.locator('select[name="contact_address[country]"]');
        this.stateSelect = page.locator('select[name="contact_address[state]"]');
        this.cityInput = page.getByRole('textbox', { name: 'City' });
        this.zipcodeInput = page.getByRole('textbox', { name: 'Postcode' });
        this.warehouseDescriptionInput = page.getByRole('textbox', { name: 'Description' });
    }

    async warehouseForm(warehouseData: WarehouseData) {
        await this.warehouseNameInput.fill(warehouseData.name);
        await this.warehouseDescriptionInput.fill(warehouseData.description);
          await this.warehouseAddressInput.fill(warehouseData.contactAddress);
        await this.contactNameInput.fill(warehouseData.contactName);
        await this.contactEmailInput.fill(warehouseData.contactEmail);
        await this.contactPhoneInput.fill(warehouseData.phone);
        await this.contactAddressTextarea.fill(warehouseData.contactAddress);
        await this.countrySelect.selectOption({ label: warehouseData.country });
        await this.stateSelect.selectOption({ label: warehouseData.state });
        await this.cityInput.fill(warehouseData.city);
        await this.zipcodeInput.fill(warehouseData.zipcode);
        await this.saveButton.click();
        await expect(this.successMessage.first()).toBeVisible();
        await this.searchWarehouse(warehouseData.contactName);
        await expect(this.page.getByRole('paragraph').filter({ hasText: warehouseData.contactName }).first()).toBeVisible();
        
    }
    async searchWarehouse(warehouseName:string)
    {
        await this.searchInputExact.fill(warehouseName);
        await this.page.keyboard.press('Enter');
        
    }
    async deleteWarehouse()
    {
        await this.firstDeleteButton.click()
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }
}
