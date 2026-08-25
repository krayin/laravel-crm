import { test } from "../../fixtures/AdminFixtures";
import { AdminPage } from "../../pages/AdminPage";
import { WarehouseData, warehouseData, WarehousePage } from "../../pages/settings/WarehousesPage";
import { generateDescription, generateEmail, generateLastName, generatePhoneNumber } from "../../utils/faker";

test.describe('warehouse management', async () => {
    const updateWareshousedata:WarehouseData={
            name: generateLastName(),
            description: generateDescription().slice(0,20),
            contactName: generateLastName(),
            contactEmail:generateEmail(),
            contactAddress: 'Plot 123, Industrial Area, Meerut, UP 250001',
            country: 'India',
            state: 'Uttar Pradesh',
            city: 'Meerut',
            zipcode: '250001',
            phone: generatePhoneNumber(),
    }
    test('create warehouse', async ({ adminPage }) => {
        const warehouse = new WarehousePage(adminPage);
        await warehouse.navigateToWarehouses();
        await warehouse.warehouseCreateLink.click();
        await warehouse.warehouseForm(warehouseData);

    })
    test('verify update warehouse',async({adminPage})=>{
        const warehouse= new WarehousePage(adminPage);
        await warehouse.navigateToWarehouses();
        await warehouse.firstEditButton.click();
        await warehouse.warehouseForm(updateWareshousedata);
    })
    test('verify delete warehouse',async({adminPage})=>{
        const warehouse= new WarehousePage(adminPage);
        await warehouse.navigateToWarehouses();
        await warehouse.deleteWarehouse();
    })
})