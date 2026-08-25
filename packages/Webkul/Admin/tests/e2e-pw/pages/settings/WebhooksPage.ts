import { Page, Locator, expect } from '@playwright/test';
import { SettingsPage } from '../SettingsPage';
import { generateDescription, generateFirstName } from '../../utils/faker';

export type WebhookData = {
  url: string;
  name: string;
  description: string;
  entity: string;
};

export const webhookData: WebhookData = {
  url: 'https://example.com/webhook',
  name: generateFirstName(),
  description: generateDescription().slice(0, 50),
  entity: 'persons',
};  
export class WebhookPage extends SettingsPage {
  readonly page: Page;
  readonly createButton: Locator;
  readonly editButton: Locator;
  readonly deleteButton: Locator;
  readonly urlEndpointInput: Locator;
  readonly nameInput: Locator;
  readonly descriptionInput: Locator;
  readonly entityTypeSelect: Locator;
  readonly queryParamKeyInput: Locator;
  readonly queryParamValueInput: Locator;
  readonly contentTypeRadio: Locator;
  readonly payloadKeyInput: Locator;
  readonly payloadValueInput: Locator;
  readonly saveButton: Locator;
  readonly deleteConfirmButton: Locator;
  readonly listingUrl: string;

  constructor(page: Page) {
    super(page);
    this.page = page;
    this.listingUrl = 'admin/settings/webhooks';
    
    // Main actions
    this.createButton = page.getByRole('link', { name: 'Create Webhook' });
    this.editButton = page.locator('(//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-edit"])[1]');
    this.deleteButton = page.locator('(//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-delete"])[1]');
    
    // Form fields
    this.urlEndpointInput = page.getByRole('textbox', { name: 'Url Endpoint' });
    this.nameInput = page.getByRole('textbox', { name: 'Name' });
    this.descriptionInput = page.getByRole('textbox', { name: 'Description' });
    this.entityTypeSelect = page.locator('#entity_type');
    this.queryParamKeyInput = page.locator('input[name="query_params[0][key]"]');
    this.queryParamValueInput = page.locator('[id="query_params[0][value]"]');
    this.contentTypeRadio = page.getByRole('radio', { name: 'x-www-form-urlencoded' });
    this.payloadKeyInput = page.locator('input[name="payload[0][key]"]');
    this.payloadValueInput = page.locator('input[name="payload[0][value]"]');
    this.saveButton = page.getByRole('button', { name: 'Save Webhook' });
    this.deleteConfirmButton = page.getByRole('button', { name: 'Agree', exact: true });
  }


  async createWebhook(webhookData: WebhookData) {
    await this.createButton.click();
    await this.urlEndpointInput.fill(webhookData.url);
    await this.nameInput.fill(webhookData.name);
    await this.entityTypeSelect.selectOption(webhookData.entity);
    await this.descriptionInput.fill(webhookData.description);
    await this.queryParamKeyInput.fill('key');
    await this.queryParamValueInput.fill('value');
    await this.contentTypeRadio.check();
    await this.payloadKeyInput.fill('key');
    await this.payloadValueInput.fill('value');
    await this.saveButton.click();
    await expect(this.successMessage.first()).toBeVisible();
  }

  async updateFirstWebhook(webhookData: WebhookData) {
    await this.editButton.click();
    await this.urlEndpointInput.fill(webhookData.url);
    await this.nameInput.fill(webhookData.name);
    await this.descriptionInput.fill(webhookData.description);
    await this.entityTypeSelect.selectOption(webhookData.entity);
    await this.queryParamKeyInput.fill('updatedKey');
    await this.queryParamValueInput.fill('updatedValue');
    await this.payloadKeyInput.fill('updatedPayloadKey');
    await this.payloadValueInput.fill('updatedPayloadValue');
    await this.saveButton.click();
    await this.searchWebhook(webhookData.name);
    await expect(this.page.locator('p.break-words', { hasText: webhookData.url }).first()).toBeVisible();
  }

  async deleteWebhook() {
    await this.deleteButton.click();
    await this.deleteConfirmButton.click();
    await expect(this.successMessage.first()).toBeVisible();
  }
  async searchWebhook(name: string) {
    await this.searchInputExact.fill(name);
    await this.page.keyboard.press('Enter');
  }
}
