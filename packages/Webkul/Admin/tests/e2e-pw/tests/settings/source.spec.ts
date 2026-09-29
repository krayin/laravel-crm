import { verify } from "crypto";
import { expect, test } from "../../fixtures/AdminFixtures";
import { SourcesPage } from "../../pages/settings/SourcesPage";
import { generateFirstName } from "../../utils/faker";

test.describe("source managment",async()=>{
    const sourceName=generateFirstName();
    const updateSourceName=sourceName+"udated";
    test('verify create source',async({adminPage})=>{
        const source=new SourcesPage(adminPage);
        await source.navigateToSources();
        await source.createSourceLink.click();
        await source.sourceForm(sourceName);
    })
    test('verify update source',async({adminPage})=>{
        const source = new SourcesPage(adminPage);
        await source.navigateToSources();
        await source.searchSource(sourceName);
        await source.firstEditIcon.click();
        await source.sourceForm(updateSourceName);
    })
    test("verify delete source",async ({adminPage})=>{
        const source= new SourcesPage(adminPage);
        await source.navigateToSources();
        await source.searchSource(updateSourceName);
        await source.deleteSource();
        
    })


})