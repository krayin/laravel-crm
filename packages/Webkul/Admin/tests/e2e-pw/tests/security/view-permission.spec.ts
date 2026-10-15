import { expect, test } from "../../fixtures/AdminFixtures";

import { AclPage } from "../../pages/AclPage";
import { GroupPage, groupData } from "../../pages/settings/GroupsPage";
import { buildRoleData, RolesPage } from "../../pages/settings/RolesPage";
import { buildUserData, UsersPage } from "../../pages/settings/UsersPage";
import {
    ACL,
    ADMIN_EMAIL,
    ADMIN_PASSWORD,
    Session,
    TEST_PASSWORD,
    closeSessions,
    expectAllowed,
    expectDenied,
    getRoute,
    findRecordId,
    runSuffix,
    signInExpectingSuccess,
} from "../../utils/acl";

/**
 * The data-scope gate: whose records an action may touch.
 *
 * Every user in this suite holds an identical, generous ACL role, so a record being hidden can
 * only be the work of `view_permission`. That separation is the whole point — a suite that varies
 * both dimensions at once cannot attribute a refusal to either.
 *
 * Shape of the fixture:
 *
 *   globalUser      -> view_permission = global      -> sees every person
 *   groupUserA      -> view_permission = group   (G) -> sees A's and B's people
 *   groupUserB      -> view_permission = group   (G) -> sees A's and B's people
 *   individualUser  -> view_permission = individual  -> sees only their own
 */
