import { LegacyCharacterEncoding } from "crypto";
import { expect, Locator, Page } from "playwright/test";

type ElementType = "button" | "textbox" | "link";

export default class CoreLocators {
    readonly page: Page;

    // Person Locators
    readonly createPersonLink: Locator;
    readonly savePersonButton: Locator;
    readonly personNameTextbox: Locator;
    readonly personEmailTextbox: Locator;
    readonly personPhoneTextbox: Locator;
    readonly personJobTitleTextbox: Locator;
    readonly personOrgSelectDiv: Locator;
    readonly personOrgListItem: (text: string) => Locator;

    // Links and buttons
    readonly createOrgLink: Locator;
    readonly saveOrganizationButton: Locator;

    // Textboxes
    readonly orgNameTextbox: Locator;
    readonly orgCityTextbox: Locator;
    readonly orgPostcodeTextbox: Locator;

    // Textareas
    readonly orgAddressTextarea: Locator;

    // Select dropdowns
    readonly orgCountryCombobox: Locator;
    readonly orgStateSelect: Locator;

    // Other UI elements
    readonly orgExtraDetailsDiv: Locator;
    readonly orgExampleListItem: (text: string) => Locator;

    // Buttons
    readonly saveQuoteButton: Locator;
    readonly saveProductsButton: Locator;
    readonly confirmDeleteButton: Locator;
    readonly saveLeadButton: Locator;
    readonly sendEmailButton: Locator;
    readonly saveFileButton: Locator;
    readonly saveNoteButton: Locator;
    readonly saveActivityButton: Locator;
    readonly signInButton: Locator;
    readonly createEventButton: Locator;
    readonly saveEventButton: Locator;
    readonly agreeButton: Locator;
    readonly createTypeButton: Locator;
    readonly saveTypeButton: Locator;
    readonly createGroupButton: Locator;
    readonly saveGroupButton: Locator;
    readonly createUserButton: Locator;
    readonly saveUserButton: Locator;
    readonly composeMailButton: Locator;
    readonly createOrganizationButton: Locator;
    readonly deleteButton: Locator;

    // Links
    readonly createQuoteLink: Locator;
    readonly createProductLink: Locator;
    readonly createLeadLink: Locator;
    readonly signOutLink: Locator;
    readonly createOrganizationLink: Locator;

    // Textboxes - single line text input fields
    readonly subjectTextbox: Locator;
    readonly descriptionTextbox: Locator;
    readonly searchInputField: Locator;
    readonly productNameTextbox: Locator;
    readonly productPriceInput: Locator;
    readonly productQuantityInput: Locator;
    readonly productSkuInput: Locator;
    readonly leadTitleInput: Locator;
    readonly leadValueInput: Locator;
    readonly leadProductSelect: Locator;
    readonly emailReplyToInput: Locator;
    readonly emailSubjectInput: Locator;
    readonly fileTitleInput: Locator;
    readonly fileNameInput: Locator;
    readonly noteCommentTextarea: Locator;
    readonly activityTitleInput: Locator;
    readonly activityLocationInput: Locator;
    readonly eventNameInput: Locator;
    readonly typeNameInput: Locator;
    readonly groupNameInput: Locator;
    readonly userNameInput: Locator;
    readonly userEmailInput: Locator;
    readonly userPasswordInput: Locator;
    readonly userConfirmPasswordInput: Locator;
    readonly organizationNameInput: Locator;
    readonly organizationCityTextbox: Locator;
    readonly organizationAddressTextarea: Locator;
    readonly organizationPostcodeTextbox: Locator;
    readonly organizationCountryCombobox: Locator;
    readonly organizationStateSelect: Locator;

    readonly addPersonButton: Locator;

    // Textboxes - date or specialized text input
    readonly expiredAtTextbox: Locator;
    readonly leadExpectedCloseDateInput: Locator;
    readonly activityScheduleFromInput: Locator;
    readonly activityScheduleToInput: Locator;
    readonly eventDateInput: Locator;

