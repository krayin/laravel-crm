import { test } from "../fixtures/AdminFixtures";
import { productData, ProductPage } from "../pages/ProductPage";

test.describe("Product mangement", async () => {
    test("verify create product", async ({ adminPage }) => {
        const product = new ProductPage(adminPage);

        await product.navigateToProductPage();
        await product.createProductLink.click();
        await product.productForm(productData);

    });
    test("verify edit product", async ({ adminPage }) => {
        const product = new ProductPage(adminPage);

        await product.navigateToProductPage();
        await product.firstEditIcon.click();
        const updatedProductData = {
            ...productData,
            name: productData.name + " - Updated",
            price: (parseInt(productData.price) + 100).toString(),
        }
        await product.productForm(updatedProductData);
        
    })
    test("verify delete product", async ({ adminPage }) => {
        const product = new ProductPage(adminPage);
        await product.navigateToProductPage();
        await product.deleteProduct();
    })
})