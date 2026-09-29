import { expect, Locator, Page } from "playwright/test";
import CoreLocators from "../locator/CoreLocators";
import { generateFirstName, generateLocation, generateName } from "../utils/faker";
export type OrganizationData = {
    name: string;
    address: string;
    country: string; // country code, e.g. 'IN'
    state: string;   // state code, e.g. 'DL'
    city: string;
    postcode: string;
};
 export  const organizationData: OrganizationData = {
  name:  generateName(),
  address: generateLocation() ,
  country: "IN",               // India country code
  state: "DL",                 // Delhi state code
  city: "New Delhi",
  postcode: "110015",
};
export default class OrganizationPage extends CoreLocators{

    
    readonly page:Page
    readonly firstOrganization:Locator;
    constructor(page:Page)
    {
        super(page)
        this.page=page
        this.firstOrganization= page.locator(`(//p[@class="break-words"])[1]`);

    }
    async navigateToOrganization()
    {
       await  this.page.goto("admin/contacts/organizations");
    }

    async createOrganization(orgData:OrganizationData) {
    await this.createOrgLink.click();

    // Fill organization details
    await this.orgNameTextbox.fill(orgData.name);
    await this.orgAddressTextarea.fill(orgData.address);

    await this.orgCountryCombobox.selectOption(orgData.country);
    await this.orgStateSelect.selectOption(orgData.state);

    await this.orgCityTextbox.fill(orgData.city);
    await this.orgPostcodeTextbox.fill(orgData.postcode);

    await this.orgExtraDetailsDiv.nth(2).click();

    await this.searchInputField.fill('exampl');
    await this.orgExampleListItem('Example').click();



    // Save organization
    await this.saveOrganizationButton.click();
    await expect(this.organizationCreateSuccessMessage.first()).toBeVisible();
    // await this.searchInputExact.fill(orgData.name);
    // await this.page.keyboard.press('Enter');
    console.log("There’s an issue: the organization search isn’t working and needs to be fixed.");


    await expect(this.page.getByText(orgData.name).first()).toBeVisible();

   
    }



    async updateOrganization(orgData:OrganizationData)
    {
    
    
    await this.firstEditIcon.click();
    // Fill organization details
    await this.orgNameTextbox.fill(orgData.name);
    await this.orgAddressTextarea.fill(orgData.address);

    await this.orgCountryCombobox.first().selectOption(orgData.country);
    await this.orgStateSelect.selectOption(orgData.state);

    await this.orgCityTextbox.fill(orgData.city);
    await this.orgPostcodeTextbox.fill(orgData.postcode);

    // Save organization
    await this.saveOrganizationButton.click();
    return orgData;

    }

    async deleteOrganization(){

       await this.firstDeleteIcon.click();
       await this.agreeButton.click();
    }

}