import { test, expect } from "../../setup";
import { createPerson, generateCompanyName } from "../../utils/faker";

test("should be able to create person", async ({ adminPage }) => {
    /**
     * Create person.
     */
    await adminPage.goto("admin/contacts/persons");
    await createPerson(adminPage);
});

test("should be able to assign a company to person", async ({ adminPage }) => {
    /**
     * Create person.
     */
    await adminPage.goto("admin/contacts/persons");
    await createPerson(adminPage);
    const editButton = adminPage.locator("span.icon-edit").first();

    await expect(editButton).toBeVisible();
    await editButton.click();

    const addCompanyField = adminPage
        .locator("div")
        .filter({ hasText: /^Click to add$/ })
        .nth(2);

    await expect(addCompanyField).toBeVisible();
    await addCompanyField.click();
    await adminPage.getByRole("textbox", { name: "Search..." }).click();
    await adminPage
        .getByRole("textbox", { name: "Search..." })
        .fill(generateCompanyName());
    await adminPage.getByText("Add as New").click();
    await adminPage.getByRole("button", { name: "Save Person" }).click();
});

test("should be able to delete person", async ({ adminPage }) => {
    /**
     * Create a person first so there is one to delete.
     */
    await adminPage.goto("admin/contacts/persons");
    await createPerson(adminPage);

    /**
     * Delete person.
     */
    await adminPage.locator("span.icon-delete").first().click();
    await adminPage.getByRole("button", { name: "Agree", exact: true }).click();
    await expect(adminPage.locator("#app")).toContainText("Success", { timeout: 10000 });
});
