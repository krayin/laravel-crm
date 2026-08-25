import { test, expect } from "../fixtures/AdminFixtures";
import { AdminPage } from "../pages/AdminPage";


test.describe("admin mangement", async () => {

    test("verify admin login", async ({ adminPage }) => {

        expect(adminPage.waitForURL("admin/dashboard"));

    })
    test("verify admin logout", async ({ adminPage }) => {
        const admin = await new AdminPage(adminPage);

        await admin.adminLogout();
        await admin.verifyLogout();
    


    })


})