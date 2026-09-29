import { expect, Page } from "playwright/test";
import CoreLocators from "../locator/CoreLocators";
import { generateDescription, generateName, generateSKU } from "../utils/faker";
import { saveData } from "../utils/savedata";
export type ProductData = {
  name: string,
  description: string,
  sku: string,
  price: string,
  priceInDollars?: string, // Optional if price is stored in a different currency
  quantity: string,
}
/**
 * Build a product with a SKU unique to this call.
 *
 * SKU is unique in Krayin, so two products built from one shared object cannot both be saved. The
 * product, lead and quote specs each create a product, so each needs its own instance — sharing one
 * makes whichever spec runs later fail on the product step.
 */
export function buildProductData(overrides: Partial<ProductData> = {}): ProductData {
  return {
    name: generateName(),
    description: generateDescription(),
    sku: generateSKU(),
    price: Math.floor(Math.random() * 1000).toString(),
    quantity: Math.floor(Math.random() * 100).toString(),
    ...overrides,
  };
}

/**
 * Default instance kept for existing importers. Do not create a product from this in more than one
 * spec — build a fresh instance instead.
 */
export const productData: ProductData = buildProductData();


export class ProductPage extends CoreLocators {


  page: Page;

  constructor(page: Page) {
    super(page);
    this.page = page
  }

  async navigateToProductPage() {

    await this.page.goto("admin/products");

  }
  async productForm(productdata: ProductData) {

    // Fill product form using this
    await this.productNameTextbox.waitFor({ state: "visible" });
    await this.productNameTextbox.click();
    await this.productNameTextbox.fill(productdata.name);

    await this.productDescriptionTextarea.fill("");
    await this.productDescriptionTextarea.type(productdata.description);

    await this.productSkuInput.fill(productdata.sku);

    await this.productPriceInput.fill(productdata.price);
    await this.productQuantityInput.fill(productdata.quantity);

    // Save product
    await this.saveProductsButton.click();
    await expect(this.successMessage.first()).toBeVisible();
    saveData("productData", productdata);
    // await this.searchProduct(productdata.name);   /* searching is not working now once the issue is fixed uncomment this line */
    // await expect(this.page.getByText(productdata.name).first()).toBeVisible();

  }
  async searchProduct(productName: string) {
    await this.searchInputExact.fill(productName);
    await this.page.keyboard.press('Enter');
  }
  async deleteProduct()
  {
    await this.firstDeleteIcon.click();
    await this.agreeButton.click();
    await expect(this.successMessage.first()).toBeVisible();
  }
}