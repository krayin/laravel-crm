
import { generateFullName, generateSKU } from "../../utils/faker";
import { attributeData, AttributesPage } from "../../pages/settings/AttributesPage";
import { test } from "../../fixtures/AdminFixtures";
import { AdminPage } from "../../pages/AdminPage";
test.describe("attribute managment ", async () => {

    test("should create an attribute", async ({ adminPage }) => {
        const attributePage = new AttributesPage(adminPage);
        await attributePage.navigateToAttributes();
        await attributePage.createAttribute(attributeData);

    });
    test("verify update attribute", async ({ adminPage }) => {
        const attributePage = new AttributesPage(adminPage)
        await attributePage.navigateToAttributes();
        await attributePage.searchAttribute(attributeData.name.toLowerCase()
        );
        await attributePage.updateAttribute(attributeData);

    })
    test("verify delete attribute",async({adminPage})=>{
        const attributePage= new AttributesPage(adminPage);
        await attributePage.navigateToAttributes();
        await attributePage.searchAttribute(attributeData.name);
        await attributePage.deleteAttribute();
    })
    


})
