import { test, expect } from "../setup";
import fs from "fs";
import { generateName, getRandomDateTime, generateDescription, generateDate, generateEmail, generatePhoneNumber, generateEmailSubject } from '../utils/faker';

async function generateLead(adminPage) {
    /**
     * Go to the lead listing page.
     */
    await adminPage.goto("admin/leads");
    await adminPage.getByRole('link', { name: 'Create Lead' }).click();

    /**
     * Fill the lead form.
     */
    const leadTitle = `${generateName()}-${Date.now()}`;
    const leadDescription = generateDescription();
    const leadDate = generateDate();
    const leadEmail = generateEmail();
    const leadPhoneNumber = generatePhoneNumber();

    await adminPage.fill('input[name="title"]', leadTitle);
    await adminPage.fill('textarea[name="description"]', leadDescription);
    await adminPage.locator('select[name="lead_source_id"]').selectOption("1");
    await adminPage.fill('input[name="expected_close_date"]', leadDate);
    await adminPage.locator('select[name="lead_type_id"]').selectOption("1");
    await adminPage.locator('select[name="user_id"]').selectOption("1");
    await adminPage.fill('input[name="lead_value"]', '1000');

    /**
     * Add a new person via the lookup component.
     * The person lookup uses v-model.lazy + v-debounce="500" on its search input.
     * Playwright's fill() doesn't fire 'change', so we dispatch it manually
     * and wait for the debounce timer to update searchTerm.
     */
    const contactSection = adminPage.locator('#contact-person');

    await contactSection.getByText('Click to Add', { exact: true }).first().click();

    const personSearch = contactSection.locator('.absolute input[type="text"]').first();
    await personSearch.waitFor({ state: 'visible' });
    await personSearch.fill(leadTitle);
    await personSearch.dispatchEvent('change');
    await adminPage.waitForTimeout(600);

    await contactSection.getByText('Add as New').first().click();

    await adminPage.fill('input[name="person[emails][0][value]"]', leadEmail);
    await adminPage.fill('input[name="person[contact_numbers][0][value]"]', leadPhoneNumber);

    /**
     * Associate an organization via the lookup component.
     * Same v-model.lazy + v-debounce issue. Additionally, the org lookup's
     * "Add as New" only appears when searchTerm.length > 2 and no results found.
     */
    await contactSection.getByText('Click to add', { exact: true }).first().click();

    const orgSearch = contactSection.locator('.absolute input[type="text"]').first();
    await orgSearch.waitFor({ state: 'visible' });
    await orgSearch.fill(leadTitle);
    await orgSearch.dispatchEvent('change');

    const orgAddNew = contactSection.getByText('Add as New').first();
    await orgAddNew.waitFor({ state: 'visible' });
    await orgAddNew.click();

    /**
     * Remove the auto-added empty product row to avoid validation errors.
     * (Products are optional; the empty row has a required product_id rule.)
     */
    while (await adminPage.locator('#products .icon-delete').count() > 0) {
        await adminPage.locator('#products .icon-delete').first().click();
    }

    await adminPage.getByRole('button', { name: 'Save' }).click();

    /**
     * Successful creation redirects to the leads index page.
     */
    await adminPage.waitForURL(/\/admin\/leads(?:\?.*)?$/);

    return { leadTitle, leadDescription, leadDate, leadEmail, leadPhoneNumber };
}

function generateFile(fileName, content) {
    fs.writeFileSync(fileName, content);

    return fileName;
}

async function openLeadByTitle(adminPage, leadTitle) {
  await adminPage.goto("admin/leads");
  const searchInput = adminPage.getByRole("textbox", { name: "Search by Title" });
  await searchInput.fill(leadTitle);
  await searchInput.press("Enter");

  const leadLink = adminPage
    .locator('a[href*="/admin/leads/view/"]')
    .filter({ hasText: leadTitle })
    .first();

  await expect(leadLink).toBeVisible();
  await leadLink.click();
}

