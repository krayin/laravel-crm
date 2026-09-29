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
    async updateGroup(groupData:GroupData): Promise<void>
    {
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
