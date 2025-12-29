## **v2.1.6 (22 of Dec 2025)** - *Release*

* #2377[fixed] Sale owner dropdown now displays values correctly for individual users

* #2378[fixed] Pipeline stage name is now correctly reflected in activity.

* #2302[fixed] Dashboard date picker placeholder translation now works correctly on backspace.

* #2382[enhancement] Cross-Site Scripting (XSS) vulnerability fixed.

## **v2.1.5 (19 of Sep 2025)** - *Release*

* #2355[fixed] db_prefix support — installation and dashboard now function as expected

## **v2.1.4 (16 of Sep 2025)** - *Release*

* #2355[fixed] Improved database setup compatibility—now supports longer table prefixes during installation.

* #2328[fixed] Enhanced IMAP integration to ensure all incoming emails are reliably received.
  
## **v2.1.3 (02 of Sep 2025)** - *Release*

* #2236[fixed] Save button now responsive when linking email to lead during lead creation.  

* #2211[fixed] Contact number validation works correctly when a custom checkbox attribute (like Gender) is added for Person.  

* #2242[fixed] Translation string added in Attributes filter panel.  

* #2271[fixed] At least one pipeline must be marked as "Is Default" to ensure proper lead management.  

* #2269[fixed] Lookup attribute for Person entity works correctly with bulk-uploaded data via Data Transfer.  

* #2170[fixed] Organization name now updates correctly in Person record after save in Contact → Person.  

* #2245[fixed] Webhook sends parameter data to endpoint successfully.  

* #2246[fixed] Webhook triggers correctly when nested JSON data is entered in JSON field.  

* #2260[fixed] Duplicate pipeline names are no longer allowed.  

* #2244[fixed] Header key updated to "Content-Type" in Webhook configuration.  

* #2241[fixed] Non-admin users with dashboard access but without roles/users permission no longer get redirected to 401.  

* #2238[fixed] User creation shortcut opens the create user page directly.  

* #2234[fixed] Lead is created in the selected pipeline instead of always default pipeline.  

* #2247[closed] Attribute type change and "Is Unique" option for custom attributes behave as intended.  

* #1916[fixed] Overlay in Name and Organization fields on Person view page removed.  

* #2322[fixed] Lost reason now visible in the activity log when opportunity is marked as "Lost".  

* #2314[fixed] Currency displays consistently across Dashboard and throughout CRM.  

* #2293[fixed] Contact name input is fully visible in Workflow Action UI.  

* #2294[fixed] Salesperson name is fully visible in Workflow Action search dropdown.  

* #2301[fixed] `closed_at` date format displays correctly in Lead activity timeline.  

* #2300[fixed] Mega Search includes Configuration and Settings modules.  

* #2303[fixed] Lead stage change via Kanban drag & drop prompts for required Won/Lost fields.  

* #2304[fixed] Duplicate translation key for campaigns removed from Arabic language file (`ar/app.php`).  

* #2307[fixed] Event cannot be deleted if associated with a campaign where event is required.  

* #2312[fixed] Selected file is visible on Import Edit page.  

* #2278[fixed] Groups associated with users cannot be deleted.  

* #2291[fixed] Clear error message is shown when deleting a source.  

* #2290[fixed] Filters for Source and Lead Type work correctly in Lead list view.  

* #2295[fixed] In Leads, when marked as Won, `closed_at` saves the correct date.  

* #2230[fixed] Leads graph no longer displays duplicate yearly data when selecting multi-year date range.  

* #2282[fixed] Duplicate email template names are no longer allowed.  

* #2256[fixed] Lead is not created from Webform submission when "Create Lead" option is disabled.  

* #2283[fixed] Duplicate email template cannot be created via POST `/api/v1/settings/email-templates`.  

* #2243[fixed] Leads Board View search placeholder updated to "Search by Title".  

* #2146[fixed] "Filter by Tag" option added in Products filter panel.  

* #2153[fixed] Dashboard restricts future date ranges to avoid empty/no data.  

* #2158[fixed] Quote creation from lead negotiation stage redirects correctly within lead context.  

* #2168[fixed] "Expired Quote" column in Quotes table displays date without unnecessary time.  

