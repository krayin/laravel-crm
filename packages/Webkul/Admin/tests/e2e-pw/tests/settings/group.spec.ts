import { test } from "../../fixtures/AdminFixtures";
import { groupData, GroupPage } from "../../pages/settings/GroupsPage";

test.describe("group managment",async()=>{
    test("verify create group",async({adminPage})=>{
        const groupPage=new GroupPage(adminPage);
        await groupPage.navigateToGroups();
        await groupPage.createGroup(groupData);
    })
    test("verify update group",async({adminPage})=>{
        const groupPage=new GroupPage(adminPage);
        await groupPage.navigateToGroups();
        const updatedGroupData={
            name: groupData.name+" Updated",
            description: groupData.description+" Updated"
        }
        await groupPage.updateGroup(updatedGroupData);
    })
    test("verify delete group",async({adminPage})=>{
        const groupPage=new GroupPage(adminPage);
        await groupPage.navigateToGroups();
    
    })
 
})