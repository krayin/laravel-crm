## **v1.1.2 (17th of September 2021)**


* #217 [fixed] - When we adding a new organization then it should not give an exception.

* #221 [fixed] - if the user has no permission to add a role and the user is trying to add a role. then it should be shown a warning

* #225 [fixed] - When we creating a person then after creating person it is not redirecting to persons list page

* #226 [fixed] - When we creating a user then the the 'asterisk icon' should visible on Confirm password label 

* #218 [fixed] - When we creating a new lead, Then after create lead it should be redirect to leads page

* #230 [fixed] - Automatic publish should be done at the time of installation



## **v1.1.0 (16th of September 2021)**

* [feature] Workflow (CRM Automation) feature implemented



* #132 [fixed] - filter option for mails are not working

* #162 [fixed] - Issue with seeder during installation

* #163 [fixed] - Lookup field is showing 2 times while creating the attribute

* #164 [fixed] - No option is coming to update lead status in grid view

* #165 [fixed] - Image type Attribute is not working

* #166 [fixed] - Getting exception when new user Login

* #172 [fixed] - If user have not Permission to delete the Roles and the user is trying to delete the Roles then the warning message should be present inside the error box

* #174 [fixed] - If the user Applying multiple filter to search sources then data should be searched.

* #175 [fixed] - After Added a new product the page should be redirect to product list page.

* #177 [fixed] - result: When admin/user adding new Person after deleting email input field. then required validation of email should be work

* #178 [fixed] - Getting Error " Class "Webklex\PHPIMAP\IMAP" not found" while integration of Google calender in Krayin

* #180 [fixed] - admin/user adding new Lead after deleting email input field inside Contact Person tab. then required validation of email should be work.

* #181 [fixed] - When admin/user run the **php artisan krayin-crm:version** command on root directory to check their Krayin version. then krayin version is there in the place of V

* #182 [fixed] - While Adding a new attribute Lookup field should be come only one time

* #184 [fixed] - When Customer running " composer create-project krayin/laravel-crm " and " php artisan krayin-crm:install ". command to install the laravel-crm. Then the project should be setup successfully .

* #186 [fixed] - When we creating person and selecting the value from the Attributes dropdown which is unique then the it should be give warning to user/admin

* #189 [fixed] - When we creating Lead and selecting the value from the Attributes dropdown which is unique then the it should be give warning to user/admin

* #193 [fixed] - When we clicking on locale dropdown inside configuration tab. there should be multiple options of languages

* #197 [fixed] - When we creating Quote. then the Quote should be show in dashboard

* #199 [fixed] - When we changing timezone from timezone dropdown inside Configuration tab . then time should be convert according to timezone

* #202 [fixed] - When we creating an activity, then the date icon should be shown in the schedule input box.

* #203 [fixed] - When we creating an activity, the schedule input box should not take date before today

* #204 [fixed] - When we creating activity and selecting date in 'To' input box which is less then 'From' date. Inside schedule label. then it should give an error. 

* #208 [fixed] - When we applying filters to search activities . then filters should be work properly to search activities



## **v1.0.1 (2nd of September 2021)**

* [feature] Activity participants


* #117 [fixed] - Implement Pest PHP For Testing

* #120 [fixed] - getting error when run migrate and seed database

* #122 [fixed] - missing translation for alert message when deleting types and sources

* #123 [fixed] - add more option is not working while edit person details

* #134 [fixed] - update alert when delete groups

* #133 [fixed] - exception when save new user roles

* #135 [fixed] - default placeholder should be selected in quotes billing address

* #136 [fixed] - buying leads stages are not assigned on mass action

* #137 [fixed] - show names instead of id for search term at activities section

* #139 [fixed] - filter is not working in quotes

* #147 [fixed] - Getting exception while creating product with same sku

* #148 [fixed] - Description field not visible while adding the products

* #152 [fixed] - Automatically curly bracket is getting added in description while editing Role



## **v1.0.0 (21th of July 2021)** - *First Release*

* [feature] Descriptive and Simple Admin Panel.

* [feature] Admin Dashboard.

* [feature] Custom Attributes.

* [feature] Built on Modular Approach.

* [feature] Email parsing via Sendgrid.


* #26 [fixed] - VAT number is missing for organization

* #28 [fixed] - Organization is not found while adding person to organization

* #29 [fixed] - Users to teams

* #31 [fixed] - Add Phone to source & type to the new lead form

* #32 [fixed] - Error on installation

* #33 [fixed] - update favicon

* #34 [fixed] - product is not listed even success alert is visible

* #35 [fixed] - Dashboard widgets are not draggable

* #38 [fixed] - Lead stages

* #39 [fixed] - unable to save the lead

* #40 [fixed] - Move icon is missing

* #41 [fixed] - unable to open the lead in kanban view

* #42 [fixed] - Lead without product

* #43 [fixed] - dashboard move icon is not working

* #54 [fixed] - Compose email is not working

* #55 [fixed] - Filter at kanban view does not work

* #56 [fixed] - Unable to back from single view to outbox or any email grid

* #57 [fixed] - wrong button name

* #60 [fixed] - date picker gets removed while selecting date on activity section

* #61 [fixed] - No validation on product SKU field

* #62 [fixed] - Getting exception when filter organization/products/admin roles

* #63 [fixed] - missing translation for compose mail

* #65 [fixed] - can't enter Email & Contact details for person

* #71 [fixed] - Menus are not visible

* #72 [fixed] - Alignment problem

* #79 [fixed] - Reset Password Mail is not sent and error is shown

* #81 [fixed] - Issue with Leads Filter in Layout

* #82 [fixed] - Exception issue in Organisation

* #83 [fixed] - Issue with Actions Alignment on Organisation page

* #84 [fixed] - Issue with user access

* #111 [fixed] - Fetch custom attribute types from config file