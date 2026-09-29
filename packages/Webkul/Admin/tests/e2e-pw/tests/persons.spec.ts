import { expect, test } from "../fixtures/AdminFixtures";
import PersonsPage, { buildPersonData } from "../pages/PersonsPage";

test.describe("person managment", async () => {
    /**
     * This spec builds its own person rather than importing the shared `personData`. Email and
     * contact number are unique in Krayin, and the lead and quote specs create a person too — with
     * one shared object all three submit the same email and the later ones fail validation with
     * "The value has already been taken."
     *
     * The instance is created once for the whole describe so that create, edit and delete all
     * address the same record.
     */
    const personData = buildPersonData();

    test("verify create person", async ({ adminPage }) => {
        const personPage = new PersonsPage(adminPage);

        await personPage.navigageToPersonsPage();
        await personPage.createPerson(personData);
    })

    test('verify edit person', async ({ adminPage }) => {
        const person = new PersonsPage(adminPage);

        await person.navigageToPersonsPage();
        await person.updatePerson(personData);
    })

    test('verify person delete', async ({ adminPage }) => {
        const person = new PersonsPage(adminPage);

        await person.navigageToPersonsPage();
        await person.searchByName(personData.name);
        await person.personDelete();
    })

    test('verify mass delete person', async ({ adminPage }) => {
        const person = new PersonsPage(adminPage);

        /**
         * A person of its own: the record created above has just been deleted, but reusing its
         * email would still collide if the delete case is skipped or run out of order.
         */
        const massDeletePersonData = buildPersonData();

        await person.navigageToPersonsPage();
        await person.createPerson(massDeletePersonData);
        await person.personMassDelete();
    })
})