* #2141[fixed] Group input field is no longer mandatory when creating a new user.  

* #2275[fixed] Users cannot view or assign non-group users as Sales Owner in Persons, Leads, Organizations, and Quotes.  
 
## **v2.1.2 (16 of May 2025)** - *Release*

* #2172[fixed] Cleaned up unnecessary label-value JSON from Email and Contact columns in export.

* #2171[fixed] Corrected the success message when adding a tag to a person in Contacts module.

* #2167[fixed] Aligned "Schedule From" and date inputs in the Workflow action UI. 

* #2155[fixed] Resolved validation for required custom attributes when creating a Person. 

* #1992[fixed] Removed duplicate "Stage" options when selecting "Lead" in workflow conditions.

## **v2.1.1 (15 of May 2025)** - *Release*

* Fixed broken password change functionality

* Implemented missing SVG sanitization to enhance security

* Added contact visibility control for individual accounts across different groups.

* Introduced dashboard pie chart features for better data visualization.

* #2195.[fixed]. Resolved migration failure on new setup.
  
* #2189.[fixed]. Addressed missing installation instructions in Readme.md.
  
* #2185.[fixed]. Corrected display of internal ACL labels on Role Edit/Create page.
  
* #2184.[fixed]. Eliminated error 500 when creating lead or contact.
  
* #2183.[fixed]. Repaired issue with email view rendering.
  
* #2181.[fixed]. Handled installation error in `krayin/rest-api`.
  
* #2180.[fixed]. Fixed database migration failure during installation.
  
* #2178.[fixed]. Enabled person creation for roles other than Administrator.
  
* #2173.[fixed]. Improved condition operator input visibility in workflow automation.

## **v2.1.0 (26th of March 2025)** - *Release*

* Mobile responsive pages.

* Ability to create lead via pdf or images.

* IMAP integration.

* Playwright support.

* Marketing feature for creating events and campaign.

## **v2.0.6 (20th of March 2025)** - *Release*

* Leads refinement.

* Qoutes refinement.

* #2065[fixed] Uncaught Error is appearing in Tags Section of Settings.

* #2064[fixed] Validations are not working on different sections.

* #2063[fixed] Uncaught Error in appearing in console in Leads section.

* #2061[fixed] Update Readme.

* #2060[fixed] Getting Exceptional error while editing Person and Saving it.

* #2022[fixed] Link to License in README.md is a dead link

* #2011[fixed] Integrity constraint violation: Update Persons

* #2000[fixed] Fix in #1961 not working

* #1985[fixed] Draft Mail Content from IMAP Account Not Appearing Properly as it is showing coded format.

* #1983[fixed] Unwanted Console Error When Clicking "Reply" or "Reply All" in Mail.

* #1982[fixed] Unwanted Console Error When Forwarding Mail with CC and BCC

* #1981[fixed] Prevent Duplicate Email Addresses in the Address Bar While Composing Mail

* #1977[fixed] Password Encryption Needed in IMAP Password Configuration.

* #1976[fixed] Console Errors in Mails Sent Mail Section.

* #1972[fixed] Emails Can Be Sent from Draft Without an Email Address.

* #1971[fixed] "From" Entry Missing in Sent Mails When Sending Emails from Krayin CRM .

* #1970[fixed] Save Draft button visibility issue in dark theme.

* #1969[fixed] Console Errors in Mails Draft Section.

* #1968[fixed] Inbox Filters: ID Filter Appears Without Corresponding Column in Datagrid.

## **v2.0.5 (17th of January 2025)** - *Release*

* #1953[fixed] Users get dead unauthorized page without dashboard permissions.

* #1938[fixed] Add Character Limit in Activity Title and Description.

* #1936[fixed] In Leads when we add tags and there when we add Long Tag Name without space causes UI issue. 

* #1935[fixed] Duplication of won and lost stages regarding pipeline on prospect detail view

* #1934[fixed] Was the ability to export leads/contacts removed in 2.0 ?

* #1917[fixed] UI Issue in Activity Description on Activities Page: Content Limit Needed.

* #1915[fixed] Character Validation for Warehouse and Quotes Address Fields

