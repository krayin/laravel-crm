import { Page, Locator, FrameLocator, expect } from '@playwright/test';
import { SettingsPage } from '../SettingsPage';
import { generateDescription, generateName } from '../../utils/faker';

export type EmailTemplateData = {
  subject: string;
  content: string;
  name: string;
};

export const emailTemplateData: EmailTemplateData = {
  subject: generateName(),
  content: generateDescription(),
  name:  generateName(),
};
 export class EmailTemplatePage extends SettingsPage {
  readonly page: Page;
  readonly createButton: Locator;
  readonly editButton: Locator;
  readonly deleteButton: Locator;
  readonly subjectInput: Locator;
  readonly contentInput: FrameLocator;
  readonly nameInput: Locator;
  readonly placeholderSelect: Locator;
  readonly saveButton: Locator;
  readonly deleteConfirmButton: Locator;
  readonly listingUrl: string;

  constructor(page: Page) {
    super(page);
    this.page = page;
    this.listingUrl = 'admin/settings/email-templates';
    this.createButton = page.getByRole('link', { name: 'Create Email Template' });
    this.editButton = page.locator('//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-edit"]').first();
    this.deleteButton = page.locator('//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-delete"]').first();
    this.subjectInput = page.getByRole('textbox', { name: 'Subject' });
    this.contentInput = page.frameLocator(`iframe[id="content_ifr"]`);
    this.nameInput = page.getByRole('textbox', { name: 'Name' });
    this.placeholderSelect = page.locator('#placeholder');
    this.saveButton = page.getByRole('button', { name: 'Save Email Template' });
    this.deleteConfirmButton = page.getByRole('button', { name: 'Delete' });
  }

  async createTemplate(emailTemplateData: EmailTemplateData) {
    await this.createButton.click();
    await this.subjectInput.fill(emailTemplateData.subject);

    await this.nameInput.fill(emailTemplateData.name);
    await this.contentInput.locator('body').fill(emailTemplateData.content);
    await this.saveButton.click();
  }

  async updateEmailTemplate(emailTemplateData: EmailTemplateData) {
    await this.editButton.click();
    await this.subjectInput.fill(emailTemplateData.subject);
    await this.contentInput.locator('body').fill(emailTemplateData.content);
    await this.saveButton.click();
  }

  async deleteEmailTemplate() {
    await this.firstDeleteIcon.click();
    await this.agreeButton.click();
    await expect(this.successMessage.first()).toBeVisible();
  }
  async searchEmailTemplate(name: string) {
    await this.searchInputField.fill(name);
    await this.page.keyboard.press('Enter');
  }
}
