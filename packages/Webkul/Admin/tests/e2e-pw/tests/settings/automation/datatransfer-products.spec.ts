import { test, expect } from '../../../setup';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);


/* -------------------------- Import Management for Products -------------------------- */
test.describe('Import Management for Products', () => {
  const csvFileName = 'products.csv';
  const updatedCsvFileName = 'products_updated.csv';
  const csvPath = path.resolve(__dirname, `../../../data/sample/${csvFileName}`);
  const updatedCsvPath = path.resolve(__dirname, `../../../data/sample/${updatedCsvFileName}`);

  test('should create a new import and validate records', async ({ adminPage }) => {
    /* Navigate to Data Transfer section */
    await adminPage.goto('admin/settings/data-transfer/imports');

    /* Click 'Create Import' */
    await adminPage.getByRole('link', { name: 'Create Import' }).click();

    /* Upload CSV file */
    await adminPage.getByRole('button', { name: 'Choose File' }).setInputFiles(csvPath);
        /* Uncheck proccess in queue */
    await adminPage.locator('.peer.h-5').click(); // Adjust selector if multiple checkboxes present

    /* Select Entity Type (products) */
    
    await adminPage.locator('#import-type').selectOption('products'); // Adjust selector if multiple checkboxes present
   


    /* Save the import job */
    await adminPage.getByRole('button', { name: 'Save Import' }).click();

    /* Click 'Validate' */
    await adminPage.getByRole('button', { name: 'Validate' }).click();

    /* Wait for validation results */
    await expect(adminPage.getByText('Your import is valid. Click')).toBeVisible();
    await expect(adminPage.getByText('Total Rows Processed: 2')).toBeVisible();
    await expect(adminPage.getByText('Total Invalid Rows: 0')).toBeVisible();
    await expect(adminPage.getByText('Total Errors: 0')).toBeVisible();

    /* Execute the import */
    await adminPage.getByRole('button', { name: 'Import' }).click();

    /* Confirm import success */
    await expect(adminPage.getByText('Congratulations! Your import')).toBeVisible();
  });

  test('should update the import record (if editable)', async ({ adminPage }) => {
    /* Navigate to Import listing */
    await adminPage.goto('admin/settings/data-transfer/imports');

    /* Click edit icon for the first import */
    await adminPage.locator('.icon-edit').first().click();

    /* Re-upload updated CSV file */
    await adminPage.getByRole('button', { name: 'Choose File' }).setInputFiles(updatedCsvPath);

    /* Save changes */
    await adminPage.getByRole('button', { name: 'Save Import' }).click();

    /* Click 'Validate' */
    await adminPage.getByRole('button', { name: 'Validate' }).click();

    /* Wait for validation results */
    await expect(adminPage.getByText('Your import is valid. Click')).toBeVisible();

    /* Execute the import */
    await adminPage.getByRole('button', { name: 'Import' }).click();

    /* Confirm import success */
    await expect(adminPage.getByText('Congratulations! Your import')).toBeVisible();
  });

  test('should delete the import', async ({ adminPage }) => {
    /* Navigate to Import listing */
    await adminPage.goto('admin/settings/data-transfer/imports');

    /* Click delete icon for the first import */
    await adminPage.locator('.icon-delete').first().click();

    /* Confirm deletion */
    await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();

    /* Assert deleted file name is not visible */
    await expect(adminPage.getByText(updatedCsvFileName).first()).not.toBeVisible();
  });
});