    // Textareas - multi-line input fields
    readonly billingAddressTextarea: Locator;
    readonly shippingAddressTextarea: Locator;
    readonly productDescriptionTextarea: Locator;
    readonly leadDescriptionTextarea: Locator;
    readonly emailReplyTextarea: Locator;
    readonly fileCommentTextarea: Locator;
    readonly noteTextarea: Locator;
    readonly activityCommentTextarea: Locator;
    readonly eventDescriptionTextarea: Locator;
    readonly groupDescriptionTextarea: Locator;

    // Inputs - single line, no explicit role
    readonly billingCityInput: Locator;
    readonly billingPostcodeInput: Locator;
    readonly shippingCityInput: Locator;
    readonly shippingPostcodeInput: Locator;
    readonly leadEmailInput: Locator;
    readonly leadPhoneNumberInput: Locator;

    // Select dropdowns
    readonly salesOwnerSelect: Locator;
    readonly billingCountrySelect: Locator;
    readonly billingStateSelect: Locator;
    readonly shippingCountrySelect: Locator;
    readonly shippingStateSelect: Locator;
    readonly leadSourceSelect: Locator;
    readonly leadTypeSelect: Locator;
    readonly leadUserSelect: Locator;
    readonly leadSourceLabelSelect: Locator;
    readonly leadOrganizationSelect: Locator;
    readonly userRoleSelect: Locator;
    readonly userViewPermissionSelect: Locator;
    readonly userGroupListbox: Locator;
    readonly leadProductAddMoreButton: Locator;
    readonly quoteLinkToLeadButton: Locator;

    // Search related list item selector
    readonly personListItem: (name: string) => Locator;

    // Lead form - person search dropdown
    readonly leadPersonSearchInput: Locator;

    // Lead form - product search dropdown
    readonly leadProductSearchInput: Locator;

    readonly quoteAddPersonButton: Locator;
    readonly addAsNewButton: Locator;
    readonly quoteSelectListPerson: Locator;

    // Edit and delete icons
    readonly firstEditIcon: Locator;
    readonly firstDeleteIcon: Locator;
    // Multi select checkbox
    readonly multiSelectCheckbox: Locator;

    // Activity modal selectors
    readonly addActivityButton: Locator;
    readonly activityCallOption: Locator;
    readonly activityMeetingOption: Locator;
    readonly activityLunchOption: Locator;

    // Login fields
    readonly emailPlaceholderInput: Locator;
    readonly passwordPlaceholderInput: Locator;
    readonly megaSearchPlaceholder: Locator;

    // Profile and logout buttons
    readonly profileButton: Locator;
    // Lead
    readonly createLeadButton: Locator;

    // Lead form
    readonly titleInput: Locator;
    readonly descriptionTextarea: Locator;
    readonly sourceDropdown: Locator;
    readonly expectedCloseDate: Locator;
    readonly typeDropdown: Locator;
    readonly userDropdown: Locator;
    readonly searchInput: Locator;
    readonly listSearchInput: Locator;

    // Add person
    readonly personEmailInput: Locator;
    readonly personPhoneInput: Locator;
    readonly successMessage: Locator;

    // Add organization
    readonly addOrganizationButton: Locator;
    readonly organizationCreateSuccessMessage: Locator;

    // General
    readonly leadSuccessToast: Locator;
    readonly editLeadButton: Locator;
    readonly listViewButton: Locator;
    readonly deleteLeadButton: Locator;
    readonly noRecordsAvailable: Locator
    readonly searchInputExact:Locator;
    readonly listViewLeadSearch: Locator;
    // Tabs
    readonly mailButton: Locator;
    readonly fileButton: Locator;
    readonly noteButton: Locator;
    readonly activityButton: Locator;

    // Mail
    readonly mailToInput: Locator;
    readonly mailSubjectInput: Locator;
    readonly mailBodyTextarea: Locator;
    readonly sendMailButton: Locator;

