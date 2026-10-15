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


    /**
     * Click Validate and wait for the result to come back.
     *
     * Validation runs over XHR and the "Your import is valid" banner is rendered behind `v-if`,
     * so it does not exist until the response lands. Clicking Import straight after Validate
     * raced that request: the Import button was still disabled (or the banner absent), and the
     * step failed on whichever of the two the caller happened to assert.
     */
    private async validateImport() {
        await this.validateButton.click();

        await expect(this.page.getByText('Your import is valid. Click')).toBeVisible();
    }

    async createImport(csvFilePath: string, entityType: EntityType) {
        await this.createImportLink.click();

        /**
         * The create screen is a full page load, so the form is waited for rather than assumed —
         * `setInputFiles` against a form that has not rendered yet fails on a missing input.
         */
        await expect(this.saveImportButton).toBeVisible();

        await this.setInputFiles('input[name="file"]', csvFilePath);
        await this.uncheckProcessQueue();

        await this.entityTypeSelect.selectOption(entityType);
        await this.saveImportButton.click();

        await this.validateImport();

        await this.runImportButton.click();

        // Wait for success record appearance
        await expect(this.successMessage.first()).toBeVisible();
    }

    async updateImport(csvFilePath: string) {
        await this.editIcon.click();

        await expect(this.saveImportButton).toBeVisible();

        await this.setInputFiles('input[name="file"]', csvFilePath);
        await this.uncheckProcessQueue();
        await this.saveImportButton.click();

        await this.validateImport();

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

/**
 * Rewrite the product id embedded in the leads CSV's `product` column.
 *
 * That column holds a compound value — `price=45,quantity=2,id=1` — and the importer validates its
 * `id` with `exists:products,id`. The fixture ships `id=1` and `id=2`, which do not exist on an
 * installation whose product ids start higher (ours start at 5), so validation fails with "The
 * selected id is invalid.", `is_valid` stays false, and the success banner the test waits for is
 * never rendered. Pointing the column at a product that actually exists is what makes the import
 * valid; `person_id` is already rewritten the same way for the same reason.
 */
async updateCsvProductId(filePath: string, productId: string): Promise<void> {
  return new Promise((resolve, reject) => {
    const rows: any[] = [];

    fs.createReadStream(filePath)
      .pipe(parse({ headers: true }))
      .on('error', reject)
      .on('data', (row) => {
        if (row.product) {
          row.product = String(row.product).replace(/\bid=\d+/, `id=${productId}`);
        }

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

/**
 * The id of a product that exists, read from the grid the admin UI itself uses.
 */
async firstProductId(): Promise<string> {
  const response = await this.page.request.get(
    'admin/products?pagination[page]=1&pagination[per_page]=1',
    { headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }
  );

  const records = (await response.json()).records ?? [];

  return String(records[0]?.id ?? '');
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
