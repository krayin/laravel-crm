
import { Page, Locator, FrameLocator, expect } from '@playwright/test';

import CoreLocators from '../locator/CoreLocators';
import { generateDescription, generateEmail, generateName } from '../utils/faker';

export type MailData = {
    replyTo: string;
    subject: string;
    body: string;
};
/**
 * `generateName()` draws from a 12x12 word list, so it repeats a subject roughly every dozen runs
 * and the mailbox already holds several copies of each. Every step here searches by subject and
 * acts on the first match, so a repeated subject makes a test trash or delete some earlier run's
 * mail and then fail to find its own — which is what made the trash tests flaky rather than
 * broken. The run suffix makes each run's subject its own.
 */
export const mailData: MailData = {
    replyTo: generateEmail(),
    subject: `${generateName()} ${Date.now().toString().slice(-6)}${Math.floor(Math.random() * 1000)}`,
    body: generateDescription().slice(0, 50),
};
export type emailType = 'bcc' | 'cc' | 'normal'|'both';
export class MailPage extends CoreLocators {
    readonly page: Page;
    readonly composeEmailButton: Locator;
    readonly replyToInput: Locator;
    readonly subjectInput: Locator;
    readonly sendButton: Locator;
    readonly successMessage: Locator;
    readonly editorFrame: FrameLocator;
    readonly saveDraftButton: Locator;
    readonly moveToTrashButton: Locator;
    readonly ccMailInput: Locator;
    readonly bccMailInput: Locator;
    readonly ccButton: Locator;
    readonly bccButton: Locator;

    constructor(page: Page) {
        super(page);
        this.page = page;
        this.composeEmailButton = page.getByRole('button', { name: 'Compose Mail' });
        this.replyToInput = page.getByLabel(/reply to/i).or(page.locator('input[name="temp-reply_to"]'));
        this.subjectInput = page.getByLabel(/subject/i).or(page.locator('input[name="subject"]'));
        this.sendButton = page.getByRole("button", { name: "Send" });
        this.successMessage = page.getByText("Email sent successfully.");
        this.editorFrame = page.frameLocator("iframe.tox-edit-area__iframe");
        this.saveDraftButton = page.getByRole("button", { name: "Draft" });
        this.moveToTrashButton = page.getByRole('button', { name: 'Moved To Trash' })
        this.ccMailInput = page.locator('input[name="temp-cc"]');
        this.bccMailInput = page.locator('input[name="temp-bcc"]');
        this.ccButton = page.getByText('CC', { exact: true });
        this.bccButton = page.getByText('BCC', { exact: true });
    }

    async navigateToMailInboxPage() {
        await this.page.goto("admin/mail/inbox");
    }
    async navigateToMailDraftPage() {
        await this.page.goto("admin/mail/draft");
    }
    async navigateToMailTrashedPage() {
        await this.page.goto("admin/mail/trash");
    }
    async composeEmail(mailData: MailData, type: emailType) {
        await this.composeEmailButton.click();
        await this.page.waitForTimeout(500);

        if (type === 'cc') {
            await this.ccButton.click();
            await this.ccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.ccMailInput.fill("cc" + mailData.replyTo);
        } else if (type === 'bcc') {
            await this.bccButton.click();
            await this.bccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.bccMailInput.fill("bcc" + mailData.replyTo);
        } else if (type === 'both') {
            await this.ccButton.click();
            await this.bccButton.click();
            await this.bccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.ccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.ccMailInput.fill("cc" + mailData.replyTo);
            await this.bccMailInput.fill("bcc" + mailData.replyTo);
        }

        await this.replyToInput.fill(mailData.replyTo);
        await this.subjectInput.fill(mailData.subject);

        await this.editorFrame.locator('body').fill(mailData.body);
        await this.sendButton.click();
    }
    
    async saveDraftEmail(mailData: MailData, type: emailType) {
        await this.composeEmailButton.click();
        await this.page.waitForTimeout(500);

        if (type === 'cc') {
            await this.ccButton.click();
            await this.ccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.ccMailInput.fill("cc" + mailData.replyTo);
        } else if (type === 'bcc') {
            await this.bccButton.click();
            await this.bccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.bccMailInput.fill("bcc" + mailData.replyTo);
        } else if (type === 'both') {
            await this.ccButton.click();
            await this.bccButton.click();
            await this.ccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.ccMailInput.fill("cc" + mailData.replyTo);


            await this.bccMailInput.waitFor({ state: 'visible', timeout: 5000 });
            await this.bccMailInput.fill("bcc" + mailData.replyTo);
        }

        await this.replyToInput.fill(mailData.replyTo);
        await this.subjectInput.fill(mailData.subject);

        await this.editorFrame.locator('body').fill(mailData.body);
        await this.saveDraftButton.click();
        await this.navigateToMailDraftPage();
        await this.searchEmail(mailData.subject);
        await expect(this.page.getByRole('paragraph').filter({ hasText: mailData.subject }).first()).toBeVisible();
    }
    async movedEmailToTrash() {
        /**
         * The caller has just searched, so wait for the checkbox the reloaded grid renders rather
         * than for a fixed delay — clicking it mid-reload selects nothing.
         */
        await expect(this.multiSelectCheckbox).toBeVisible();

        await this.multiSelectCheckbox.click();

        await this.moveToTrashButton.click();

        await this.agreeButton.click();

        await expect(this.page.getByText(/deleted successfully/i).first()).toBeVisible();
    }
    async searchEmail(subject: string) {
        await this.searchInputExact.fill(subject);
        await this.page.keyboard.press('Enter');
    }
    async deleteEmailFromTrash(subject: string) {
        await this.searchEmail(subject);

        /**
         * Wait for the row itself rather than a fixed delay: the grid reloads over XHR after the
         * search, and clicking the select-all checkbox before it settles selects nothing, so the
         * delete then applies to no record and the caller's assertion fails for the wrong reason.
         */
        await expect(
            this.page.getByRole('paragraph').filter({ hasText: subject }).first()
        ).toBeVisible();

        await this.multiSelectCheckbox.click();

        await this.deleteButton.click();

        await this.agreeButton.click();

        /**
         * The delete is confirmed before the caller asserts the row is gone, so the assertion is
         * not racing the request that removes it.
         */
        await expect(this.page.getByText(/deleted successfully/i).first()).toBeVisible();
    }

}