* #1914[fixed] Exceptional Error Displayed When Previewing Webform Without Admin Login 

* #1873[fixed] FIlters Drop down for Type and Entity Type is having other translation missing. 

* #1868[fixed] Workflow Webhook not work 

* #1853[fixed] Menu Caching causing menu use old role menu access, not real menu 

* #1850[fixed] Failing to access webform when not logged in

* #1848[fixed] In Webform when we click on Add Attribute Button, there we can see translation is missing.

* #1846[fixed] Placeholder Entries Not Visible in Webform Preview Page 

* #1840[fixed] UI Issue When Adding Long Entries in Pipeline Stage Name Section 

* #1838[fixed] In leads Filter we need to change the placeholder Text for Expected Delivery Date and Created Date.

* #1836[fixed] UI Issue in Email Input Section of Compose Mail Box

* #1835[fixed] Shimmer Effect Missing on Create Quote Form Page

* #1834[fixed] In Lead Won/Lost drop down arrow positiion is not working.

* #1833[fixed] Assigned Organization Not Displayed in New Lead When Person with Organization is Selected .

* #1831[fixed] Shimmer Effect Missing on Create Lead Form Page

* #1829[fixed] User Edit and Current Password Mandatory Field Validation Not Working.

* #1828[fixed] UI issue is appearing when we assign Inventory to the product in location.

* #1827[fixed] Delete Icon in the Location and QTY assigning to product is not aligned properly.

* #1825[fixed] Datagrid "Action" Heading Misaligned.

* #1824[fixed] Need to update the Text in Dashboard Open Leads By States. Change it to Open Leads By Stages.

* #1823[fixed] Shimmer effect in the WebForm Page is not appropriate.

* #1822[fixed] When viewing person profile and there when we remove 1 email and save it. All emails entry appears empty.

* #1821[fixed] When I am updating the Phone Number and Email drop-down to Work to Home or Home to Work, it is not functionaling properly.

* #1820[fixed] Change the text to Mega Search in Mega Search Bar

* #1819[fixed] In Mega Search Add button Email option, Clicking on it should directly open the create mail pop-up box.

* #1817[fixed] Sales Details Bar Graph Not Displaying Properly Due to UI Issue with Lost Leads Graph for todays sale.

* #1812[fixed] Adjust Font Size and Line Height Alignment in Leads Section Without Entries

* #1811[fixed] Add a Scrollbar for Long Warehouse Entries in Product Inventory Addition

* #1808[fixed] Remove Location Validation in Activities 

* #1807[fixed] Organization Name in Person View Page Should be in Black Color. 

* #1806[fixed] Update Button in Filters Not Visible in Dark Theme

* #1804[fixed] Creating a Quote Without a Product Leads to a "Not Found" Page

* #1803[fixed] Creating a Quote without Product leads to Not Found page.

* #1802[fixed] Enhance Saved Filter Entry UI and Make Cross Icon Visible.

* #1801[fixed] Add Back Button in Save/Update Filter Section 

* #1800[fixed] Add Proper Spacing and Padding in Calendar Filter

* #1799[fixed] Enable Auto-Save for Text Fields in Filters Without Requiring "Enter"

* #1798[fixed] Lead Name Not Displaying Properly in "Open Leads By States" Dashboard Widget in Dark Mode.

* #1797[fixed] Update the Format of Organization Address on Person View Page  

* #1796[fixed] Negative Inventory Appears in "On Hand" When Allocated Inventory Exceeds "In Stock"

* #1795[fixed] Deleting Email and Contact Number from Lead Also Removes Them from the Person

* #1794[fixed] Organization Details Removed from Person After Lead Creation Without Adding Organization in Lead 

* #1793[fixed] Spelling Mistake in URL for Calendar When Viewing Activities

* #1792[fixed] Activity Link Redirects to Lead Section Instead of Specific Lead.

* #1791[fixed] Add Proper Spacing Between the Comment and Date Columns in Activities

* #1790[fixed] Display Quote Subject, Person, and Custom Attributes in Quote Print.

* #1789[fixed] Custom Attributes Assigned to Quotes Are Not Visible

* #1788[fixed] Deleting a Source Also Deletes Related Leads .

