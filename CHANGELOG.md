# CHANGELOG for 2.2

This changelog consists of the bug & security fixes and new features being included in the releases listed below.

## **v2.2.6 (19th of Aug 2026)**

* [security] Updated Composer dependencies to clear all 40 outstanding security advisories across 13 packages, with no constraint changes — Laravel stays on 12.x and Symfony on 7.4.x. Notable fixes: `symfony/mime` 7.4.16 (CRLF in `Address` allowing email header and SMTP command injection), `laravel/framework` 12.66.0 (CRLF injection in the default `email` validation rule, and temporary signed URL path confusion), `league/commonmark` 2.10.0 (quadratic-time parsing, duplicate footnote definitions and three further denial-of-service paths, plus an unsafe-link filter bypass), `guzzlehttp/guzzle` 7.15.3 and `guzzlehttp/psr7` 2.13.0 (host-check bypasses, cookie scope and CRLF issues), `dompdf/dompdf` 3.1.6 (local file read and resource exhaustion) and `setasign/fpdi` 2.6.8.

* [security] Upgraded Vite to 6.4.3 across the root, Admin, Installer and WebForm packages, along with `laravel-vite-plugin` (0.7 → 1.3) and `@vitejs/plugin-vue` (4 → 5) where those packages still required them. Vite 4.x and 5.x are affected by CVE-2026-53571, where `server.fs.deny` could be bypassed on Windows through NTFS alternate data streams (`/.env::$DATA?raw`) and 8.3 short names, exposing `.env` and certificate files; no fix exists on the 4.x or 5.x lines, so a major upgrade was the only route. This affects the development server only — never a built deployment — and requires the server to be exposed to the network, which `VITE_HOST` makes possible.

* [security] The same Vite upgrade also picks up a patched `launch-editor`, which Vite bundles into its distribution rather than installing as a resolvable dependency (CVE-2024-52011, arbitrary command execution on Windows via crafted filenames).

* [security] Updated DOMPurify to 3.4.13, fixing an XSS where removing an `IN_PLACE` hook left a detached subtree executable, and nanoid to 3.3.18.

* [security] Updated `phpoffice/phpspreadsheet` to 1.30.6 and added an explicit `^1.30.6` floor. 1.30.4 shipped a `prohibitWrappers` guard built on `parse_url()`, which returns `false` rather than a scheme for a `phar:///a/b.phar/c` path and so let the phar stream wrapper through (CVE-2026-34084). Krayin was not exploitable — import paths are resolved through `Storage::disk('public')->path()`, which puts the disk root at position 0 so no wrapper scheme is ever honoured — but the dependency is now patched regardless.

* [security] Fixed the SVG sanitizer being skipped when an SVG was uploaded under a non-SVG file name. The TinyMCE media upload derives the stored extension from the file's own content, so an SVG named `payload.png` was stored and served as an SVG while the name-based check waved it past the sanitizer. Detection now also consults the stored path and the markup itself.

* [security] Fixed nested configuration fields storing uploaded files without SVG sanitization, unlike the flat configuration fields alongside them.

* [fixed] Fixed a configuration save reusing a previously uploaded file's path as the value of a later field that had no upload of its own.

* [security] Fixed the installer API endpoints (`/install/api/admin-config-setup`, `/install/api/run-migration`, `/install/api/run-seeder`) becoming reachable again on a fully installed application whenever the `storage/installed` marker was missing. That file is gitignored, so a fresh checkout, a container rebuild or any deploy not carrying `storage/` across left the endpoints open to unauthenticated callers, who could overwrite the administrator account and re-run `migrate:fresh` against live data. Installation completion is now also recorded in the database, where it travels with the data it describes.

* [security] Added rate limiting to the admin login, forgot-password and reset-password endpoints, which previously accepted unlimited attempts and allowed offline-speed credential brute-forcing.

* [security] Fixed the admin logout leaving the session intact — the session is now invalidated and its CSRF token rotated, so no session data survives a logout and the session id cannot be replayed.

* [security] Fixed the forgot-password form disclosing which email addresses belong to registered users; it now returns the same response whether or not the address exists.

* [security] Fixed a deactivated user being signed in automatically after completing a password reset, bypassing the account-status check that login enforces.

## **v2.2.5 (4th of Aug 2026)**

* [feature] Added Chinese (Simplified) `zh_CN` translation for the Admin, Installer, DataTransfer, WebForm and Core packages.

* [feature] Added a configurable default dashboard date range — 1 month, 3 months, 9 months, 1 year, 2 years or a custom number of days — under Configuration > General > Settings > Dashboard Configurations.

* [fixed] Fixed menu item names set in Configuration not applying to section pages, breadcrumbs and the mobile sidebar. Previously only the desktop sidebar reflected a rename.

* [fixed] Fixed renaming the "Mail" and "Contacts" menu items having no effect anywhere, as their configuration fields did not match the actual menu keys.

* [fixed] Fixed the dashboard date range label omitting the year on ranges spanning more than one calendar year, which rendered as "30 Jul - 30 Jul".

* [fixed] Fixed Arabic DataTransfer translations never loading, as the file was named `ar/ar.php` instead of `ar/app.php`.

* [fixed] Fixed the missing Korean translation for the "None" input validation option on the create and edit attribute forms.

* [enhancement] Moved the Core and DataTransfer package translations into the Admin package. Only packages that ship their own Blade views now carry a `Resources/lang` directory.

* [enhancement] Reduced database queries on every admin page by loading the configured menu names in a single query instead of one per menu item.

