import { Page, Locator, expect } from '@playwright/test';
import { SettingsPage } from '../SettingsPage';
import path from 'path';
import fs from 'fs';
import { parse } from 'fast-csv';
import { format } from '@fast-csv/format';
export type EntityType = 'leads' | 'products' | 'persons';
export class DataTransferPage extends SettingsPage {
    readonly page: Page;

    // Locators
    readonly importPageUrl = 'admin/settings/data-transfer/imports';
    readonly createImportLink: Locator;
    readonly fileInput: Locator;
    readonly processQueueCheckbox: Locator;
    readonly processQueueInput: Locator;
    readonly entityTypeSelect: Locator;
    readonly saveImportButton: Locator;
    readonly validateButton: Locator;
    readonly runImportButton: Locator;
    readonly importRecordsText: Locator;
    readonly editIcon: Locator;
    readonly deleteIcon: Locator;
    readonly confirmDeleteButton: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.createImportLink = page.locator('a.primary-button');
        this.fileInput = page.locator('input[name="file"]');
        this.processQueueCheckbox = page.locator('label[for="process_in_queue"]');
        this.processQueueInput = page.locator('input[name="process_in_queue"]');
        this.entityTypeSelect = page.locator('#import-type');
        this.saveImportButton = page.getByRole('button', { name: 'Save Import' });
        this.validateButton = page.locator('//button[contains(.,"Validate")]');
        this.runImportButton = page.getByRole('button', { name: 'Import' });
        this.importRecordsText = page.getByRole('paragraph').filter({ hasText: /^leads$/ });
        this.editIcon = page.locator('.icon-edit').first();
        this.deleteIcon = page.locator('.icon-delete').first();
        this.confirmDeleteButton = page.getByRole('button', { name: 'Agree', exact: true });
                
    }


    async createImport(csvFilePath: string, entityType: EntityType) {

        await this.createImportLink.click();
        await this.setInputFiles('input[name="file"]', csvFilePath);
        await this.uncheckProcessQueue();

        await this.entityTypeSelect.selectOption(entityType);
        await this.saveImportButton.click();

        await this.page.locator('//button[contains(.,"Validate")]').click();
        await this.runImportButton.click();

        // Wait for success record appearance
        await expect(this.successMessage.first()).toBeVisible();
    }
    async updateImport(csvFilePath: string) {
        await this.editIcon.click();
        await this.setInputFiles('input[name="file"]', csvFilePath);
        await this.uncheckProcessQueue();
        await this.saveImportButton.click();

        // Validation and import
        await this.page.locator('//button[@class="primary-button place-self-start"]').click();
        await expect(this.page.getByText('Your import is valid. Click')).toBeVisible();

        await this.runImportButton.click();

        // Wait for success message
        await expect(this.page.getByText('Congratulations! Your import')).toBeVisible();
    }

    async deleteImport() {

        await this.deleteIcon.click();
        await this.confirmDeleteButton.click();
        // Confirm deletion and presence of 'No Records Available.'
        await expect(this.successMessage.first()).toBeVisible();
    }

    /**
     * The import must run synchronously in every test, otherwise the records are
     * only queued and the assertions race the queue worker. The switch is toggled
     * through its label because the real checkbox is visually hidden, and it is
     * clicked only when checked so the state stays deterministic on both the
     * create form (off by default) and the edit form (reflects the saved import).
     */
    async uncheckProcessQueue() {
        await this.processQueueInput.waitFor({ state: 'attached' });

        if (await this.processQueueInput.isChecked()) {
            await this.processQueueCheckbox.click();
        }

        await expect(this.processQueueInput).not.toBeChecked();
    }

    // Utility method for setting files, with fallback to Playwright's method
    async setInputFiles(selector: string, filePath: string) {
        await this.page.setInputFiles(selector, filePath);
    }

async updateCsv(filePath: string,column:string, newOrgId: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const rows: any[] = [];
    
    fs.createReadStream(filePath)
      .pipe(parse({ headers: true }))
      .on('error', reject)
      .on('data', (row) => {
        row[column] = newOrgId; // only update this field
        rows.push(row);
      })
      .on('end', () => {
        const writeStream = fs.createWriteStream(filePath);
        const csvStream = format({ headers: true });
        
        csvStream
          .pipe(writeStream)
          .on('finish', resolve)
          .on('error', reject);
        
        rows.forEach(row => csvStream.write(row));
        csvStream.end();
      });
  });
}



}
