# Krayin CRM: Agent Instructions

This repository ships reusable **agent skills** for developing Krayin CRM. These
instructions apply to all AI coding agents (Claude Code, Codex, Copilot, Cursor,
Kilo Code, etc.).

---

## Framework Context

- **Product:** Krayin CRM (Webkul) — an open-source Laravel CRM.
- **Stack:** Laravel `^12.0`, PHP `^8.3`, Pest `^3.0`, Laravel Pint `^1.18`.
- **Follow Krayin's own conventions** — use the patterns already present in this
  repository's modules; do not import patterns from other Laravel products.
- **Module location:** `packages/Webkul/{ModuleName}/src/`
- **Core modules (reference implementations):** `Lead`, `Contact`, `Activity`,
  `Quote`, `Product`, `User`, `Attribute`, `Core`, `Admin`, `DataGrid`.

---

## Skills

Workspace skills live in `.github/skills/` (canonical source) and are symlinked
into every agent runtime (`.ai`, `.claude`, `.codex`, `.cursor`, `.kilocode`).
See [.github/skills/README.md](.github/skills/README.md) for the standard.

| Skill | Use when |
|-------|----------|
| `crm-package-development` | Creating a new CRM package/module or extending CRM functionality without touching core. |
| `pest-testing` | Writing or debugging unit/feature tests with Pest. |

Read the relevant `SKILL.md` before starting work in its domain. Do not restate
skill content here — keep details in the skills.

---

## Critical Conventions (Never Deviate)

- **Never modify core Krayin files** unless explicitly required. Extend behavior
  through a package under `packages/Webkul/`.
- **All schema changes go through migrations** — never edit the database directly.
- **Follow the existing module layout** (Providers, Models, Contracts,
  Repositories, Http/Controllers, Routes, Database/Migrations, Resources/views,
  Config). The `crm-package-development` skill documents the full structure.
- **Repository pattern:** models are accessed through repositories bound via
  contracts in the service provider — match the surrounding modules.
- **Preserve backward compatibility** so the CRM stays upgrade-safe.

---

## Development Cycle

```bash
composer install            # install PHP dependencies
php artisan migrate          # run migrations
php artisan test --compact   # run the Pest test suite
./vendor/bin/pint            # format code (PSR-12 via Laravel Pint)
```

- Add tests for new behavior; follow the `pest-testing` skill.
- Run `./vendor/bin/pint` before committing — CI applies Pint formatting.
- Validate the skills setup with `bash bin/validate-skills.sh`.
