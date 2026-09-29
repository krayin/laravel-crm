import { test, expect } from '../../../setup';
import { generateUniqueWebhookUrl } from '../../../utils/faker';

test.describe('Webhook Management', () => {
  const originalUrl = generateUniqueWebhookUrl();
  const updatedUrl = generateUniqueWebhookUrl();
  const name = 'Test Webhook';
  const description = 'Webhook for automation';
  const entity = 'persons';

  test('should create a webhook', async ({ adminPage }) => {
    /* Navigate to the webhook listing page */
    await adminPage.goto('admin/settings/webhooks');

    /* Click the 'Create Webhook' button */
    await adminPage.getByRole('link', { name: 'Create Webhook' }).click();

    /* Fill the webhook creation form with required data */
    await adminPage.getByRole('textbox', { name: 'Url Endpoint' }).fill(originalUrl);
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(name);
    await adminPage.locator('#entity_type').selectOption(entity);
    await adminPage.getByRole('textbox', { name: 'Description' }).fill(description);
    await adminPage.locator('input[name="query_params[0][key]"]').fill('key');
    await adminPage.locator('[id="query_params[0][value]"]').fill('value');
    await adminPage.getByRole('radio', { name: 'x-www-form-urlencoded' }).check();
    await adminPage.locator('input[name="payload[0][key]"]').fill('key');
    await adminPage.locator('input[name="payload[0][value]"]').fill('value');

    /* Submit the form to save the webhook */
    await adminPage.getByRole('button', { name: 'Save Webhook' }).click();

    /* Verify that the webhook with originalUrl is visible in the list */
    await expect(adminPage.getByText(originalUrl).first()).toBeVisible();
  });

  test('should update the webhook', async ({ adminPage }) => {
    /* Define updated values for the webhook */
    const updatedName = 'Updated Webhook Name';
    const updatedDescription = 'Updated description for webhook';
    const updatedQueryKey = 'updatedKey';
    const updatedQueryValue = 'updatedValue';
    const updatedPayloadKey = 'updatedPayloadKey';
    const updatedPayloadValue = 'updatedPayloadValue';
    const updatedEntity = 'leads';

    /* Navigate to the webhook listing page */
    await adminPage.goto('admin/settings/webhooks');

    /* Click the edit icon of the first webhook in the list */
    await adminPage.locator('(//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-edit"])[1]').click();

    /* Update the webhook details */
    await adminPage.getByRole('textbox', { name: 'Url Endpoint' }).fill(updatedUrl);
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(updatedName);
    await adminPage.getByRole('textbox', { name: 'Description' }).fill(updatedDescription);
    await adminPage.locator('#entity_type').selectOption(updatedEntity);
    await adminPage.locator('input[name="query_params[0][key]"]').fill(updatedQueryKey);
    await adminPage.locator('[id="query_params[0][value]"]').fill(updatedQueryValue);
    await adminPage.locator('input[name="payload[0][key]"]').fill(updatedPayloadKey);
    await adminPage.locator('input[name="payload[0][value]"]').fill(updatedPayloadValue);

    /* Save the updated webhook */
    await adminPage.getByRole('button', { name: 'Save Webhook' }).click();

    /* Verify that the updated webhook URL is now visible in the list */
    await expect(adminPage.locator('p.break-words', { hasText: updatedUrl }).first()).toBeVisible();
  });

  test('should delete the webhook', async ({ adminPage }) => {
    /* Navigate to the webhook listing page */
    await adminPage.goto('admin/settings/webhooks');

    /* Click the delete icon of the first webhook in the list */
    await adminPage.locator('(//span[@class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800 max-sm:place-self-center icon-delete"])[1]').click();

    /* Confirm the deletion action in the modal */
    await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();

    /* Verify that the webhook with updated URL is no longer visible */
    await expect(adminPage.getByText(updatedUrl).first()).not.toBeVisible();
  });
});
