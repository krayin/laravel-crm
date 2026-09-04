import { title } from "process";
import { test, expect } from "../fixtures/AdminFixtures";
import { AdminPage } from "../pages/AdminPage";
import { LeadData, LeadPage } from "../pages/LeadPage";
import OrganizationPage, { OrganizationData } from "../pages/OrganizationPage";
import PersonsPage, { PersonData } from "../pages/PersonsPage";
import { ProductData, productData, ProductPage } from "../pages/ProductPage";
import { quoteData, QuotesPage, } from "../pages/QuotesPage";
import { generateDescription, generateEmail, generateLocation, generateName, generatePhoneNumber, generateSKU } from "../utils/faker";

test.describe("quotes mangement", async () => {

  test("verify create quote", async ({ adminPage }) => {
    const person = new PersonsPage(adminPage);
    const product = new ProductPage(adminPage);
    await person.navigageToPersonsPage();
    
    await person.createPerson(quoteData.person);
    await product.navigateToProductPage();
    await product.createProductLink.click();
    await product.productForm(productData);
    quoteData.product = productData;
    const quote = new QuotesPage(adminPage);

    await quote.navigateToQuotesPage();
    await quote.createQuote(quoteData);
    
    await expect(quote.successMessage.first()).toBeVisible({ timeout: 10000 });
  })
  test('verify updated quote', async ({ adminPage }) => {
    const updateQuoteData = {
      ...quoteData, 
      subject: quoteData.subject + " - Updated",
      title:"updated title",
      quoteNumber:"updated quote number",
      description:"updated description",
      notes:"updated notes",
      
    }
    const quote = new QuotesPage(adminPage);
    await quote.navigateToQuotesPage();
    await quote.searchByName(quoteData.subject);
    await quote.updateQuote(updateQuoteData);
  })
  test('verify delete quote', async ({ adminPage }) => {
    const quote = new QuotesPage(adminPage);
    await quote.navigateToQuotesPage();
    await quote.searchByName(quoteData.subject);
    await quote.deleteQuote();
  })

})
