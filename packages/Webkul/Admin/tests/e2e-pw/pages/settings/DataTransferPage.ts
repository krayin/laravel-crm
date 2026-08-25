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
        this.processQueueCheckbox = page.locator('.peer.h-5');
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
        // Uncheck process in queue if needed
        await this.processQueueCheckbox.click();

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
