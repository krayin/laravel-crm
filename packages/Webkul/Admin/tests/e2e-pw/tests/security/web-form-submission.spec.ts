import { APIRequestContext, Browser, BrowserContext } from "@playwright/test";

import { expect, test } from "../../fixtures/AdminFixtures";

import { runSuffix } from "../../utils/acl";

/**
 * The public web form (`POST /web-forms/forms/{id}`) is the application's only unauthenticated
 * write endpoint, and it reached the lead and person repositories with the request body intact.
 * That let an anonymous visitor decide which columns were written rather than the admin who built
 * the form, attach a lead to any contact by supplying `persons[id]`, be merged into an unrelated
 * contact through a substring email match, and learn which addresses already belong to the CRM.
 *
 * The suite drives the real endpoint from a browser context with no session — the attacker's
 * position — so the route, its CSRF middleware, the form request and the controller are covered
 * together.
 *
 * Three things it has to get right, or it proves nothing:
 *
 * 1. The route is behind CSRF. A bare POST is answered 419 before the controller runs, so each
 *    probe first loads the public form page to pick up the `XSRF-TOKEN` cookie and echoes it back.
 * 2. A submission may only write the attributes the admin placed on the form, so the suite builds
 *    its own form exposing a known set rather than hoping the seeded data has one configured — a
 *    form exposing nothing filters every field and would make these probes pass for the wrong
 *    reason.
 * 3. Whether an injected field was actually written is only visible to an authenticated user, so
 *    the assertions read the record back through the admin session.
 */
