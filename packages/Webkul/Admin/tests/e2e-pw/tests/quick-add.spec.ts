import { test } from "../fixtures/AdminFixtures";
import {
    QuickAddPage,
    quickAddLeadData,
    quickAddMailData,
    quickAddOrganizationData,
    quickAddPersonData,
    quickAddProductData,
} from "../pages/QuickAddPage";

test.describe("quick add modal", async () => {

    test("should open the quick add modal from the header plus button", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        /**
         * The seeded admin role has all five quick create permissions.
         */
        await quickAddPage.verifyQuickAddTabs(["Lead", "Person", "Organization", "Product", "Email"]);

    });

    test("should create a lead via quick add", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        await quickAddPage.createLead(quickAddLeadData);

    });

    test("should create a person via quick add", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        await quickAddPage.createPerson(quickAddPersonData);

    });

    test("should create an organization via quick add", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        await quickAddPage.createOrganization(quickAddOrganizationData);

    });

    test("should create a product via quick add", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        await quickAddPage.createProduct(quickAddProductData);

    });

    test("should send an email via quick add", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        await quickAddPage.createMail(quickAddMailData);

    });

    test("should keep modal open and surface validation errors when fields are empty", async ({ adminPage }) => {

        const quickAddPage = new QuickAddPage(adminPage);

        await quickAddPage.submitEmptyForm("Lead");

    });

});
