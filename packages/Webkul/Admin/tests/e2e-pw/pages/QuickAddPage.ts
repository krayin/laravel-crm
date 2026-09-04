import { Page, expect } from "@playwright/test";
import CoreLocators from "../locator/CoreLocators";

export class QuickAddPage extends CoreLocators {
    constructor(page: Page) {
        super(page);
    }

    async openQuickAddModal() {
        await this.page.goto("admin/dashboard");
        await this.quickAddTrigger.waitFor({ state: "visible" });
        await this.quickAddTrigger.click();
        await expect(this.quickAddModalTitle).toBeVisible();
    }

    async selectQuickAddTab(tabLabel: string) {
        await this.quickAddTab(tabLabel).click();
    }

    async submitQuickAdd() {
        await this.quickAddSaveButton.click();
    }
}
