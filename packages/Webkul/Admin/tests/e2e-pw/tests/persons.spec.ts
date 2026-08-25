import { expect, test } from "../fixtures/AdminFixtures";
import PersonsPage, { personData } from "../pages/PersonsPage";


test.describe("person managment", async () => {
    test("verify create person", async ({ adminPage }) => {
        const personPage = new PersonsPage(adminPage);

        await personPage.navigageToPersonsPage();
        await personPage.createPerson(personData);


    })
    test('verify edit person',async({adminPage})=>{
        const person=new PersonsPage(adminPage);
        await person.navigageToPersonsPage();
        await person.updatePerson(personData);

        
    })
    test('verify person delete',async({adminPage})=>{
        const person=new PersonsPage(adminPage);

        await person.navigageToPersonsPage();
        await person.searchByName(personData.name);
        await person.personDelete();
    } )

    test('verify mass delete person',async({adminPage})=>{
        const person= new PersonsPage(adminPage);
        await person.navigageToPersonsPage();
        await person.createPerson(personData);
        await person.personMassDelete();

    })
  


})