test.describe("lead management", () => { 
  test("should create a new lead", async ({ adminPage }) => {
    /**
     * Create a new lead.
     */
    await generateLead(adminPage);
  });

  test("should able to update lead", async ({ adminPage }) => {
    /**
    * Create a new lead.
    */
    const lead = await generateLead(adminPage);

    /**
    * Update the lead.
    */
    await openLeadByTitle(adminPage, lead.leadTitle);
    const page1Promise = adminPage.waitForEvent('popup');
    await adminPage.getByRole('link', { name: '' }).first().click();
    const page1 = await page1Promise;
    await page1.fill('textarea[name="description"]', generateDescription());
    await page1.fill('input[name="title"]', generateName());
    await page1.getByLabel('Source').selectOption('3');
    await page1.fill('input[name="lead_value"]', '30000');

    // Remove auto-added empty product rows on the edit page
    // (the edit page's v-product-list always adds an empty row).
    while (await page1.locator('#products .icon-delete').count() > 0) {
        await page1.locator('#products .icon-delete').first().click();
    }

    await page1.getByRole('button', { name: 'Save' }).click();

    /**
    * Successful update redirects to the leads index page.
    */
    await page1.waitForURL(/\/admin\/leads(?:\?.*)?$/);
  });

  test("should able to delete lead", async ({ adminPage }) => {
    /**
    * Create a new lead.
    */
    const lead = await generateLead(adminPage);

    /**
    * Delete the lead.
    */
    await adminPage.getByRole('link', { name: '' }).click();
    await adminPage.locator('div:nth-child(4) > .flex > span:nth-child(2)').click();
    await adminPage.getByRole('button', { name: 'Agree', exact: true }).click();
    await expect(adminPage.getByText('Success', { exact: true })).toBeVisible();
    await expect(adminPage.locator('#app')).toContainText('Lead deleted successfully.');
  });

  test("should sent a mail", async ({ adminPage }) => {
    /**
    * Create a new lead.
    */
    const lead = await generateLead(adminPage);

    /**
    * fill mail detail.
    */
    await openLeadByTitle(adminPage, lead.leadTitle);
    await adminPage.getByRole('button', { name: ' Mail' }).click();
    await adminPage.fill('input[name="temp-reply_to"]', generateEmail());
    await adminPage.fill('input[name="subject"]', generateEmailSubject());
    await adminPage.fill('textarea[name="reply"]', generateDescription());

    /**
    * Sending mail and closing the modal.
    */
    await adminPage.getByRole('button', { name: 'Send' }).click();

    await expect(adminPage.getByText('Email sent successfully.')).toBeVisible();
  });

  test("should able to upload file in lead", async ({ adminPage }) => {
    /**
     * Create a new lead.
     */
    const lead = await generateLead(adminPage);

    /**
     * fill the file detail or upload a file.
     */
    await openLeadByTitle(adminPage, lead.leadTitle);
    await adminPage.getByRole('button', { name: ' File' }).click();
    await adminPage.locator('input[name="title"]').fill(lead.leadTitle);
    await adminPage.locator('textarea[name="comment"]').fill(generateDescription());
    await adminPage.locator('input[name="name"]').fill(generateName());
    await adminPage.locator('#file').setInputFiles(generateFile('example.txt', 'Hello, this is a generated file!'));
    await adminPage.getByRole('button', { name: 'Save File' }).click();
  });

  test("should able to write a note in lead", async ({ adminPage }) => {
    /**
     * Create a new lead.
     */
    const lead = await generateLead(adminPage);

    /**
     * write a notes 
     */
    await openLeadByTitle(adminPage, lead.leadTitle);
    await adminPage.getByRole('button', { name: ' Note' }).click();
    await adminPage.locator('textarea[name="comment"]').fill(generateDescription());
    await adminPage.getByRole('button', { name: 'Save Note' }).click();
  });

  test("should able to add call activity in lead", async ({ adminPage }) => {
    /**
   * Create a new lead.
   */
    const lead = await generateLead(adminPage);

    /**
    * write a call activity detail 
    */
    await openLeadByTitle(adminPage, lead.leadTitle);
    await adminPage.getByRole('button', { name: ' Activity' }).click();
    await adminPage.getByRole('heading', { name: 'Add Activity - Call ' }).locator('span').click();
    await adminPage.getByText('Call', { exact: true }).click();
    await adminPage.locator('input[name="title"]').fill(lead.leadTitle);
    await adminPage.locator('textarea[name="comment"]').fill(generateDescription());
    await adminPage.locator('input[name="schedule_from"]').click();
    await adminPage.locator('input[name="schedule_from"]').fill(getRandomDateTime());
    await adminPage.locator('input[name="schedule_to"]').click();
    await adminPage.locator('input[name="schedule_to"]').fill(getRandomDateTime());
    await adminPage.locator('input[name="location"]').fill('call');
    await adminPage.getByRole('button', { name: 'Save Activity' }).click();
  });

  test("should able to add meeting activity in lead", async ({ adminPage }) => {
    /**
    * Create a new lead.
    */
    const lead = await generateLead(adminPage);

    /**
     * write a meeting activity detail 
     */
    await openLeadByTitle(adminPage, lead.leadTitle);
    await adminPage.getByRole('button', { name: ' Activity' }).click();
    await adminPage.getByRole('heading', { name: 'Add Activity' }).locator('span').click();
    await adminPage.getByText('Meeting', { exact: true }).click();
    await adminPage.locator('input[name="title"]').fill(lead.leadTitle);
    await adminPage.locator('textarea[name="comment"]').fill(generateDescription());
    await adminPage.locator('input[name="schedule_from"]').click();
    await adminPage.locator('input[name="schedule_from"]').fill(getRandomDateTime());
    await adminPage.locator('input[name="schedule_to"]').click();
    await adminPage.locator('input[name="schedule_to"]').fill(getRandomDateTime());
    await adminPage.locator('input[name="location"]').fill('Google meet');
    await adminPage.getByRole('button', { name: 'Save Activity' }).click();
  });

  test("should able to add lunch activity in lead", async ({ adminPage }) => {
    /**
    * Create a new lead.
    */
    const lead = await generateLead(adminPage);

    /**
     * write a lunch activity detail 
     */
    await openLeadByTitle(adminPage, lead.leadTitle);
    await adminPage.getByRole('button', { name: ' Activity' }).click();
    await adminPage.getByRole('heading', { name: 'Add Activity' }).locator('span').click();
    await adminPage.getByText('Lunch', { exact: true }).click();
    await adminPage.locator('input[name="title"]').fill(lead.leadTitle);
    await adminPage.locator('textarea[name="comment"]').fill(generateDescription());
    await adminPage.locator('input[name="schedule_from"]').click();
    await adminPage.locator('input[name="schedule_from"]').fill(getRandomDateTime());
    await adminPage.locator('input[name="schedule_to"]').click();
    await adminPage.locator('input[name="schedule_to"]').fill(getRandomDateTime());
    await adminPage.locator('input[name="location"]').fill('Restaurant');
    await adminPage.getByRole('button', { name: 'Save Activity' }).click();
  });
});