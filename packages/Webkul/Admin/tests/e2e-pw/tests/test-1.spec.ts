import { test, expect } from '@playwright/test';

test('test', async ({ page }) => {
  await page.locator('body').click();
  await page.locator('body').press('Enter');
  await page.getByRole('link', { name: ' Settings' }).click();
  await page.getByRole('link', { name: ' Tags Add, edit or delete' }).click();
  await page.getByRole('button', { name: 'Create Tag' }).click();
  await page.getByRole('textbox', { name: 'Name' }).click();
  await page.getByRole('textbox', { name: 'Name' }).fill('tagname ');
  await page.locator('span:nth-child(6) > .block').click();
  await page.getByRole('button', { name: 'Save Tag' }).click();
});