    // File

    // Note

    // Activity
    readonly addActivityTitleInput: Locator;
    readonly addActivityCommentTextarea: Locator;
    readonly scheduleFromInput: Locator;
    readonly scheduleToInput: Locator;
    readonly locationInput: Locator;

    // Validation message
    readonly expectedCloseDateMustBeDateAfter: Locator;



    // Other
    readonly appLocator: Locator;

    // Quick Add
    readonly quickAddTrigger: Locator;
    readonly quickAddModalTitle: Locator;
    readonly quickAddTab: (tabLabel: string) => Locator;
    readonly quickAddSaveButton: Locator;
    
    readonly quickAddLeadForm: Locator;
    readonly quickAddPersonForm: Locator;
    readonly quickAddOrganizationForm: Locator;
    readonly quickAddProductForm: Locator;

    readonly quickAddLeadTitleInput: Locator;
    readonly quickAddLeadDescriptionTextarea: Locator;

    readonly quickAddPersonNameInput: Locator;
    readonly quickAddPersonEmailInput: Locator;
    readonly quickAddPersonContactInput: Locator;

    readonly quickAddOrgNameInput: Locator;

    readonly quickAddProductNameInput: Locator;
    readonly quickAddProductDescriptionTextarea: Locator;
    readonly quickAddProductSkuInput: Locator;
    readonly quickAddProductQuantityInput: Locator;
    readonly quickAddProductPriceInput: Locator;

    readonly quickAddLeadSuccessMsg: Locator;
    readonly quickAddPersonSuccessMsg: Locator;
    readonly quickAddOrgSuccessMsg: Locator;
    readonly quickAddProductSuccessMsg: Locator;

