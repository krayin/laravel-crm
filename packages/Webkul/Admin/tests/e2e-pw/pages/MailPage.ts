
import { Page, Locator, FrameLocator, expect } from '@playwright/test';

import CoreLocators from '../locator/CoreLocators';
import { generateDescription, generateEmail, generateName } from '../utils/faker';

export type MailData = {
    replyTo: string;
    subject: string;
    body: string;
};
export const mailData: MailData = {
    replyTo: generateEmail(),
    subject: generateName(),
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
        await this.page.waitForTimeout(500);
        await this.multiSelectCheckbox.click();
        await this.page.waitForTimeout(500);
        await this.moveToTrashButton.click();
        await this.agreeButton.click();
    }
    async searchEmail(subject: string) {
        await this.searchInputExact.fill(subject);
        await this.page.keyboard.press('Enter');
    }
    async deleteEmailFromTrash(subject: string) {
        await this.searchEmail(subject);
        await this.page.waitForTimeout(500);
        await this.multiSelectCheckbox.click();
        await this.page.waitForTimeout(500);
        await this.deleteButton.click();
        await this.agreeButton.click();
    }

}