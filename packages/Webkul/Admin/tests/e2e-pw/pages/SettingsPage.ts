import { Page } from "playwright/test";
import CoreLocators from "../locator/CoreLocators";

export class SettingsPage extends CoreLocators {
    readonly page: Page;

    constructor(page: Page) {
        super(page);
        this.page = page;
    }

    // Warehouses
    async navigateToWarehouses() {
        await this.page.goto("admin/settings/warehouses");
    }

    // Automation
    async navigateToAutomation() {
        await this.page.goto("admin/settings/automation");
    }

    // Attributes
    async navigateToAttributes() {
        await this.page.goto("admin/settings/attributes");
    }

    // Email Templates
    async navigateToEmailTemplates() {
        await this.page.goto("admin/settings/email-templates");
    }

    // Events
    async navigateToEvents() {
        await this.page.goto("admin/settings/marketing/events");
    }

    // Campaigns
    async navigateToCampaigns() {
        await this.page.goto("admin/settings/marketing/campaigns");
    }

    // Webhooks
    async navigateToWebhooks() {
        await this.page.goto("admin/settings/webhooks");
    }

    // Workflows
    async navigateToWorkflows() {
        await this.page.goto("admin/settings/workflows");
    }

    // Data Transfer
    async navigateToDataTransfer() {
        await this.page.goto("admin/settings/data-transfer/imports");
    }

    // Other Settings
    async navigateToOtherSettings() {
        await this.page.goto("admin/settings/other-settings");
    }

    // Web Forms
    async navigateToWebForms() {
        await this.page.goto("admin/settings/web-forms");
    }

    // Tags
    async navigateToTags() {
        await this.page.goto("admin/settings/tags");
    }

    // Groups
    async navigateToGroups() {
        await this.page.goto("admin/settings/groups");
    }

    // Roles
    async navigateToRoles() {
        await this.page.goto("admin/settings/roles");
    }

    // Users / Agents
    async navigateToUsers() {
        await this.page.goto("admin/settings/users");
    }

    // Pipelines
    async navigateToPipelines() {
        await this.page.goto("admin/settings/pipelines");
    }

    // Sources
    async navigateToSources() {
        await this.page.goto("admin/settings/sources");
    }
}