* #1787[fixed] Total Lead Entry Not Displayed on Dashboard Bar Graph in Leads. 

* #1786[fixed] Inappropriate Validation Allows Negative Price in Product Creation.

* #1785[fixed] Proper Character Validation Needed for Name and Job Title in Person Section.

* #1784[fixed] Proper Character Validation Needed for Name, City, Address, and Zip Code in Organization 

* #1782[fixed] Text Type Attribute Validation is not working in leads section.

* #1781[fixed] Dashboard Pie Chart Not Displaying Colors for All Lead Details

* #1767[fixed] Date and Time Format in RTL view for User Settings is not appropriate.

* #1764[fixed] Remove the Mandatory Asterisk sign from the Make as Default in Pipeline.

* #1762[fixed] User Deleted When Assigned Custom Role is Removed. 

* #1761[fixed] Custom Role Created Without Selecting Options Causes Exception on User Login 

* #1760[fixed] Incorrect Text Displayed in Create Group Modal Box 

* #1758[fixed] Add Character Limit Validation for Tags Configuration

* #1755[fixed] GUI Installer: Admin Password Accepts Fewer Than Minimum Required Characters

* #1744[fixed] Deleted Options still appearing when we are check the select type attribute when editing it.

* #1742[fixed] Attribute Created File Type and assigned to product. PDF is not appearing on Product View Page.

* #1741[fixed] Attribute Created and assigned to product. Attribute is not appearing on Product View Page.

* #1732[fixed] Create a new attribute and assign it to Quotes, we are unable to see the assigned Attribute data.

* #1701[fixed] XSS vulnerability at Note function. 

* #1691[fixed] Mails details are not stored in Activities and changelog in ech section of Krayin.

* #1690[fixed] Compose Mail, Mail To Entry issue is appearing. This issue is appearing in almost all mail related section. 

* #1687[fixed] Need to add correct translation for the Sources text in Settings section.

* #1679[fixed] Workflow Mails are not working. Files are missing due to which mails are not working also Translations are missing.

* #1649[fixed] Drag and Drop Leads Card on Kanban error because fixing sortOrder of #1594

* #1614[fixed] When we create a webform without enable lead toggle button, there we can see attribute options are not appear. 

* #1611[fixed] When Priniting to pdf, cuurency Symbol gets printed with ? instead of the Symbol.

* #1609[fixed] Error displaying person contacts

* #1603[fixed] Bug: Page resets when entering the first character in "URL And Parameters" while creating a Webhook on mobile

* #1598[fixed] Translation Variables Displayed Instead of Text in Email Notifications

* #1595[fixed] REST API: ReflectionException "Class 'Webkul\Attribute\Http\Requests\AttributeForm' does not exist" in Krayin CRM v2.0.1 Bug

* #1961[fixed] Changing activity schedule from / to date not saving

* #1954[fixed] Decimal values must be saved in product Price.

## **v2.0.4 (19th of December 2024)** - *Release*

Added support for Brazilian Portuguese (PT_BR) Language.

* #1727[fixed] Fix: vulnerability issues.

* #1713[fixed] Refactor the datagrid.

* #1712[fixed] Fix the lead mass update action. 

* #1711[fixed] Fix tinymce dark mode more button ui.

* #1710[fixed] Fix sidebar border on rtl.

* #1708[fixed] Fix translation of arabic and publish build as missing assets.

* #1705[fixed] Update mass-update method 

* #1702[fixed] Fix users index page console error, ui and refactor code.

* #1687[fixed] Need to add correct translation for the Sources text in Settings section.

* #1690[fixed] Compose Mail, Mail To Entry issue is appearing. This issue is appearing in almost all mail related section. 

* #1687[fixed] Need to add correct translation for the Sources text in Settings section.

* #1686[fixed] remove dark mode from sessions pages. 

* #1676[fixed] Translation issue at webform. 

* #1683[fixed] Update installation link for docker.

* #1673[fixed] Update composer for development phase. 

* #1673[fixed] Add missing translation. 

* #1668[fixed] Activities datagrid total counts.

* #1663[fixed] Numerical Validation Filed is appearing Empty in Attribute Text Field Validation. 