* [enhancement] Documented the localization convention in the `crm-package-development` agent skill and in AGENTS.md.
* [feature] Added a collapse/expand toggle to the admin sidebar, matching the Bagisto admin. The choice is remembered across page loads, and page content now reflows to the sidebar width instead of being overlaid by it. The sidebar no longer expands on hover; it is controlled by the toggle only.
* #2614[security] Fixed unauthenticated installer access and executable email attachment upload vulnerabilities.

* #2612[feature] Added import and export support for custom attributes for Leads and Persons.

* #2609[feature] Added Google Contacts export for Persons with Google account connection, duplicate detection, queued export progress, and result summary.

* #2608[fixed] Added missing "none" key to the Korean locale for attribute validation.

* #2606[feature] Added a collapse/expand toggle to the admin sidebar, matching the Bagisto admin. The choice is remembered across page loads, and page content now reflows to the sidebar width instead of being overlaid by it. The sidebar no longer expands on hover; it is controlled by the toggle only.

* #2606[feature] Added an option to show or hide the "Powered by" bar under Configuration > General > Settings > Powered by Section Configurations.

* #2603[feature] Added Korean translations for the Installer, DataTransfer, WebForm, and Core packages.

* #2602[feature] Added Korean translation support for the Admin package.

* #2600[fixed] Fixed invalid activity calendar .ics date-times by emitting UTC RFC 5545 values.

* #2592[security] Fixed webhook validation to reject internal endpoint URLs.

* #2580[enhancement] Added a "None" option to input validation for text attributes.

## **v2.2.4 (20th of July 2026)** *Release*

* #2590[fixed] Fixed page does not refresh after creating a record via Quick Add.

* #2589[fixed] Fixed Quick Add not working for users with group and individual permissions.

* #2582[fixed] Fixed pipeline field visible on public webform.

* #2581[enhancement] Fixed responsive UI issues when page is zoomed.

* #2579[feature] Allow group selection for individual view permission users.

* #2575[enhancement] Added previous month's sales update in Kanban view.

* #2573[enhancement] Added dashboard support for multiple pipelines.

* #2572[enhancement] Added filter by tag option in Contacts > Persons.

* #2571[fixed] Fixed issue with lead creation.

* #2570[fixed] Fixed auto-fill lead email issue.

* #2567[fixed] Fixed IDOR agent record access control vulnerability.

* #2563[fixed] Fixed Kanban infinite scroll duplicates issue.

* #2583[fixed] Fixed SQL injection in rotten lead filter.

* #2585[security] Fixed unrestricted file upload vulnerability (CVE-2026-38526).

* #2559[fixed] Fixed agent record access control issue.

* #2556[fixed] Fixed installation config save issue.

* #2550[fixed] Fixed Kanban infinite scroll duplicates.

* #2549[enhancement] Added support tab feature.

* #2548[enhancement] Allow search by phone and email when creating a lead.

* #2546[feature] Quick Attribute now available at lead form.

* #2545[feature] Added agent skills functionality.

* #2544[enhancement] Added validate skills.

* #2543[enhancement] Added Agents Skills folder.

* #2542[fixed] Fixed stored XSS vulnerability in notes field.

* #2541[fixed] Fixed quote description truncation issue.

* #2539[fixed] Fixed lost revenue arrow UI issue.

* #2538[fixed] Fixed missing translations.

* #2501[fixed] Fixed sales owner not saved in organization.

* #2500[fixed] Fixed activities date filter range issue.

* #2479[fixed] Fixed textarea field not rendered in WebForm.

* #2471[fixed] Fixed missing translations for lead won/lost modal.

* #2420[fixed] Added missing mega search translations for settings and configurations.

* #2533[fixed] Fixed GUI installation issue.

* #2419[security] Fixed stored XSS vulnerability in notes field.

* #2454[fixed] Fixed quote description truncation.

* #2407[fixed] Fixed missing translations.

* #2157[fixed] Fixed auto-fill lead email when creating a lead.

* #2258[fixed] Fixed issue with same-as-billing-address field.

## **v2.2.3 (1st of May 2026)** *Release*

* [fixed] Pipline critical issue resolved.

## **v2.2.2 (1st of May 2026)** *Release*

* [fixed] Update Change Log and version.

## **v2.2.1 (1st of May 2026)** *Release*

* [fixed] Quote fields now auto-fill correctly when a quote is linked to a lead.

* [fixed] Fixed price formatting issue.

* [fixed] Fixed Lead Kanban list ordering.

* [fixed] Fixed header block position at the top.

* [fixed] Updated Activity UI.

* [fixed] Admins can now view and share quote details to a person from the quote list.

* [fixed] Fixed submission issue on the web form.

* [fixed] Fixed activity display issue in the Calendar view.

* [fixed] Logo update issue resolved.

* [enhancement] Drag-and-drop support added to Activity. Admins can now change date and time directly from the Calendar view.

* [feature] Quick App feature added for faster access to key CRM actions.

* [feature] Admins can now add or update a person directly from the lead view page.

* [security] Resolved an authentication bypass vulnerability caused by improper access control in the installer.

## **v2.2.0 (17th of March 2026)** *Release*

* **[Laravel 12 Upgrade]** Upgraded framework to Laravel 12

* #2480[enhancement] Codebase updates and refinements.

* #2478[enhancement] Improved class instantiation handling.

* #2472[enhancement] Upgrade to Laravel 12.

* #2470[enhancement] Updated auto_commits.yml configuration.

* #2469[enhancement] General enhancements and optimizations.

* #2468[enhancement] Documentation updates (MD files).

* #2450[fixed] Added ACL support for warehouses.

* #2444[fixed] Improved global search functionality for organizations.
