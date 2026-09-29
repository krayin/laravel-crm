import { expect, Page, Locator } from '@playwright/test';
import { generateName } from '../../utils/faker';
import { SettingsPage } from '../SettingsPage';


export class TagsPage extends SettingsPage {
  readonly page: Page;

  // Locators
  readonly gotoTagsPageButton: Locator;
  readonly createTagButton: Locator;
  readonly tagNameInput: Locator;
  readonly saveTagButton: Locator;
  readonly tagsUpdatedMessage: Locator;
  readonly tagsCreatedMessage: Locator;
  readonly tagsDeletedMessage: Locator;
  readonly firstEditButton: Locator;
  readonly firstDeleteButton: Locator;
  readonly confirmDeleteButton: Locator;

  constructor(page: Page) {
    super(page);
    this.page = page;
    // Initialize locators
    this.gotoTagsPageButton = page.getByRole('link', { name: /Tags/i });
    this.createTagButton = page.getByRole('button', { name: 'Create Tag' });
    this.tagNameInput = page.getByRole('textbox', { name: 'Name' });
    this.saveTagButton = page.getByRole('button', { name: 'Save Tag' });
    this.tagsCreatedMessage = page.getByText(/Tag created successfully\./);
    this.tagsUpdatedMessage = page.getByText(/Tag updated successfully\./);
    this.tagsDeletedMessage = page.getByText(/Tag deleted successfully\./);

    this.firstEditButton = page.locator('//span[@class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"]').first();
    this.firstDeleteButton = page.locator('//span[@class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center"]').first();
    this.confirmDeleteButton = page.getByRole('button', { name: 'Agree', exact: true });
  }

  async createTag(name:string) {
    await this.createTagButton.click();
    await this.tagNameInput.fill(name);
    await this.saveTagButton.click();
    await expect(this.tagsCreatedMessage).toBeVisible();
  }

  async udpateTag(newName: string) {
    await this.firstEditButton.click();
    await this.tagNameInput.fill(newName);
    await this.saveTagButton.click();
    await expect(this.tagsUpdatedMessage).toBeVisible();
  }

  async deleteTag() {
    await this.firstDeleteButton.click();
    await this.confirmDeleteButton.click();
    await expect(this.tagsDeletedMessage).toBeVisible();
  }
  async seacrhTag(name:string): Promise<void>
  {
      await this.searchInputExact.fill(name);
      await this.page.keyboard.press('Enter');
  }

  // Optional: Any utility methods like search, verify list, etc., can be added here.
}
