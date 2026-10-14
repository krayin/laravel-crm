// groupPage.js
import { expect, Locator, Page } from '@playwright/test';
import { SettingsPage } from '../SettingsPage';
import { generateDescription, generateFirstName, generateName } from '../../utils/faker';
export type GroupData={
    name:string,
    description:string
}
export const groupData:GroupData={
    name: generateFirstName()+" Group",
    description: generateDescription().slice(0, 60),
}

export class GroupPage extends SettingsPage {
    readonly page:Page;
    readonly createGroupButton: Locator;
    readonly saveGroupButton: Locator;
    readonly nameInput: Locator;
    readonly descriptionTextarea: Locator;
    readonly successMessageGroupCreated: Locator;
    readonly successMessageGroupUpdated: Locator;

    constructor(page:Page) {
        super(page);
        this.page = page;
        
        // Buttons
        this.createGroupButton = page.getByRole("button", { name: "Create group" });
        this.saveGroupButton = page.getByRole("button", { name: "Save Group" });
        
        // Form fields
        this.nameInput = page.locator('input[name="name"]');
        this.descriptionTextarea = page.locator('textarea[name="description"]');
        
        // Notifications
        this.successMessageGroupCreated = page.getByText("Group created successfully.");
        this.successMessageGroupUpdated = page.getByText("Group updated successfully.");
    }
    async createGroup(groupData:GroupData): Promise<void>
    {
        await this.createGroupButton.click();
        await this.nameInput.fill(groupData.name);
        await this.descriptionTextarea.fill(groupData.description);
        await this.saveGroupButton.click();
        await expect(this.successMessageGroupCreated.first()).toBeVisible();
        await this.searchGroup(groupData.name);
        await expect(this.page.getByText(groupData.name).first()).toBeVisible();
    }
    /**
     * `searchFor` is the group to edit; `groupData` is what it becomes.
     *
     * Clicking the first edit icon on an unfiltered grid edited whichever group happened to sort
     * first — not the one the create test made — so the update either hit a record the suite does
     * not own or raced the grid's own load and found no icon at all. Searching first pins the edit
     * to the intended record.
     */
    async updateGroup(groupData:GroupData, searchFor?:string): Promise<void>
    {
        if (searchFor) {
            await this.searchGroup(searchFor);
        }

        await expect(this.firstEditIcon).toBeVisible();

        await this.firstEditIcon.click();
        await this.nameInput.fill(groupData.name);
        await this.descriptionTextarea.fill(groupData.description);
        await this.saveGroupButton.click();
        await expect(this.successMessageGroupUpdated.first()).toBeVisible();
        await this.searchGroup(groupData.name);
        await expect(this.page.getByText(groupData.name).first()).toBeVisible();
    }
    async deleteGroup(): Promise<void>
    {
        await this.firstDeleteIcon.click();
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }
    async searchGroup(name:string): Promise<void>
    {
        await this.searchInputExact.fill(name);
        await this.page.keyboard.press('Enter');

    }
}
