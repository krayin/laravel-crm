import { test } from "../fixtures/AdminFixtures";
import OrganizationPage, { organizationData } from "../pages/OrganizationPage";


test.describe("organization mangement",async()=>{

    


    test("verify organization create",async({adminPage})=>{

        const organizationPage=new OrganizationPage(adminPage);
        await organizationPage.navigateToOrganization();
        await organizationPage.createOrganization(organizationData);
        

    })

    test('verify update organization',async({adminPage})=>{
        const organizationPage=new OrganizationPage(adminPage);
        await organizationPage.navigateToOrganization();
        await organizationPage.updateOrganization(organizationData);

    })

    
    test("verify delete organization",async({adminPage})=>{
        const organizationPage=new OrganizationPage(adminPage);
        await organizationPage.navigateToOrganization();
        await organizationPage.searchByName(organizationData.name);
        await organizationPage.deleteOrganization();


    })
    test("verify mass delete organization",async({adminPage})=>{

        const organizationPage=new OrganizationPage(adminPage);
        await organizationPage.navigateToOrganization();

        /* create organization before delete*/
        
        await organizationPage.createOrganization(organizationData);

        /* than mass delete organization*/ 
        await organizationPage.massDelete();

    })


    
})