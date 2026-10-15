import { expect, test } from "../../../fixtures/AdminFixtures";
import { generateDescription, generateName } from "../../../utils/faker";

/**
 * A workflow is an event, the conditions that narrow it, and the actions it then takes.
 *
 * The form builds its condition and action rows in the browser, and what a condition offers
 * depends on the event chosen before it, so each step waits for the row it is about to fill rather
 * than assuming the previous click has already rendered it.
 */
test.describe("Workflow Management", () => {
    const workflowName = generateName();
    const updatedName = generateName();
    const workflowDescription = generateDescription();
    const updatedDescription = generateDescription();

    test("should create a workflow", async ({ adminPage }) => {
        await adminPage.goto("admin/settings/workflows/create");

        await adminPage.getByRole("textbox", { name: "Name" }).fill(workflowName);

        await adminPage
            .getByRole("textbox", { name: "Description" })
            .fill(workflowDescription);

        /**
         * The event decides the entity the workflow belongs to, which in turn decides the
         * attributes and actions offered below, so it is chosen before anything is added.
         */
        await adminPage.locator('select[name="event"]').selectOption("lead.update.after");

        await adminPage.locator("#condition_type").selectOption("and");

        /* A condition on the lead's title, which is a plain text attribute. */
        await adminPage.getByRole("button", { name: "Add Condition" }).click();

        const conditionAttribute = adminPage.locator('[name="conditions[0][attribute]"]');

        await expect(conditionAttribute).toBeVisible();

        await conditionAttribute.selectOption("title");

        /**
         * The value control is rendered to suit the attribute — an input, a select, a date picker
         * — so it is matched on the name it starts with rather than on the element it became.
         */
        const conditionValue = adminPage
            .locator('[name^="conditions[0][value]"]')
            .first();

        await expect(conditionValue).toBeVisible();

        await conditionValue.fill("title name");

        /* An action that tags whatever the workflow matches. */
        await adminPage.getByRole("button", { name: "Add Action" }).click();

        const actionType = adminPage.locator('[name="actions[0][id]"]');

        await expect(actionType).toBeVisible();

        await actionType.selectOption("add_tag");

        const actionValue = adminPage.locator('[name^="actions[0][value]"]').first();

        await expect(actionValue).toBeVisible();

        await actionValue.fill("new tag");

        await adminPage.getByRole("button", { name: "Save Workflow" }).click();

        await expect(adminPage.getByText(workflowName).first()).toBeVisible();
    });

    test("should update a workflow", async ({ adminPage }) => {
        await adminPage.goto("admin/settings/workflows");

        await adminPage.locator("span.icon-edit").first().click();

        const nameInput = adminPage.getByRole("textbox", { name: "Name" });

        await expect(nameInput).toBeVisible();

        await nameInput.fill(updatedName);

        await adminPage
            .getByRole("textbox", { name: "Description" })
            .fill(updatedDescription);

        await adminPage.getByRole("button", { name: "Save Workflow" }).click();

        await expect(adminPage.getByText(updatedName).first()).toBeVisible();
    });

    test("should delete a workflow", async ({ adminPage }) => {
        await adminPage.goto("admin/settings/workflows");

        await adminPage.locator("span.icon-delete").first().click();

        await adminPage.getByRole("button", { name: "Agree", exact: true }).click();

        /**
         * The listing itself reports the removal, which is waited for rather than the row's
         * absence: an assertion that something is gone passes just as readily before the request
         * has finished as after it.
         */
        await expect(
            adminPage.getByText(/deleted successfully/i).first()
        ).toBeVisible();
    });
});
