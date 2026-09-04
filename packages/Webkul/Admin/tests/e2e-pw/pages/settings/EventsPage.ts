import { Page, Locator, expect } from '@playwright/test';
import { SettingsPage } from '../SettingsPage';
import { generateDescription, generateFullName, generateRandomDateTime } from '../../utils/faker';

export type EventData = {
    name: string,
    description: string,
    date: string
}
export const eventdata: EventData = {
    name: generateFullName(),
    description: generateDescription(),
    date: "2027-11-18",
}

export class EventsPage extends SettingsPage {
    readonly page: Page;
    readonly createEventButton: Locator;
    readonly nameInput: Locator;
    readonly descriptionTextarea: Locator;
    readonly dateInput: Locator;
    readonly saveEventButton: Locator;
    readonly editFirstEventButton: Locator;
    readonly deleteFirstEventButton: Locator;
    readonly confirmDeleteButton: Locator;
    readonly successMessage: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.createEventButton = page.getByRole('button', { name: 'Create Event' });
        this.nameInput = page.locator('input[name="name"]');
        this.descriptionTextarea = page.locator('textarea[name="description"]');
        this.dateInput = page.locator('input[name="date"]');
        this.saveEventButton = page.getByRole('button', { name: 'Save Event' });
        this.editFirstEventButton = page.locator('.row > div:nth-child(6) > a').first();
        this.deleteFirstEventButton = page.locator('div:nth-child(6) > a:nth-child(2)').first();
        this.confirmDeleteButton = page.getByRole('button', { name: 'Agree', exact: true });
        this.successMessage = page.getByText(/Event (created|updated|deleted) successfully./);
    }

    async createEvent(eventdata: EventData) {

        await this.createEventButton.click();
        await this.nameInput.fill(eventdata.name);
        await this.descriptionTextarea.fill(eventdata.name);
        await this.dateInput.fill(eventdata.date);
        await this.saveEventButton.click();
        await expect(this.successMessage).toBeVisible();

    }
    async massDeleteEvents() {
        await this.multiSelectCheckbox.click()
        await this.deleteButton.click();
        await this.agreeButton.click();
        await expect(this.noRecordsAvailable).toBeVisible();

    }
    async editEvents(eventData: EventData) {

        await this.firstEditIcon.click();
        await this.nameInput.fill(eventdata.name);
        await this.descriptionTextarea.fill(eventdata.name);
        await this.dateInput.fill(eventdata.date);
        await this.saveEventButton.click();
        await expect(this.successMessage).toBeVisible();
    }
    async deleteEvent() {
        await this.deleteFirstEventButton.click();
        await this.agreeButton.click();
        await expect(this.successMessage).toBeVisible();

    }
    async eventSearch(name: string) {
        await this.searchInputExact.fill(name);
        await this.page.keyboard.press('Enter');

    }



}
