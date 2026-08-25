import path from "path";
import { fileURLToPath } from "url";
import { expect, test } from "../../fixtures/AdminFixtures";
import { DataTransferPage } from "../../pages/settings/DataTransferPage";
import OrganizationPage, { organizationData } from "../../pages/OrganizationPage";
import PersonsPage, { personData } from "../../pages/PersonsPage";
import { allowedNodeEnvironmentFlags } from "process";
const updatedCsvFileName = 'leads_updated.csv';
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const csvFileName ={
    leads: 'leads.csv',
    updatedLeads: 'leads_updated.csv',
    persons: 'persons.csv',
    updatedPersons: 'persons_updated.csv',
    products: 'products.csv',
    updatedProducts: 'products_updated.csv',

}


test.describe("data transfer product management",async()=>{
    test("verify create import product",async({adminPage})=>{
      
        const dataTransferPage=new DataTransferPage(adminPage );
        const csvPath = path.resolve(__dirname, `../../data/data-transfer/${csvFileName.products}`);
    
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.createImport(csvPath,'products');
    }
    )
    test("verify update import product",async({adminPage})=>{
        const updatedCsvPath = path.resolve(__dirname, `../../data/data-transfer/${csvFileName.updatedProducts}`);
        const dataTransferPage=new DataTransferPage(adminPage );
        
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.updateImport(updatedCsvPath);
        await expect(dataTransferPage.successMessage.first()).toBeVisible();    
    }
    )
    test("verify delete import product",async({adminPage})=>{
        const dataTransferPage=new DataTransferPage(adminPage );
        
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.deleteImport();
        await expect(dataTransferPage.successMessage.first()).toBeVisible();    
    }
    )
})  


test.describe("data transfer lead management",async()=>{
    let personId:string | null;


    test("verify create import lead",async({adminPage})=>{
        const csvPath = path.resolve(__dirname, `../../data/data-transfer/${csvFileName.leads}`);
        const person = new PersonsPage(adminPage);
        const organization=new OrganizationPage(adminPage);
        

        await person.navigageToPersonsPage();
        await person.createPerson(personData);
        personId = await person.firstPerson.textContent();

      
        const dataTransferPage=new DataTransferPage(adminPage );
        await dataTransferPage.updateCsv(csvPath, 'person_id', personId?.toString() || '1' );
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.createImport(csvPath,'leads');
    }
    )
    test("verify update import lead",async({adminPage})=>{
        const updatedCsvPath = path.resolve(__dirname, `../../data/data-transfer/${csvFileName.updatedLeads}`);
        const person = new PersonsPage(adminPage);
        const dataTransferPage=new DataTransferPage(adminPage );
        await person.navigageToPersonsPage();
         personId = await person.firstPerson.textContent();

        await dataTransferPage.updateCsv(updatedCsvPath, 'person_id', personId?.toString() || '1' );
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.updateImport(updatedCsvPath);
        await expect(dataTransferPage.successMessage.first()).toBeVisible();    
    }
    )
    test("verify delete import lead",async({adminPage})=>{
        const dataTransferPage=new DataTransferPage(adminPage );
        
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.deleteImport();
        await expect(dataTransferPage.successMessage.first()).toBeVisible();    
    }
    )
})
test.describe("data transfer person management",async()=>{
    let organizationId:string | null;
    test("verify create import person",async({adminPage})=>{
        const csvPath = path.resolve(__dirname, `../../data/data-transfer/${csvFileName.persons}`);
        const organization=new OrganizationPage(adminPage);
        await organization.navigateToOrganization();
        await organization.createOrganization(organizationData);
      
        const dataTransferPage=new DataTransferPage(adminPage );
        organizationId=await organization.firstOrganization.textContent();
        await dataTransferPage.updateCsv(csvPath, 'organization_id', organizationId?.toString() || '1' );  
      
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.createImport(csvPath,'persons');
    }
    )
    test("verify update import person",async({adminPage})=>{
        const updatedCsvPath = path.resolve(__dirname, `../../data/data-transfer/${csvFileName.updatedPersons}`);
        const dataTransferPage=new DataTransferPage(adminPage );
        await dataTransferPage.updateCsv(updatedCsvPath, 'organization_id', organizationId?.toString() || '1' );  
        
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.updateImport(updatedCsvPath);
        await expect(dataTransferPage.successMessage.first()).toBeVisible();    
    }

    )
    test("verify delete import person",async({adminPage})=>{
        const dataTransferPage=new DataTransferPage(adminPage );
        
        await dataTransferPage.navigateToDataTransfer();
        await dataTransferPage.deleteImport();
        await expect(dataTransferPage.successMessage.first()).toBeVisible();    
    }
    )   
})      