* #1664[fixed] Fix format date and after validation.

* #1662[fixed] fix datagrid shimmers.

* #1660[fixed] In Attribute Filter, there we can enhance the filter in drop-down. 

* #1658[fixed] Entity Type must be clickable and should work and filter just like other options in Attributes Settings.

* #1655[fixed] Getting issue in the warehouse view when we add unwanted String in the Description. 

* #1653[fixed] fix div can not be child of p.

* #1650[fixed] fix lead kanban sort order issue. 

* #1643[fixed] Missing translation.

* #1642[fixed] Fix activities mass action destroy.

* #1640[fixed] Need to Correct translation on Configuration Locale Settings. 

* #1639[fixed] Fix Errors blade file.

* #1637[fixed] Datagrid checkbox.

* #1636[fixed] Quote pdf download error fix. 

* #1594[fixed] Stage Sorting in Pipeline not working - Krayin CRM Version. 

* #1594[fixed] Stage Sorting in Pipeline not working - Krayin CRM Version. 

## **v2.0.2 (24th of September 2024)** - *Release*

* #1633[enhancement] Use variable instead of calling every time of use db table prefix.

* #1621[enhancement] Person view page refactor.

* #1632[fixed] Update mail view render event.

* #1631[fixed] Dark Mode UI.

* #1629[fixed] Activities participants dark mode ui fixed.

* #1628[fixed] Refactor the quotes edit and create page.

* #1627[fixed] Leads actions ui.

* #1626[fixed] Mail View Page Refactor

* #1625[fixed] Sidebar rounded menu fix in rtl view.

* #1619[fixed] Persons view page avatar and organization edit button.

* #1617[fixed] When Printing to pdf, currency Symbol gets printed with ? instead of the Symbol.

* #1603[fixed] Bug: Page resets when entering the first character in "URL And Parameters" while creating a Webhook on mobile.

* #1597[fixed] Issues with Multiselect Attribute Type for Person Entity.

* #1590[fixed] File upload issue.

* #1589[fixed] Date time format.

* #1578[fixed] Create new user.

## **v2.0.1 (6th of September 2024)** - *Release*

* #1583[enhancement] Workflow create and edit page ui enhancement.

* #1580[enhancement] Donut chart, ui enhancement.

* #1570[enhancement] Kanban Leads add empty placeholder.

* #1575[enhancement] sidebar menu width.

* #1584[fixed] User is not being saved when status is not active.

* #1582[fixed] Remove unused links of font family.

* #1581[fixed] Product Inventories Migration have be updated.

* #1576[fixed] Database Table prefix does not working while installation from CLI and GUI.

* #1574[fixed] Inline component ui issues.

* #1573[fixed] Add missing mail inbound parse route.

* #1572[fixed] Fix modal ui issues.

* #1571[fixed] Fixed lookup component.

* #1560[fixed] Add description to leads view

## **v2.0.0 (30th of August 2024)** - *Release*

* #1492[feature] Mega Search.

* #1501[enhancement] Fixed vite helpers.

* #1484[enhancement] Adjustment done for all the attribute entities.

* #1374[enhancement] DataGrid searchable dropdown.

* #1515[enhancement] Activity deleting method added to activity trait's modal.

* #1516[enhancement] Add Support pdf for arabic language.

* #1525[enhancement] Update Activity Log Traits.

* #1523[enhancement] Add value-label props to inline component for showing custom input value.

* #1543[enhancement] Fix validation attributes with enums.

* #1540[enhancement] Inline allow editable or not.

* #1457[enhancement] Fixed Ui Issues.

* #1460[enhancement] Mail View Dark Mode.

* #1462[enhancement] Refactor Lead index page.

* #1466[enhancement] Fixed accordion border issue.

* #1552[enhancement] Automation Entity.

* #1467[enhancement] Fix stage for rtl remove publishable folder and old css file.

* #1557[enhancement] Fix script tag in webform embed.

* #1529[enhancement] Log activity refactor.

* #1558[fixed]  Fix DataGrid not refresh when performing the mass action.

* #1527[fixed] Create Webform, There click on Add Attribute Button we can see that the translation is missing.

