# Skills Standard

## Canonical Source

All workspace skills are maintained in:

- `.github/skills/<skill-name>/SKILL.md`

This is the single source of truth for every AI coding agent in this repository.

## Agent Runtime Links

The following paths are **symlinks** to `.github/skills` so each agent runtime
auto-discovers the same skills:

| Path | Agent |
|------|-------|
| `.ai/skills` | Generic / `.ai` convention |
| `.claude/skills` | Claude Code |
| `.codex/skills` | OpenAI Codex |
| `.cursor/skills` | Cursor |
| `.kilocode/skills` | Kilo Code |

> Symlinks are committed to git (mode `120000`). On a Windows checkout without
> symlink support they may materialize as plain files — clone with symlink
> support enabled (`git config core.symlinks true`).

## Available Skills

| Skill | Use when |
|-------|----------|
| `crm-package-development` | Creating a new Krayin CRM package/module or extending CRM functionality without touching core files. |
| `pest-testing` | Writing or debugging Krayin CRM unit/feature tests with Pest. |

## Authoring Rules

- Add or update skills **only** in `.github/skills` (never edit through a symlink target name as if it were a separate copy).
- Keep `name` and `description` in the YAML frontmatter of every `SKILL.md`.
- `name` must be lowercase, hyphen-separated, and **match the skill's directory name**.
- Use explicit "Use when..." trigger language in descriptions so agents match them reliably.
- Do not maintain duplicate `SKILL.md` files in agent-specific folders.

## Validation

Validate frontmatter and naming with the Agent Skills reference tool:

```bash
skills-ref validate .github/skills/<skill-name>
```

See the [Agent Skills specification](https://agentskills.io/specification) for the full format.
