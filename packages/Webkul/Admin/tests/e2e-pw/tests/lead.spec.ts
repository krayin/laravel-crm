import { test, expect } from "../fixtures/AdminFixtures";
import { LeadData, leadData, LeadPage } from "../pages/LeadPage";
import PersonsPage from "../pages/PersonsPage";
import { generateDescription, generateEmail, generateName, generatePhoneNumber, generateSKU } from "../utils/faker";


test.describe("lead management", async () => {

    const updatedLeadData={
            title: generateName(),
            description: generateDescription(),
            value: (Math.floor(Math.random() * 10000)).toString(),
            expectedCloseDate: "2028-12-31",
            person: leadData.person,
            product: leadData.product,
            organizationName: leadData.person.organizationName

    }
  
    test("should create a new lead", async ({ adminPage }) => {

        const leadPage = new LeadPage(adminPage);
        const personPage = new PersonsPage(adminPage);
        await personPage.navigageToPersonsPage();
        /**
         * Create the person the lead form will then select. It has to be the lead flow's own
         * person, not the shared one, or this collides with the person spec's record.
         */
        await personPage.createPerson(leadData.person);

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