* #1549[fixed] Fixed webform is not creating the leads and redirecting to the dashboard.

* #1450[fixed] After fresh Installation when we click on the link it is not redirected to Admin login page.

* #1520[fixed] Unable to add location in the warehouse when we have added location first time and again adding them.

* #1538[fixed] Person View Page.

* #1519[fixed] Getting warning issue when adding inventory in products.

* #1472[fixed] Create a new quote and there in Quote Items we can see UI issue in Quantity and Price Input Fields.

* #1535[fixed] Fix Leads stages won/lost date add validations to date component.

* #1545[fixed] Unable to see List of Leads in List mode in Other ACL User.

* #1541[fixed] Assigned a Lead Role to a user and gave permission for Create, Edit and View, Exceptional Error is appearing.

* #1485[fixed] UI issue in Action Buttons -> They must be centre aligned.

* #1404[fixed] Translation missing -> Trying to login an Inactive User.

* #1482[fixed] Updated Address details in Warehouse format is not appropriate.

* #1520[fixed] Unable to add location in the warehouse when we have added location first time and again adding them.

* #1528[fixed] Checked and Found Console Error on Dashboard.

* #1521[fixed] UI issue is appearing in product View add location section.

* #1518[fixed] Add View Render events.

* #1530[fixed] UI Issues.

* #1526[fixed] Fixed view actions and sticky the right bar.

* #1517[fixed] Fix Leads Stages.

* #1486[fixed] Inactive Users are also visible in Lead Create, Sales Owner Dropdown.

* #1502[fixed] Getting 500 Internal Server Error in Console and Exceptional Error in Dashboard Calender Same Day Filter applied.

* #1503[fixed] Getting Console Error and Blank Page when we edit the Lead and there when we click About Lead.

* #1506[fixed] Fix settings tags view create and update.

* #1481[fixed] The same details are being updated across multiple warehouse locations.

* #1477[fixed] After creating a note and attaching file in Product View Page, we can only see the updated details when we refresh the page.

* #1415[fixed] UI issue in adding Contact Person In Leads Section.

* #1403[fixed] Created a new admin user and set the status to Inactive. However, after saving, we can see the status as Active.

* #1458[fixed] While sending Mail, Draft button must be disable.

## **v2.0.0-BETA-1 (23rd of August 2024)** - *Release*

* New, attractive UI design.

* #1271[feature] Set both these accounts set as individual and yet they still see all the contacts even though they are also in different groups.

* #1048[enhancement] Dashboard Pie Chart

* #791[enhancement] Should have a responsive design on mobile

* #1198[fixed] Sent Mail details are appearing in Inbox.

* #1298[fixed] Toggle button is currently on the sidebar, in the menu section. This is not the perfect place for it .The toggle button is confusing right now

* #1403[fixed] Created a new admin user and set the status to Inactive. However, after saving, we can see the status as Active.

* #1405[fixed] Status Color is missing for Inactive.

* #1406[fixed] Blank Page and console error is appearing when we click on Tags Section in Settings.

* #1411[fixed] Unappropriated Warning Message is appearing when we send a mail In Leads.

* #1418[fixed] Mail Section Checkbox's are missing

* #1421[fixed] While sending Mail, Draft button must be disable.

* #1424[fixed] Unable to Figure out which view is selected in List View mode, as it is not highlighted.

* #1431[fixed] Arabic Local Entry is appearing twice in General Configuration.

* #1432[fixed] Create Pipeline -> Delete the stage -> Warning message not appearing.

* #1433[fixed] Unable to add Note and Files in Products. Getting issue Illegal operator and value combination.

* #1437[fixed] Getting Console Error when we Edit SKU and Inventory in Product View Page. Failed to load resource: the server responded with a status of 405 (Method Not Allowed)

* #1438[fixed] Favicon Icon for Krayin must be available.

* #1445[fixed] Getting Exceptional Error when we click on the Product Number in Warehouse DataGrid.

* #1450[fixed] After fresh installation when we click on the link it is not redirected to Admin login page.

* #1459[fixed] Getting Exceptional error when creating a Contact Person and then we fill the required field and then click save button we can see exceptional error.