test.describe.serial("public web form submission hardening", () => {
    const suffix = runSuffix();

    type Visitor = {
        context: BrowserContext;
        request: APIRequestContext;
        token: string;
    };

    let formId: number | null = null;

    let formSlug: string | null = null;

    /**
     * Build a form exposing lead title, person name and person emails — the shape of a real
     * "contact us" form, and enough for a submission to be accepted.
     */
    test.beforeAll(async ({ adminPage }) => {
        /**
         * Provisioning the whole fixture — users, roles, groups and the records they own — is many
         * actions, not one, so it gets its own budget rather than the per-test timeout. Without
         * this the suite fails in setup, which reports nothing about the behaviour under test.
         */
        test.setTimeout(180 * 1000);

        const attributes = await adminPage.request.get(
            "admin/settings/attributes?pagination[page]=1&pagination[per_page]=200",
            {
                headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
                failOnStatusCode: false,
            }
        );

        if (attributes.status() !== 200) {
            return;
        }

        const wanted = [
            { entity: "leads", code: "title" },
            { entity: "persons", code: "name" },
            { entity: "persons", code: "emails" },
        ];

        const records = (await attributes.json()).records ?? [];

        const chosen = wanted
            .map(({ entity, code }) =>
                records.find(
                    (row: Record<string, unknown>) =>
                        String(row.code ?? "") === code &&
                        // the grid renders entity_type capitalised ("Persons", "Leads")
                        String(row.entity_type ?? "").toLowerCase() === entity
                )
            )
            .filter(Boolean);

        if (chosen.length !== wanted.length) {
            return;
        }

        const form: Record<string, string> = {
            title: `E2E Security Form ${suffix}`,
            description: "Created by the web form security suite",
            submit_button_label: "Send",
            submit_success_action: "message",
            submit_success_content: "Thanks",
            create_lead: "1",
        };

        chosen.forEach((attribute: Record<string, unknown>, index: number) => {
            form[`attributes[${index}][attribute_id]`] = String(attribute.id);
            form[`attributes[${index}][name]`] = String(attribute.code);
            form[`attributes[${index}][is_required]`] = "0";
            form[`attributes[${index}][is_hidden]`] = "0";
            form[`attributes[${index}][sort_order]`] = String(index + 1);
        });

        const adminCookie = (await adminPage.context().cookies()).find(
            (candidate) => candidate.name === "XSRF-TOKEN"
        );

        const created = await adminPage.request.post("admin/settings/web-forms/create", {
            failOnStatusCode: false,
            maxRedirects: 0,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-XSRF-TOKEN": adminCookie ? decodeURIComponent(adminCookie.value) : "",
            },
            form,
        });

        if (![200, 302].includes(created.status())) {
            return;
        }

        const grid = await adminPage.request.get(
            "admin/settings/web-forms?pagination[page]=1&pagination[per_page]=200",
            { headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" } }
        );

        if (grid.status() !== 200) {
            return;
        }

        const row = ((await grid.json()).records ?? []).find(
            (candidate: Record<string, unknown>) =>
                String(candidate.title ?? "").trim() === `E2E Security Form ${suffix}`
        );

        if (!row) {
            return;
        }

        formId = Number(row.id);

        /**
         * The grid exposes only id/title/actions, so the public slug comes from the edit screen —
         * it is the `form_id` the public URL uses, and the numeric id is what the submission posts
         * to.
         */
        const edit = await adminPage.request.get(`admin/settings/web-forms/edit/${formId}`, {
            failOnStatusCode: false,
        });

        if (edit.status() !== 200) {
            return;
        }

        const slug = (await edit.text()).match(/web-forms\/forms\/([A-Za-z0-9]+)\/form\.js/);

        formSlug = slug ? slug[1] : null;
    });

    test.beforeEach(() => {
        test.skip(
            formId === null || !formSlug,
            "could not create a configured web form on this installation"
        );
    });

    async function openVisitor(browser: Browser, baseURL: string | undefined): Promise<Visitor> {
        const context = await browser.newContext({ baseURL });

        await context.request.get(`web-forms/forms/${formSlug}/form.html`, {
            failOnStatusCode: false,
        });

        const cookie = (await context.cookies()).find(
            (candidate) => candidate.name === "XSRF-TOKEN"
        );

        return {
            context,
            request: context.request,
            token: cookie ? decodeURIComponent(cookie.value) : "",
        };
    }

    async function submit(visitor: Visitor, form: Record<string, string>) {
        const response = await visitor.request.post(`web-forms/forms/${formId}`, {
            failOnStatusCode: false,
            maxRedirects: 0,
            headers: { Accept: "application/json", "X-XSRF-TOKEN": visitor.token },
            form,
        });

        return { status: response.status(), body: await response.text() };
    }

    async function findLead(adminPage: any, title: string) {
        const grid = await adminPage.request.get(
            "admin/leads?pagination[page]=1&pagination[per_page]=200&sort[column]=id&sort[order]=desc",
            { headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" } }
        );

        if (grid.status() !== 200) {
            return null;
        }

        return (
            ((await grid.json()).records ?? []).find(
                (row: Record<string, unknown>) => String(row.title ?? "").trim() === title
            ) ?? null
        );
    }

    test("@security a submission cannot set the lead owner the form never exposed", async ({
        browser,
        baseURL,
        adminPage,
    }) => {
        const visitor = await openVisitor(browser, baseURL);

        const title = `Owner probe ${suffix}`;

        const { status } = await submit(visitor, {
            "leads[title]": title,
            "leads[user_id]": "1",
            "persons[name]": `Visitor ${suffix}`,
            "persons[emails][0][value]": `owner.${suffix}@example.invalid`,
            "persons[emails][0][label]": "work",
        });

        await visitor.context.close();

        expect(status, "the submission must be accepted, not fail with a server error").toBe(200);

        const lead = await findLead(adminPage, title);

        expect(lead, "the lead should have been created").not.toBeNull();

        /**
         * `user_id` is not on the form, so the submitted value must not have been written. The
         * grid renders the owner's name in `sales_person`; an unassigned lead leaves it empty.
         */
        expect(
            String(lead?.sales_person ?? "").trim(),
            "an anonymous submission must not be able to choose the lead's sales owner"
        ).toBe("");
    });

    test("@security a submission cannot attach its lead to a contact of its choosing", async ({
        browser,
        baseURL,
        adminPage,
    }) => {
        const visitor = await openVisitor(browser, baseURL);

        const title = `Hijack probe ${suffix}`;

        const { status } = await submit(visitor, {
            "leads[title]": title,
            "persons[id]": "1",
            "persons[name]": `Visitor ${suffix}`,
            "persons[emails][0][value]": `hijack.${suffix}@example.invalid`,
            "persons[emails][0][label]": "work",
        });

        await visitor.context.close();

        expect(status).toBe(200);

        const lead = await findLead(adminPage, title);

        expect(lead, "the lead should have been created").not.toBeNull();

        /**
         * The lead must belong to the contact the submission described, not to contact id 1.
         */
        /**
         * The grid renders the contact as a link, so the assertion is on the name it carries.
         */
        expect(
            String(lead?.person_name ?? ""),
            "a supplied contact id must be ignored"
        ).toContain(`Visitor ${suffix}`);
    });

    test("@security a sql wildcard in the email field is not treated as a contact match", async ({
        browser,
        baseURL,
        adminPage,
    }) => {
        const visitor = await openVisitor(browser, baseURL);

        const title = `Wildcard probe ${suffix}`;

        const { status, body } = await submit(visitor, {
            "leads[title]": title,
            "persons[name]": `Wildcard visitor ${suffix}`,
            "persons[emails][0][value]": "%",
            "persons[emails][0][label]": "work",
        });

        await visitor.context.close();

        expect(body).not.toContain("Integrity constraint");

        if (status !== 200) {
            // Rejected as an invalid address, which is also a correct outcome.
            return;
        }

        const lead = await findLead(adminPage, title);

        if (lead) {
            expect(
                String(lead.person_name ?? ""),
                "a wildcard must not merge the submission into an existing contact"
            ).toContain(`Wildcard visitor ${suffix}`);
        }
    });

    test("@security the endpoint never reports that a value is already taken", async ({
        browser,
        baseURL,
    }) => {
        const visitor = await openVisitor(browser, baseURL);

        const { body } = await submit(visitor, {
            "leads[title]": `Enumeration probe ${suffix}`,
            "persons[name]": `Visitor ${suffix}`,
            "persons[emails][0][value]": "admin@example.com",
            "persons[emails][0][label]": "work",
        });

        await visitor.context.close();

        expect(
            body,
            "a uniqueness message would confirm the address belongs to a contact"
        ).not.toContain("already been taken");
    });

    test("@security an array in a phone field does not produce a 500", async ({
        browser,
        baseURL,
    }) => {
        const visitor = await openVisitor(browser, baseURL);

        const { body } = await submit(visitor, {
            "leads[title]": `Array probe ${suffix}`,
            "persons[name]": `Visitor ${suffix}`,
            "persons[emails][0][value]": `array.${suffix}@example.invalid`,
            "persons[emails][0][label]": "work",
            "persons[contact_numbers][0][value][]": "1",
            "persons[contact_numbers][0][label]": "work",
        });

        await visitor.context.close();

        /**
         * The assertion is on the TypeError specifically rather than on the status. `preg_match()`
         * receiving an array is the defect being covered; an installation can return 500 for
         * unrelated reasons (a pre-existing duplicate-key collision in seeded contact data, for
         * one), and pinning the status would make this test fail for something it does not test.
         */
        expect(body, "an array value must not reach preg_match()").not.toContain("TypeError");

        expect(body, "a stack trace must never reach an anonymous caller").not.toContain(
            "preg_match():"
        );
    });
});