    constructor(page: Page) {
        this.page = page;

        // Buttons
        this.saveQuoteButton = page.getByRole("button", { name: "Save Quote" });
        this.saveProductsButton = page.getByRole("button", { name: "Save Products" });
        this.confirmDeleteButton = page.locator("button.transparent-button + button.primary-button:visible");
        this.saveLeadButton = page.getByRole("button", { name: "Save" });
        this.sendEmailButton = page.getByRole("button", { name: "Send" });
        this.saveFileButton = page.getByRole("button", { name: "Save File" });
        this.saveNoteButton = page.getByRole("button", { name: "Save Note" });
        this.saveActivityButton = page.getByRole("button", { name: "Save Activity" });
        this.signInButton = page.getByRole("button", { name: "Sign In" });
        this.createEventButton = page.getByRole("button", { name: "Create Event" });
        this.saveEventButton = page.getByRole("button", { name: "Save Event" });
        this.agreeButton = page.getByRole("button", { name: "Agree", exact: true });
        this.createTypeButton = page.getByRole("button", { name: "Create Type" });
        this.saveTypeButton = page.getByRole("button", { name: "Save Type" });
        this.createGroupButton = page.getByRole("button", { name: "Create group" });
        this.saveGroupButton = page.getByRole("button", { name: "Save Group" });
        this.createUserButton = page.getByRole("button", { name: "Create User" });
        this.saveUserButton = page.getByRole("button", { name: "Save User" });
        this.composeMailButton = page.getByRole("button", { name: "Compose Mail" });
        this.createOrganizationButton = page.getByRole("link", { name: "Create Organization" });
        this.saveOrganizationButton = page.getByRole("button", { name: "Save Organization" });
        this.deleteButton = page.getByRole("button", { name: "Delete" });
        this.leadProductSelect = page.locator('.mb-4.\\!mb-0 > div > .relative.inline-block > .relative').first();
        this.leadProductAddMoreButton = page.locator('//button[@class="flex max-w-max items-center gap-2 text-brandColor"]');
        this.leadOrganizationSelect = page.locator("div").filter({ hasText: /^Click to add$/ }).nth(2);
        this.addPersonButton = page.locator("div", { hasText: /^Click to Add$/ }).nth(1);
        this.quoteAddPersonButton = page.locator(".relative.flex.items-center.justify-between").first();

        // Links
        this.createQuoteLink = page.getByRole("link", { name: "Create Quote" });
        this.createProductLink = page.getByRole("link", { name: "Create Product" });
        this.createLeadLink = page.getByRole("link", { name: "Create Lead" });
        this.signOutLink = page.getByRole("link", { name: "Sign Out" });
        this.createOrganizationLink = page.getByRole("link", { name: "Create Organization" });

        this.addAsNewButton = page.getByText("Add as New");
        this.quoteSelectListPerson = page.locator(`(//li[@class="flex cursor-pointer gap-2 p-2 transition-colors hover:bg-blue-100 dark:text-gray-300 dark:hover:bg-gray-900"])[1]`);

        // Textboxes - single line text input fields
        this.subjectTextbox = page.getByRole("textbox", { name: "Subject *" });
        this.descriptionTextbox = page.getByRole("textbox", { name: "Description" });

        this.productNameTextbox = page.getByRole("textbox", { name: "Name *" });
        this.productPriceInput = page.locator('input[name="price"]');
        this.productQuantityInput = page.locator('input[name="quantity"]');
        this.productSkuInput = page.locator('input[name="sku"]');
        this.leadTitleInput = page.locator('input[name="title"]');
        this.leadValueInput = page.locator('input[name="lead_value"]');
        this.emailReplyToInput = page.locator('input[name="temp-reply_to"]');
        this.emailSubjectInput = page.locator('input[name="subject"]');
        this.fileTitleInput = page.locator('input[name="title"]');
        this.fileNameInput = page.locator('input[name="name"]');
        this.noteCommentTextarea = page.locator('textarea[name="comment"]');
        this.activityTitleInput = page.locator('input[name="title"]');
        this.activityLocationInput = page.locator('input[name="location"]');
        this.eventNameInput = page.locator('input[name="name"]');
        this.typeNameInput = page.locator('input[name="name"]');
        this.groupNameInput = page.locator('input[name="name"]');
        this.userNameInput = page.locator('input[name="name"]');
        this.userEmailInput = page.locator('input[name="email"]');
        this.userPasswordInput = page.locator('input[name="password"]');
        this.userConfirmPasswordInput = page.locator('input[name="confirm_password"]');
        this.organizationNameInput = page.locator('input[name="name"]');
        this.organizationCityTextbox = page.getByRole('textbox', { name: 'City' });
        this.organizationAddressTextarea = page.locator('textarea[name="address\\[address\\]"]');
        this.organizationPostcodeTextbox = page.getByRole('textbox', { name: 'Postcode' });
        this.organizationCountryCombobox = page.getByRole('combobox');
        this.organizationStateSelect = page.locator('select[name="address\\[state\\]"]');

        // Textboxes - date or specialized text input
        this.expiredAtTextbox = page.getByRole("textbox", { name: "Expired At *" });
        this.leadExpectedCloseDateInput = page.locator('input[name="expected_close_date"]');
        this.activityScheduleFromInput = page.locator('input[name="schedule_from"]');
        this.activityScheduleToInput = page.locator('input[name="schedule_to"]');
        this.eventDateInput = page.locator('input[name="date"]');

        this.noRecordsAvailable = page.getByText('No Records Available.')

        // Textareas - multi-line input fields
        this.billingAddressTextarea = page.locator('textarea[name="billing_address\\[address\\]"]');
        this.shippingAddressTextarea = page.locator('textarea[name="shipping_address\\[address\\]"]');
        this.productDescriptionTextarea = page.locator('textarea[name="description"]');
        this.leadDescriptionTextarea = page.locator('textarea[name="description"]');
        this.emailReplyTextarea = page.locator('textarea[name="reply"]');
        this.fileCommentTextarea = page.locator('textarea[name="comment"]');
        this.noteTextarea = page.locator('textarea[name="comment"]');
        this.activityCommentTextarea = page.locator('textarea[name="comment"]');
        this.eventDescriptionTextarea = page.locator('textarea[name="description"]');
        this.groupDescriptionTextarea = page.locator('textarea[name="description"]');

        // Inputs - single line, no explicit role
        this.billingCityInput = page.locator('input[name="billing_address\\[city\\]"]');
        this.billingPostcodeInput = page.locator('input[name="billing_address\\[postcode\\]"]');
        this.shippingCityInput = page.locator('input[name="shipping_address\\[city\\]"]');
        this.shippingPostcodeInput = page.locator('input[name="shipping_address\\[postcode\\]"]');
        this.leadEmailInput = page.locator('input[name="person[emails][0][value]"]');
        this.leadPhoneNumberInput = page.locator('input[name="person[contact_numbers][0][value]"]');

        // Select dropdowns
        this.salesOwnerSelect = page.getByLabel("Sales Owner");
        this.billingCountrySelect = page.locator('select[name="billing_address\\[country\\]"]');
        this.billingStateSelect = page.locator('select[name="billing_address\\[state\\]"]');
        this.shippingCountrySelect = page.locator('select[name="shipping_address\\[country\\]"]');
        this.shippingStateSelect = page.locator('select[name="shipping_address\\[state\\]"]');
        this.leadSourceSelect = page.locator('select[name="lead_source_id"]');
        this.leadTypeSelect = page.locator('select[name="lead_type_id"]');
        this.leadUserSelect = page.locator('select[name="user_id"]');
        this.leadSourceLabelSelect = page.getByLabel('Source');
        this.userRoleSelect = page.locator('select[name="role_id"]');
        this.userViewPermissionSelect = page.locator('select[name="view_permission"]');
        this.userGroupListbox = page.getByRole('listbox');

        // Search related list item selector
        this.personListItem = (name: string) => page.getByRole("listitem").filter({ hasText: name });

        // Edit and delete icons
        this.firstEditIcon = page.locator("span.cursor-pointer.icon-edit").first();
        this.firstDeleteIcon = page.locator("span.cursor-pointer.icon-delete").first();

        //mass delte checkbox
        this.multiSelectCheckbox = page.locator('.icon-checkbox-outline').first();

        this.quoteLinkToLeadButton = page.locator('div:nth-child(2) > div > .relative.inline-block > .relative');

        // Activity modal selectors
        this.addActivityButton = page.getByRole("button", { name: " Activity" });
        this.activityCallOption = page.getByText("Call", { exact: true });
        this.activityMeetingOption = page.getByText("Meeting", { exact: true });
        this.activityLunchOption = page.getByText("Lunch", { exact: true });

        // Login fields
        this.emailPlaceholderInput = page.getByPlaceholder("Email Address");
        this.passwordPlaceholderInput = page.getByPlaceholder("Password");
        this.megaSearchPlaceholder = page.getByPlaceholder("Mega Search").first();

        // Profile and logout buttons
        this.profileButton = page.getByRole('button', { name: "E" });

        // Organization Locators Initialization
        this.createOrgLink = page.getByRole('link', { name: 'Create Organization' });
        this.orgNameTextbox = page.getByRole('textbox', { name: 'Name *' });
        this.orgAddressTextarea = page.locator('textarea[name="address\\[address\\]"]');
        this.orgCountryCombobox = page.getByRole('combobox');
        this.orgStateSelect = page.locator('select[name="address\\[state\\]"]');
        this.orgCityTextbox = page.getByRole('textbox', { name: 'City' });
        this.orgPostcodeTextbox = page.getByRole('textbox', { name: 'Postcode' });

        this.orgExtraDetailsDiv = page.locator('div').filter({ hasText: /^Click to add$/ });
        this.orgExampleListItem = (text: string) => page.getByRole('listitem').filter({ hasText: text });

        // Person Locators Initialization
        this.createPersonLink = page.getByRole('link', { name: 'Create Person' });
        this.savePersonButton = page.getByRole('button', { name: 'Save Person' });
        this.personNameTextbox = page.getByRole('textbox', { name: 'Name *' });
        this.personEmailTextbox = page.getByRole('textbox', { name: 'Emails *' });
        this.personPhoneTextbox = page.getByRole('textbox', { name: 'Contact Numbers' });
        this.personJobTitleTextbox = page.getByRole('textbox', { name: 'Job Title' });
        this.personOrgSelectDiv = page.locator('.relative > div > .relative').first();

        this.personOrgListItem = (text: string) => page.getByRole('listitem').filter({ hasText: text });


        // Lead
        this.createLeadButton = page.getByRole('link', { name: 'Create Lead' });

        // Lead form
        this.titleInput = page.getByRole('textbox', { name: 'Title *' });
        this.descriptionTextarea = page.locator('textarea[name="description"]');
        this.sourceDropdown = page.locator('select[name="lead_source_id"]');
        this.expectedCloseDate = page.locator('input[name="expected_close_date"]');
        this.typeDropdown = page.locator('select[name="lead_type_id"]');
        this.userDropdown = page.locator('select[name="user_id"]');
        this.leadValueInput = page.locator('input[name="lead_value"]');
        this.listViewLeadSearch= page.getByRole('textbox', { name: 'Search', exact: true });
        this.searchInput = page.getByRole('textbox', { name: 'Search by Title' });
        this.listSearchInput = page.getByRole('textbox', { name: 'Search' });

        // Lead form - person search dropdown
        this.leadPersonSearchInput = page.getByPlaceholder("Search by name, email and number");

        // Lead form - product search dropdown
        this.leadProductSearchInput = page.getByRole('textbox', { name: 'Product Name' });

        // Add person
  
        this.personEmailInput = page.locator('input[name="person[emails][0][value]"]');
        this.personPhoneInput = page.locator('input[name="person[contact_numbers][0][value]"]');
        this.successMessage = page.getByText('Success');

        // Add organization
        this.addOrganizationButton = page.locator('div', { hasText: /^Click to add$/ }).nth(2);


        this.saveLeadButton = page.getByRole('button', { name: 'Save' });
        this.organizationCreateSuccessMessage = page.getByText('Organization created successfully');

        // General

        this.leadSuccessToast = page.getByText('Success', { exact: true });
        this.editLeadButton = page.getByRole('link', { name: '' }).first();
        this.listViewButton = page.getByRole('link', { name: '' });
        this.agreeButton = page.getByRole('button', { name: 'Agree', exact: true });
        this.deleteLeadButton = page.locator(
            '.cursor-pointer.rounded-md.p-1\\.5.text-2xl.transition-all.hover\\:bg-gray-200.dark\\:hover\\:bg-gray-800.max-sm\\:place-self-center.icon-delete'
        ).first();
        this.searchInputField = page.getByRole("textbox", { name: "Search..." });
        this.searchInputExact = page.getByRole("textbox", { name: "Search", exact: true });


        // Tabs
        this.mailButton = page.getByRole('button', { name: ' Mail' });
        this.fileButton = page.getByRole('button', { name: ' File' });
        this.noteButton = page.getByRole('button', { name: ' Note' });
        this.activityButton = page.getByRole('button', { name: ' Activity' });

        // Mail
        this.mailToInput = page.locator('input[name="temp-reply_to"]');
        this.mailSubjectInput = page.locator('input[name="subject"]');
        this.mailBodyTextarea = page.locator('textarea[name="reply"]');
        this.sendMailButton = page.getByRole('button', { name: 'Send' });

        // File
        this.fileTitleInput = page.locator('input[name="title"]');
        this.fileCommentTextarea = page.locator('textarea[name="comment"]');
        this.fileNameInput = page.locator('input[name="name"]');
        this.saveFileButton = page.getByRole('button', { name: 'Save File' });

        // Note
        this.noteCommentTextarea = page.locator('textarea[name="comment"]');
        this.saveNoteButton = page.getByRole('button', { name: 'Save Note' });

        // Activity
        this.addActivityTitleInput = page.locator('input[name="title"]');
        this.addActivityCommentTextarea = page.locator('textarea[name="comment"]');
        this.scheduleFromInput = page.locator('input[name="schedule_from"]');
        this.scheduleToInput = page.locator('input[name="schedule_to"]');
        this.locationInput = page.locator('input[name="location"]');
        this.saveActivityButton = page.getByRole('button', { name: 'Save Activity' });

        // Validation message
        this.expectedCloseDateMustBeDateAfter = page.getByText('The expected close date must be a date after');


        // Other
        this.appLocator = page.locator("#app");
        
        // Quick Add
        this.quickAddTrigger = page.locator("header button.bg-brandColor").first();
        this.quickAddModalTitle = page.locator("p").filter({ hasText: "Quick Add" });
        this.quickAddTab = (tabLabel: string) => page.locator("span.cursor-pointer").filter({ hasText: new RegExp(`^${tabLabel}$`) }).first();
        this.quickAddSaveButton = page.getByRole("button", { name: "Save", exact: true }).last();
        
        this.quickAddLeadForm = page.locator('form:has(input[value="lead"])').first();
        this.quickAddPersonForm = page.locator('form:has(input[value="person"])').first();
        this.quickAddOrganizationForm = page.locator('form:has(input[value="organization"])').first();
        this.quickAddProductForm = page.locator('form:has(input[value="product"])').first();

        this.quickAddLeadTitleInput = this.quickAddLeadForm.locator('input[name="title"]');
        this.quickAddLeadDescriptionTextarea = this.quickAddLeadForm.locator('textarea[name="description"]');

        this.quickAddPersonNameInput = this.quickAddPersonForm.locator('input[name="name"]');
        this.quickAddPersonEmailInput = this.quickAddPersonForm.locator('input[name="emails[0][value]"]');
        this.quickAddPersonContactInput = this.quickAddPersonForm.locator('input[name="contact_numbers[0][value]"]');

        this.quickAddOrgNameInput = this.quickAddOrganizationForm.locator('input[name="name"]');

        this.quickAddProductNameInput = this.quickAddProductForm.locator('input[name="name"]');
        this.quickAddProductDescriptionTextarea = this.quickAddProductForm.locator('textarea[name="description"]');
        this.quickAddProductSkuInput = this.quickAddProductForm.locator('input[name="sku"]');
        this.quickAddProductQuantityInput = this.quickAddProductForm.locator('input[name="quantity"]');
        this.quickAddProductPriceInput = this.quickAddProductForm.locator('input[name="price"]');

        this.quickAddLeadSuccessMsg = page.getByText("Lead created successfully.");
        this.quickAddPersonSuccessMsg = page.getByText("Person created successfully.");
        this.quickAddOrgSuccessMsg = page.getByText("Organization created successfully.");
        this.quickAddProductSuccessMsg = page.getByText("Product created successfully.");
    }
    async searchByName(name: string) {
        (await this.getElementByTypeAndName('textbox', 'Search')).fill(name);
        await this.page.keyboard.press('Enter');
    }

    async getElementByTypeAndName(type: ElementType, name: string) {
        return this.page.getByRole(`${type}`, { name: `${name}`, exact: true });
    }

    async selectListItmeByName(value: string) {
        return this.page.getByRole('listitem').filter({ hasText: `${value}` });
    }
    async massDelete() {
        await this.multiSelectCheckbox.click();
        if(await this.deleteButton.isVisible())  {
        await this.deleteButton.click();
        await this.agreeButton.click();
        }

        await expect(this.noRecordsAvailable).toBeVisible();

    }

}
