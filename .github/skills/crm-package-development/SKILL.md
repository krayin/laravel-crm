---
name: crm-package-development
description: Use when creating a new Krayin CRM package or module, extending CRM functionality via a package, or adding custom business logic without modifying core files. Also use for any translation work — adding or moving lang files, adding a locale, or deciding whether a package gets its own Resources/lang directory — and whenever a fix or feature needs a CHANGELOG.md entry. Covers Laravel package structure, service providers, migrations, models, repositories, routes, controllers, views, localization, changelog entries, config, ACL, and menus.
---

# Skill: CRM Package Development (Krayin CRM)

## Purpose
This skill guides the AI agent to design and develop a new CRM package
(module) for Krayin CRM in a clean, upgrade-safe, and maintainable way.

---

## When to Activate
Activate this skill when the user wants to:
- Create a new CRM package/module
- Extend CRM functionality via a package
- Add custom business logic without modifying core files
- Add, move, or delete translation (lang) files, or add a new locale
- Fix a bug or ship a feature — the change needs a `CHANGELOG.md` entry

---

## Project Context
- Framework: Laravel
- Product: Krayin CRM
- Krayin CRM is already installed and running
- The package must integrate seamlessly with existing CRM modules

---

## Development Rules
- Follow Krayin CRM architecture and conventions
- Do NOT modify core Krayin files unless explicitly required
- Use Laravel package-based structure
- All database changes must be done using migrations
- Ensure backward compatibility and safe upgrades

---

## Package Structure Guidelines

A CRM package should follow this structure:

```text
packages/
└── Webkul/
    └── PackageName/
        ├── src/
        │   ├── Providers/
        │   │   └── PackageServiceProvider.php
        │   ├── Models/
        │   ├── Contracts/
        │   ├── Repositories/
        │   ├── Http/
        │   │   ├── Controllers/
        │   │   └── Requests/
        │   ├── Routes/
        │   │   ├── admin.php
        │   │   └── api.php
        │   ├── Database/
        │   │   └── Migrations/
        │   ├── Resources/
        │   │   ├── views/          # only if the package renders its own Blade
        │   │   └── lang/           # ONLY if views/ exists — see Localization
        │   └── Config/
        │       ├── package.php
        │       ├── menu.php
        │       ├── core_config.php
        │       └── acl.php
        └── composer.json
```

---

## Localization (Translations)

### The rule

A package owns a `Resources/lang/` directory **only if it also owns
`Resources/views/`** — that is, only if it renders its own Blade templates.
Every other package ships **zero** translation files; its strings live in the
**Admin** package under `admin::app.*`.

Before creating any lang file, ask:

> Does this package have `src/Resources/views/` with Blade templates?

- **Yes** → give it `Resources/lang/<locale>/app.php` and register its own
  namespace with `loadTranslationsFrom()` in the service provider.
- **No** → do **not** create a lang directory and do **not** call
  `loadTranslationsFrom()`. Put the keys in the Admin package and reference them
  as `admin::app.…`.

Current state of the repo — preserve this invariant:

| Owns views | Packages | Translations live in |
|---|---|---|
| Yes | `Admin`, `Installer`, `WebForm` | the package itself (`admin::`, `installer::`, `web_form::`) |
| No | everything else (`Core`, `DataTransfer`, `Lead`, `Contact`, `Quote`, `User`, …) | `Admin` (`admin::app.*`) |

"Backend-only" strings are still translations and follow the same rule —
validation-rule messages, importer error maps, and `title` values referenced
from `Config/*.php` all belong in Admin when the package has no views.

### Where keys go for a view-less package

Nest them under the Admin key that already owns that domain so one feature's
strings stay together. Established precedent:

| Before | After |
|---|---|
| `core::app.validations.code` | `admin::app.validations.message.code` |
| `data_transfer::app.importers.persons.title` | `admin::app.settings.data-transfer.importers.persons.title` |
| `data_transfer::app.validation.errors.system` | `admin::app.settings.data-transfer.validation.errors.system` |

