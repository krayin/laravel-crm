import { expect, Locator, Page } from "playwright/test";
import { SettingsPage } from "../SettingsPage";

export class SourcesPage extends SettingsPage {
    readonly page: Page;
    readonly createSourceLink:Locator;
    readonly saveSourceButton:Locator;
    readonly thisName:Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.createSourceLink=page.getByRole('button', { name: 'Create Source' });
        this.saveSourceButton=page.getByRole('button', { name: 'Save Source' })
        this.thisName=page.getByRole('textbox', { name: 'Name' });
    }

    async searchSource(thisName:string)
    {
        await this.searchInputExact.fill(thisName)
        await this.page.keyboard.press('Enter');

    }
    async sourceForm(sourceName:string)
    {
        await this.thisName.fill(sourceName);
        await this.saveSourceButton.click();
        await expect(this.successMessage.first()).toBeVisible();
        await this.searchSource(sourceName);
        await expect(this.page.getByRole('paragraph').filter({ hasText: sourceName }).first()).toBeVisible();
    }
    async deleteSource()
    {
        await this.firstDeleteIcon.click()
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }
    
}