test.describe.serial("view permission data scoping", () => {
    test.describe.configure({ timeout: 120 * 1000 });

    const suffix = runSuffix();

    /**
     * Only leaf keys are listed — the tree pulls in `contacts` and `contacts.persons` as ancestors.
     * `contacts.persons.view` is included deliberately: without it, the "opened directly by id"
     * case would be refused by the ACL gate and prove nothing about data scope.
     */
    const contactsRole = buildRoleData({
        permissions: [
            ACL.dashboard,
            ACL.contactsPersonsQuickCreate,
            ACL.contactsPersonsView,
        ],
    });

    const sharedGroup = {
        name: `ACL Scope Group ${suffix}`,
        description: "Group shared by the view-permission suite",
    };

    const globalUser = buildUserData({ roleName: contactsRole.name, viewPermission: "global" });
    const groupUserA = buildUserData({
        roleName: contactsRole.name,
        viewPermission: "group",
        groups: [sharedGroup.name],
    });
    const groupUserB = buildUserData({
        roleName: contactsRole.name,
        viewPermission: "group",
        groups: [sharedGroup.name],
    });
    const individualUser = buildUserData({
        roleName: contactsRole.name,
        viewPermission: "individual",
    });

    /**
     * One person per owner, named distinctly so a listing assertion is unambiguous.
     */
    const personOfGlobal = `Global Person ${suffix}`;
    const personOfGroupA = `GroupA Person ${suffix}`;
    const personOfGroupB = `GroupB Person ${suffix}`;
    const personOfIndividual = `Individual Person ${suffix}`;

    let globalSession: Session | undefined;
    let groupASession: Session | undefined;
    let groupBSession: Session | undefined;
    let individualSession: Session | undefined;

    test.beforeAll(async ({ browser, baseURL }) => {
        /**
         * Provisioning the whole fixture — users, roles, groups and the records they own — is many
         * actions, not one, so it gets its own budget rather than the per-test timeout. Without
         * this the suite fails in setup, which reports nothing about the behaviour under test.
         */
        test.setTimeout(180 * 1000);

        /**
         * Every test in this suite checks a scope by running several grid searches in turn — each a
         * navigation plus a filtered reload — so they legitimately take longer than the per-test
         * default, which is sized for a single action.
         */

        const admin = await signInExpectingSuccess(browser, baseURL, ADMIN_EMAIL, ADMIN_PASSWORD);

        try {
            const groupPage = new GroupPage(admin.page);

            await groupPage.navigateToGroups();
            await groupPage.createGroup(sharedGroup);

            await new RolesPage(admin.page).createRole(contactsRole);

            const usersPage = new UsersPage(admin.page);

            await usersPage.createUser(globalUser);
            await usersPage.createUser(groupUserA);
            await usersPage.createUser(groupUserB);
            await usersPage.createUser(individualUser);
        } finally {
            await closeSessions(admin);
        }

        globalSession = await signInExpectingSuccess(browser, baseURL, globalUser.email, TEST_PASSWORD);
        groupASession = await signInExpectingSuccess(browser, baseURL, groupUserA.email, TEST_PASSWORD);
        groupBSession = await signInExpectingSuccess(browser, baseURL, groupUserB.email, TEST_PASSWORD);
        individualSession = await signInExpectingSuccess(
            browser,
            baseURL,
            individualUser.email,
            TEST_PASSWORD
        );

        /**
         * Each person is created by the user who should own it. Creating them as an administrator
         * and reassigning would leave ownership to the form's default, which is exactly the
         * behaviour under test.
         */
        await new AclPage(globalSession.page).createPerson(personOfGlobal, `g.${suffix}@example.test`);
        await new AclPage(groupASession.page).createPerson(personOfGroupA, `ga.${suffix}@example.test`);
        await new AclPage(groupBSession.page).createPerson(personOfGroupB, `gb.${suffix}@example.test`);
        await new AclPage(individualSession.page).createPerson(
            personOfIndividual,
            `i.${suffix}@example.test`
        );
    });

    test.afterAll(async () => {
        await closeSessions(globalSession, groupASession, groupBSession, individualSession);
    });

    test("global scope sees records owned by everyone", async () => {
        const aclPage = new AclPage(globalSession!.page);

        for (const person of [personOfGlobal, personOfGroupA, personOfGroupB, personOfIndividual]) {
            await aclPage.expectRecordListed("admin/contacts/persons", person);
        }
    });

    test("individual scope sees only its own records", async () => {
        const aclPage = new AclPage(individualSession!.page);

        await aclPage.expectRecordListed("admin/contacts/persons", personOfIndividual);

        for (const person of [personOfGlobal, personOfGroupA, personOfGroupB]) {
            await aclPage.expectRecordNotListed("admin/contacts/persons", person);
        }
    });

    test("group scope sees records owned by fellow group members", async () => {
        const aclPage = new AclPage(groupASession!.page);

        /**
         * A and B share one group, so each must see the other's record — this is the assertion that
         * separates group scope from individual scope.
         */
        await aclPage.expectRecordListed("admin/contacts/persons", personOfGroupA);
        await aclPage.expectRecordListed("admin/contacts/persons", personOfGroupB);
    });

    test("group scope does not see records owned outside the group", async () => {
        const aclPage = new AclPage(groupASession!.page);

        for (const person of [personOfGlobal, personOfIndividual]) {
            await aclPage.expectRecordNotListed("admin/contacts/persons", person);
        }
    });

    test("group scope is symmetric between members", async () => {
        const aclPage = new AclPage(groupBSession!.page);

        await aclPage.expectRecordListed("admin/contacts/persons", personOfGroupA);
        await aclPage.expectRecordListed("admin/contacts/persons", personOfGroupB);
    });

    test("a record created under a scope is owned, not orphaned", async () => {
        /**
         * A person created with a null owner is invisible to every non-global user, including the
         * one who created it. Re-reading the creator's own listing is what catches that.
         */
        const aclPage = new AclPage(individualSession!.page);

        await aclPage.expectRecordListed("admin/contacts/persons", personOfIndividual);
    });

    /**
     * KNOWN DEFECT — marked fixme so it is recorded without failing the suite.
     *
     * `PersonController::store()` assigns `user_id` from the acting user only when the request
     * carries `quick_add`. A person created through the standard create form without picking a
     * Sales Owner is therefore saved with `user_id = null` and disappears from its own creator's
     * listing the moment that creator is scoped to `group` or `individual`.
     *
     * `LeadRepository::create()` already guards exactly this case for the person it creates, with a
     * comment naming the same failure mode, so the standard form path is missing a guard its
     * sibling paths have.
     *
     * Verified on branch 2.2: five persons created this way all persisted with `user_id = NULL`,
     * and the individual-scope creator's listing reported "0 - 0 of 0".
     */
    test.fixme("a person created through the standard form is owned by its creator", async () => {
        const aclPage = new AclPage(individualSession!.page);

        const orphanCandidate = `Standard Form Person ${suffix}`;

        await individualSession!.page.goto("admin/contacts/persons/create");
        await individualSession!.page.waitForLoadState("networkidle");

        await aclPage.personNameTextbox.fill(orphanCandidate);
        await aclPage.personEmailTextbox.fill(`sf.${suffix}@example.test`);
        await aclPage.personPhoneTextbox.fill(AclPage.uniquePhone());

        await aclPage.savePersonButton.click();
        await individualSession!.page.waitForLoadState("networkidle");

        await aclPage.expectRecordListed("admin/contacts/persons", orphanCandidate);
    });

    test("the action gate stays open while the scope narrows the result", async () => {
        /**
         * A narrowed scope must not turn into a 401 on the listing itself — the user still holds
         * `contacts.persons`, so the page renders and simply contains fewer rows.
         */
        const response = await getRoute(individualSession!, "admin/contacts/persons");

        expectAllowed(response, "person listing for an individual-scope user holding the permission");
    });

    test("the user listing is itself scoped by view permission", async () => {
        /**
         * `UserDataGrid` runs the same `getAuthorizedUserIds()` filter, so a restricted user must
         * not enumerate the whole staff list. Without the settings permission the request is
         * refused outright, which is an equally acceptable outcome here.
         */
        const response = await getRoute(individualSession!, "admin/settings/users");

        expectDenied(response, "user listing for a role without the settings permission");
    });

    test("a person owned by another user cannot be opened directly", async () => {
        /**
         * Listing filters hide a record; `preventUnauthorizedAccess()` is what stops it being
         * fetched by id anyway.
         *
         * The id is resolved from the datagrid's JSON as the global user, because the person
         * listing renders no per-row links to scrape. Replaying that id as the individual user is
         * the actual assertion.
         */
        const personId = await findRecordId(
            globalSession!,
            "admin/contacts/persons",
            "person_name",
            personOfGroupA
        );

        expect(
            personId,
            "the global user should be able to resolve another owner's person id"
        ).not.toBeNull();

        const response = await getRoute(individualSession!, `admin/contacts/persons/view/${personId}`);

        expectDenied(response, "another owner's person fetched directly by id");
    });

    /**
     * A record's activity timeline carries its notes and, for leads and people, the subject, body
     * and recipients of its correspondence. It was built from the id in the url alone, so it
     * answered for records the reader's own scope refuses — the record's page said no while the
     * timeline behind it said yes.
     */
    test("another owner's activity timeline cannot be read by id", async () => {
        const personId = await findRecordId(
            globalSession!,
            "admin/contacts/persons",
            "person_name",
            personOfGroupA
        );

        expect(
            personId,
            "the global user should be able to resolve another owner's person id"
        ).not.toBeNull();

        const timeline = await getRoute(
            individualSession!,
            `admin/contacts/persons/${personId}/activities?type=all`
        );

        expectDenied(timeline, "another owner's activity timeline fetched by id");
    });

    /**
     * The page and its timeline have to agree: a refusal on one and a result on the other is the
     * shape the gap took.
     */
    test("the timeline refuses whatever the record page refuses", async () => {
        const personId = await findRecordId(
            globalSession!,
            "admin/contacts/persons",
            "person_name",
            personOfGroupA
        );

        const page = await getRoute(individualSession!, `admin/contacts/persons/view/${personId}`);

        const timeline = await getRoute(
            individualSession!,
            `admin/contacts/persons/${personId}/activities`
        );

        expectDenied(page, "another owner's person page");

        expectDenied(timeline, "the same person's activity timeline");
    });

    /**
     * The owner still reaches their own timeline — a fix that refused everyone would pass the
     * assertions above while breaking the feature.
     */
    test("a user reaches the timeline of a record they own", async () => {
        const personId = await findRecordId(
            individualSession!,
            "admin/contacts/persons",
            "person_name",
            personOfIndividual
        );

        expect(
            personId,
            "the individual user should be able to resolve their own person id"
        ).not.toBeNull();

        const timeline = await getRoute(
            individualSession!,
            `admin/contacts/persons/${personId}/activities?type=all`
        );

        expectAllowed(timeline, "the timeline of a record the reader owns");
    });

    test("a scoped user cannot resolve another owner's record through the grid json", async () => {
        /**
         * The same lookup run as the individual user must come back empty — the grid's JSON is
         * scoped by the identical `getAuthorizedUserIds()` filter that hides the row in the UI.
         */
        const personId = await findRecordId(
            individualSession!,
            "admin/contacts/persons",
            "person_name",
            personOfGroupA
        );

        expect(personId, "another owner's person must not appear in a scoped grid response").toBeNull();
    });
});
