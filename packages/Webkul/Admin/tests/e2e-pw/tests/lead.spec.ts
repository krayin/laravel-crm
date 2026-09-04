import { test, expect } from "../fixtures/AdminFixtures";
import { LeadData, leadData, LeadPage } from "../pages/LeadPage";
import PersonsPage, { personData } from "../pages/PersonsPage";
import { productData } from "../pages/ProductPage";
import { generateDescription, generateEmail, generateName, generatePhoneNumber, generateSKU } from "../utils/faker";


test.describe("lead management", async () => {

    const updatedLeadData={
            title: generateName(),
            description: generateDescription(),
            value: (Math.floor(Math.random() * 10000)).toString(),
            expectedCloseDate: "2028-12-31",
            person: personData,
            product: productData,
            organizationName: personData.organizationName

    }
  
    test("should create a new lead", async ({ adminPage }) => {

        const leadPage = new LeadPage(adminPage);
        const personPage = new PersonsPage(adminPage);
        await personPage.navigageToPersonsPage();
        await personPage.createPerson(personData);

        await leadPage.createLead(leadData);



    });

    test("should update an existing lead", async ({ adminPage }) => {

        const lead=new LeadPage(adminPage);

        await lead.navigateToLeadList();
        const updatelead = await lead.searchLead(leadData.title);
        await updatelead.updateLead(updatedLeadData);
       
    });
    test("user should able to delete the lead", async ({ adminPage }) => {
        const leadPage = new LeadPage(adminPage);

        await leadPage.navigateToLeadList();
        await leadPage.deleteLead(updatedLeadData);


    })


})