When you move keys out of a package, finish the job: rewrite every reference,
delete the package's `Resources/lang/`, and remove its `loadTranslationsFrom()`
call so the provider does not point at a path that no longer exists.

### Working with Admin lang files

- Admin ships one `app.php` per locale: `ar`, `en`, `es`, `fa`, `ko`, `pt_BR`,
  `tr`, `vi`, `zh_CN`. **Add every new key to all of them in the same change** —
  a key present only in `en` renders as the raw key string in other locales.
- If a real translation is unavailable, use the English text as the value. Never
  omit the key, and never leave the key name as its own value.
- Preserve placeholders exactly: `:attribute`, `:days`, `:count` (Laravel) and
  `%s` (importer messages formatted with `sprintf`).
- Pint enforces a **single space** around `=>` (`pint.json` →
  `binary_operator_spaces`). Do not align `=>` into columns.
- Name the file `app.php` in every locale directory. A misnamed file (e.g.
  `ar/ar.php`) is never loaded and the locale silently falls back to English.

### Adding a new locale

1. Create `<locale>/app.php` in each package that owns a lang directory
   (`Admin`, `Installer`, `WebForm`), mirroring the `en` key structure exactly.
2. Register it in `config/app.php` → `available_locales`. This drives both the
   admin locale dropdown (via `Core@locales`) and the web installer.
3. Add it to `$locales` in
   `packages/Webkul/Installer/src/Console/Commands/Installer.php` so the CLI
   installer offers it.
4. If the script is right-to-left, add it to the `['fa', 'ar']` checks in the
   Admin layout Blade files.

### Verifying

Confirm parity with `en` — same key set, same placeholders — then check that
keys actually resolve:

```bash
php artisan tinker --execute='app()->setLocale("<locale>"); echo trans("admin::app.<your.key>");'
./vendor/bin/pint --test packages/Webkul/<Package>
```

A key that resolves to its own name (`admin::app.foo.bar`) is missing.

---

## Changelog

### The rule

**Every bug fix and every feature gets a `CHANGELOG.md` entry, in the same
change.** Do not leave it for someone else and do not treat it as optional
follow-up work — a fix that is not in the changelog is invisible to the release
notes.

Add an entry when the change is user-visible or operator-visible: a bug fix, a
new feature, an enhancement, a security fix, a configuration option, or a
convention that affects how the product is built. Skip it only for changes with
no observable effect at all — a typo in a code comment, a pure test refactor.

### Format

Entries live at the top of [CHANGELOG.md](../../../CHANGELOG.md), newest version
first, and use exactly this shape:

```markdown
## **v2.2.5 (Upcoming)**

* #2601[fixed] Fixed lead value not saved when the currency is changed.

* #2604[feature] Added Chinese (Simplified) translation.
```

- One blank line between every entry — the file is read as rendered markdown.
- No space between the issue number and the tag: `#2601[fixed]`, not `#2601 [fixed]`.
- Write the description as a complete sentence ending in a period, phrased from
  the user's point of view, not the implementation's.

Valid tags, and when each applies:

| Tag | Use for |
|---|---|
| `[fixed]` | Something was broken and now works. |
| `[feature]` | New capability that did not exist before. |
| `[enhancement]` | Existing behaviour improved, refactored, or made faster. |
| `[security]` | Vulnerability fix. Reference the CVE when one exists. |

### Which version section

Add to the topmost section if it is still unreleased. If the top section is
already tagged `*Release*`, open a new one above it:

```markdown
## **v<next patch version> (Upcoming)**
```

Replace `(Upcoming)` with the real date in the release commit — the existing
released headings show the expected style: `## **v2.2.4 (20th of July 2026)** *Release*`.

### Two things not to do

- **Never invent an issue or PR number.** They point at real GitHub PRs, so a
  guessed number creates a false reference. If the PR does not exist yet, omit
  the `#NNNN` prefix and leave the tag, then fill the number in once it is
  opened.
- **Do not bump the version constant as part of a feature or fix.**
  `KRAYIN_VERSION` in `packages/Webkul/Core/src/Core.php` is incremented in the
  release commit, together with dating the changelog heading — not per change.
