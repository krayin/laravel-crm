import { test, expect } from "../setup";
import {
    generateEmailSubject,
    createPerson,
    generateDate,
    generateName,
} from "../utils/faker";

test.describe("quotes management", () => {
    test("should create a quotes", async ({ adminPage }) => {
        /**
         * Create person.
         */
        await adminPage.goto("admin/contacts/persons");
        const Person = await createPerson(adminPage);

        /**
         * Create quote.
         */
        await adminPage.goto("admin/quotes");
        await adminPage.getByRole("link", { name: "Create Quote" }).click();
        await adminPage.getByRole("textbox", { name: "Subject *" }).click();
        await adminPage
            .getByRole("textbox", { name: "Subject *" })
            .fill(generateEmailSubject());
        await adminPage.getByRole("textbox", { name: "Description" }).click();
        await adminPage
            .getByRole("textbox", { name: "Description" })
            .fill(generateEmailSubject());
        await adminPage.getByLabel("Sales Owner").selectOption("1");
        await adminPage.getByRole("textbox", { name: "Expired At *" }).click();
        await adminPage
            .getByRole("textbox", { name: "Expired At *" })
            .fill(generateDate());
        await adminPage.locator(".relative > div > .relative").first().click();
        await adminPage.getByRole("textbox", { name: "Search..." }).click();
        await adminPage
            .getByRole("textbox", { name: "Search..." })
            .fill(Person.Name);
        await adminPage
            .getByRole("listitem")
            .filter({ hasText: Person.Name })
            .click();

        /**
         * Fill billing address.
         */
        await adminPage
            .locator('textarea[name="billing_address\\[address\\]"]')
            .click();
        await adminPage
            .locator('textarea[name="billing_address\\[address\\]"]')
            .fill("ARV Park");
        await adminPage
            .locator('select[name="billing_address\\[country\\]"]')
            .selectOption("IN");
        await adminPage
            .locator('select[name="billing_address\\[state\\]"]')
            .selectOption("UP");
        await adminPage
            .locator('input[name="billing_address\\[city\\]"]')
            .click();
        await adminPage
            .locator('input[name="billing_address\\[city\\]"]')
            .fill("Noida");
        await adminPage
            .locator('input[name="billing_address\\[postcode\\]"]')
            .click();
        await adminPage
            .locator('input[name="billing_address\\[postcode\\]"]')
            .fill("201301");

        /**
         * Fill shipping address.
         */
        await adminPage
            .locator('textarea[name="shipping_address\\[address\\]"]')
            .click();
        await adminPage
            .locator('textarea[name="shipping_address\\[address\\]"]')
            .fill("ARV Park");
        await adminPage
            .locator('select[name="shipping_address\\[country\\]"]')
            .selectOption("IN");
        await adminPage
            .locator('select[name="shipping_address\\[state\\]"]')
            .selectOption("UP");
        await adminPage
            .locator('input[name="shipping_address\\[city\\]"]')
            .click();
        await adminPage
            .locator('input[name="shipping_address\\[city\\]"]')
            .fill("Noida");
        await adminPage
            .locator('input[name="shipping_address\\[postcode\\]"]')
            .click();
        await adminPage
            .locator('input[name="shipping_address\\[postcode\\]"]')
            .fill("201301");
        await adminPage.getByRole("button", { name: "Save Quote" }).click();
        await expect(adminPage.locator("#app")).toContainText("Success");
    });
});
