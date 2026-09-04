import { Page, Locator, expect } from '@playwright/test';
import { generateFullName, generateLastName, generateRandomNumericString } from '../../utils/faker';
import { SettingsPage } from '../SettingsPage';
import { eventdata, EventData } from './EventsPage';

export type CampaignData = {
    name: string;
    subject: string;
    emailTemplateId: string;
    event: EventData;
    active: number;
}
export const campaignData: CampaignData = {
    name: generateFullName(),
    subject: generateLastName(),
    emailTemplateId: generateRandomNumericString(),
    event: eventdata,
    active: 1,
}

export class CampaignsPage extends SettingsPage {
    readonly page: Page;

    // Campaign locators
    readonly createCampaignButton: Locator;
    readonly campaignNameInput: Locator;
    readonly campaignSubjectInput: Locator;
    readonly emailTemplateDropdown: Locator;
    readonly eventDropdown: Locator;
    readonly activeToggle: Locator;
    readonly saveCampaignButton: Locator;
    readonly firstCampaignEditButton: Locator;
    readonly firstCampaignDeleteButton: Locator;
    readonly confirmDeleteButton: Locator;
    readonly successCreatedMessage: Locator;
    readonly successUpdatedMessage: Locator;
    readonly successDeletedMessage: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;

        this.createCampaignButton = page.getByRole('button', { name: 'Create Campaigns' })
        this.campaignNameInput = page.locator('input[name="name"]');
        this.campaignSubjectInput = page.getByRole('textbox', { name: 'Subject *' });
        this.emailTemplateDropdown = page.locator('#marketing_template_id');
        this.eventDropdown = page.getByLabel('Event');
        this.activeToggle = page.locator('.peer.h-5');
        this.saveCampaignButton = page.getByRole('button', { name: 'Save Activity' });

        this.firstCampaignEditButton = page.locator('.row > div:nth-child(6) > a').first();
        this.firstCampaignDeleteButton = page.locator('xpath=(//span[contains(@class, "icon-delete")])[1]');
        this.confirmDeleteButton = page.getByRole('button', { name: 'Agree', exact: true });

        this.successCreatedMessage = page.getByText(/(created) successfully\./).first();
        this.successUpdatedMessage = page.getByText(/(updated) successfully\./).first();
        this.successDeletedMessage = page.getByText(/(deleted) successfully\./).first();
    }

    async gotoCampaigns() {
        await this.page.goto("admin/settings/marketing/campaigns");
    }

    async createCampaign(data: CampaignData) {
        await this.createCampaignButton.click();
        await this.campaignNameInput.fill(data.name);
        await this.campaignSubjectInput.fill(data.subject);
        await this.campaignSubjectInput.click(); // For validation if needed
        await this.campaignSubjectInput.fill(data.subject);

        await this.eventDropdown.selectOption({ label: data.event.name });
        await this.emailTemplateDropdown.selectOption({ value: "1" });


        if (data.active) {
            // Only toggle if not already active, this might require check logic per app specifics
            await this.activeToggle.click();
        }

        await this.saveCampaignButton.click();
        await expect(this.successCreatedMessage).toBeVisible();
        await this.searchByName(data.name);
        await expect(this.page.getByText(data.name).first()).toBeVisible();
    }

    async editCampaign(data: CampaignData) {
        await this.firstCampaignEditButton.click();
        await this.campaignNameInput.fill(data.name);
        await this.campaignSubjectInput.fill(data.subject);
        await this.campaignSubjectInput.click(); // For validation if needed
        await this.campaignSubjectInput.fill(data.subject);

        await this.eventDropdown.selectOption({ label: data.event.name });
        await this.emailTemplateDropdown.selectOption({ value: "1" });


        if (data.active) {
            // Only toggle if not already active, this might require check logic per app specifics
            await this.activeToggle.click();
        }

        await this.saveCampaignButton.click();
        await expect(this.successUpdatedMessage).toBeVisible();
        await this.searchInputExact.fill(data.name);
        await this.page.keyboard.press('Enter');
        await expect(this.page.getByText(data.name).first()).toBeVisible();
    }

    async deleteCampaign() {
        await this.firstCampaignDeleteButton.click();
        await this.confirmDeleteButton.click();
        await expect(this.successDeletedMessage).toBeVisible();
    }
}
