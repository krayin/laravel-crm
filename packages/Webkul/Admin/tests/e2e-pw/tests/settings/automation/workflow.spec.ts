import { test, expect } from '../../../setup';
import { generateDescription, generateName } from '../../../utils/faker';

test.describe('Workflow Management', () => {
  const workflowName = generateName();
  const updatedName = generateName();
  const workflowDescription = generateDescription();
  const updatedDescription = generateDescription();

  test('should create a workflow', async ({ adminPage }) => {
    /* Navigate to workflow listing page */
    await adminPage.goto('admin/settings/workflows');

    /* Click the 'Create Workflow' button */
    await adminPage.getByRole('link', { name: 'Create Workflow' }).click();

    /* Fill basic details */
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(workflowName);
    await adminPage.getByRole('textbox', { name: 'Description' }).fill(workflowDescription);

    /* Select event trigger */
    await adminPage.locator('select[name="event"]').selectOption('lead.update.after');

    /* Select condition type */
    await adminPage.locator('#condition_type').selectOption('and');

    /* Add condition */
    await adminPage.getByRole('button', { name: /Add Condition/ }).click();
    await adminPage.locator('[id="conditions[0][attribute]"]').selectOption('title');
    await adminPage.locator('input[name="conditions[0][value]"]').fill('title name');

    /* Add action */
    await adminPage.getByRole('button', { name: /Add Action/ }).click();
    await adminPage.locator('select[name="actions[0][id]"]').selectOption('add_tag');
    await adminPage.locator('input[name="actions[0][value]"]').fill('new tag');

    /* Save workflow */
    await adminPage.getByRole('button', { name: 'Save Workflow' }).click();

    /* Validate the workflow appears in the listing */
    await expect(adminPage.getByText(workflowName).first()).toBeVisible();
  });

  test('should update a workflow', async ({ adminPage }) => {
    /* Navigate to workflow listing */
    await adminPage.goto('admin/settings/workflows');

    /* Click edit icon on the first workflow */
    await adminPage.locator('(//span[contains(@class,"icon-edit")])[1]').click();

    /* Update name and description */
    await adminPage.getByRole('textbox', { name: 'Name' }).fill(updatedName);
    await adminPage.getByRole('textbox', { name: 'Description' }).fill(updatedDescription);

    /* Save updated workflow */
    await adminPage.getByRole('button', { name: 'Save Workflow' }).click();

    /* Verify updated name appears in list */
    await expect(adminPage.getByText(updatedName).first()).toBeVisible();
  });

  test('should delete a workflow', async ({ adminPage }) => {
    /* Navigate to workflow listing */
    await adminPage.goto('admin/settings/workflows');

    /* Click delete icon on first workflow */
    await adminPage.locator('(//span[contains(@class,"icon-delete")])[1]').click();

    /* Confirm delete in modal */
    await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();

    /* Confirm deleted workflow name no longer exists */
    await expect(adminPage.getByText(updatedName).first()).not.toBeVisible();
  });
});
