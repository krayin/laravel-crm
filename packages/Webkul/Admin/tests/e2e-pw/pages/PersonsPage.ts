import { expect, Locator, Page } from "playwright/test";
import CoreLocators from "../locator/CoreLocators";
import { generateEmail, generateFullName, generateName, generatePhoneNumber } from "../utils/faker";
import { organizationData } from "../pages/OrganizationPage";
export type PersonData = {
    name: string;
    emails: string;                     // array to hold multiple emails
    contactNumber: string;             // array to hold multiple contact numbers
    jobTitle: string;
    salesOwnerId: string;                 // id or value representing Sales Owner
    organizationName: string;
};

export const personData: PersonData = {
    name: generateName(),
    emails: generateEmail(),
    contactNumber: generatePhoneNumber(),
    jobTitle: generateName(),
    salesOwnerId: "1", // Example sales owner id
    organizationName: organizationData.name
};

export default class PersonsPage extends CoreLocators {
    readonly page: Page;
    readonly firstPerson:Locator;

    constructor(page: Page) {
        super(page),
            this.page = page
            this.firstPerson= page.locator(`(//div[@class="flex flex-col gap-1.5 dark:text-gray-300"])[1]`);
    }

    async navigageToPersonsPage() {
        await this.page.goto("admin/contacts/persons");
    }
    async createPerson(personData: PersonData) {

        await this.createPersonLink.click();

        // Fill person details (use first email/phone if multiple provided)
        await this.personNameTextbox.fill(personData.name);
        if (personData.emails && personData.emails.length > 0) {
            await this.personEmailTextbox.fill(personData.emails);
        }
        if (personData.contactNumber && personData.contactNumber.length > 0) {
            await this.personPhoneTextbox.fill(personData.contactNumber);
        }
        await this.personJobTitleTextbox.fill(personData.jobTitle || "");

        // Assign organization (if provided)
        if (personData.organizationName) {
            await this.personOrgSelectDiv.click();
            await this.searchInputField.fill("Examp");
            await this.personOrgListItem("Examp").click();
        }
        // Save person
        await this.savePersonButton.click();
        await this.searchInputExact.fill(personData.name);
        await this.page.keyboard.press('Enter');
        await expect(this.page.getByText(personData.name).first()).toBeVisible();

    }
    async updatePerson(personData: PersonData) {
        await this.searchInputExact.fill(personData.name);
        await this.page.keyboard.press('Enter');
        await this.firstEditIcon.click();
        // Fill person details (use first email/phone if multiple provided)
        await this.personNameTextbox.fill(personData.name);
        if (personData.emails && personData.emails.length > 0) {
            await this.personEmailTextbox.fill(personData.emails);
        }
        if (personData.contactNumber && personData.contactNumber.length > 0) {
            await this.personPhoneTextbox.fill(personData.contactNumber);
        }
        await this.personJobTitleTextbox.fill(personData.jobTitle || "");

        // Assign organization (if provided)
        if (personData.organizationName) {
            await this.personOrgSelectDiv.click();
            await this.searchInputField.fill("Examp");
            await this.personOrgListItem("Examp").click();
        }
        // Save person
        await this.savePersonButton.click();
        await this.searchInputExact.fill(personData.name);
        await expect(this.page.getByText(personData.name).first()).toBeVisible();

    }
    async personDelete() {

        await this.firstDeleteIcon.click();
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }
    async personMassDelete() {
        await this.multiSelectCheckbox.click();
        await this.deleteButton.click();
        await this.agreeButton.click();
        await expect(this.successMessage.first()).toBeVisible();
    }



}