import { Page, Locator, expect } from "@playwright/test";
import CoreLocators from "../locator/CoreLocators";

export class AdminPage extends CoreLocators {
    readonly page: Page;
    readonly emailInput: Locator;
    readonly passwordInput: Locator;
    readonly signInButton: Locator;
    readonly megaSearchInput: Locator;
    readonly profileButton: Locator;
    readonly signOutLink: Locator;

    constructor(page: Page) {
        super(page)
        this.page = page;
        this.emailInput = page.getByPlaceholder("Email Address");
        this.passwordInput = page.getByPlaceholder("Password");
        this.signInButton = page.getByRole("button", { name: "Sign In" });
        this.megaSearchInput = page.getByPlaceholder("Mega Search").first();
        this.profileButton = page.getByRole("button", { name: "E" });
        this.signOutLink = page.getByRole("link", { name: "Sign Out" });
    }



    async adminLogin(email: string, password: string) {
        await this.page.goto('admin/login');
        await this.emailInput.fill(email);
        await this.passwordInput.fill(password);
        await this.signInButton.click();
    }

    async verifyLogin() {
        await expect(this.megaSearchInput).toBeVisible();
    }

    async adminLogout() {
        await this.page.waitForTimeout(1000);
        await this.profileButton.click();
        await this.signOutLink.click();

    }

    async verifyLogout() {
        await expect(this.passwordInput.first()).toBeVisible();
    }
}
