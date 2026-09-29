import { expect, test } from "../../fixtures/AdminFixtures";
import { TagsPage } from "../../pages/settings/TagsPage";

test.describe("tag management",async()=>{
    test("verify create tag",async({adminPage})=>{
        const tagsPage=new TagsPage(adminPage);
        await tagsPage.navigateToTags();
        const tagName="Tag "+Math.floor(Math.random()*1000);
        await tagsPage.createTag(tagName);
        await tagsPage.seacrhTag(tagName);
        await expect(adminPage.getByText(tagName).first()).toBeVisible();
    })
    test("verify update tag",async({adminPage})=>{
        const tagsPage=new TagsPage(adminPage);
        await tagsPage.navigateToTags();
        const tagName="Tag "+Math.floor(Math.random()*1000);
        await tagsPage.createTag(tagName);
        const updatedTagName=tagName+" Updated";
        await tagsPage.udpateTag(updatedTagName);
        await tagsPage.seacrhTag(updatedTagName);
        await expect(adminPage.getByText(updatedTagName).first()).toBeVisible();
    })
    test("verify delete tag",async({adminPage})=>{
        const tagsPage=new TagsPage(adminPage);
        await tagsPage.navigateToTags();
        const tagName="Tag "+Math.floor(Math.random()*1000);            
        await tagsPage.createTag(tagName);
        await tagsPage.seacrhTag(tagName);
        await expect(adminPage.getByText(tagName).first()).toBeVisible();
        await tagsPage.deleteTag();
        await tagsPage.seacrhTag(tagName);
        await expect(adminPage.getByText(tagName).first()).toBeHidden();
    })
})