#!/usr/bin/env bash
set -euo pipefail

canonical_dir=".github/skills"

echo "[skills] validating canonical directory"
if [[ ! -d "$canonical_dir" ]]; then
    echo "ERROR: $canonical_dir does not exist"
    exit 1
fi

skill_count=$(find "$canonical_dir" -mindepth 2 -maxdepth 2 -type f -name SKILL.md | wc -l | tr -d ' ')
if [[ "$skill_count" -eq 0 ]]; then
    echo "ERROR: no SKILL.md files found under $canonical_dir"
    exit 1
fi

echo "[skills] validating agent link targets"
while IFS='|' read -r link_path expected_target; do
    [[ -z "$link_path" ]] && continue

    if [[ ! -L "$link_path" ]]; then
        echo "ERROR: $link_path must be a symlink to $expected_target"
        exit 1
    fi

    actual_target=$(readlink "$link_path")
    if [[ "$actual_target" != "$expected_target" ]]; then
        echo "ERROR: $link_path points to $actual_target (expected $expected_target)"
        exit 1
    fi
done <<'LINKS'
.ai/skills|../.github/skills
.claude/skills|../.github/skills
.codex/skills|../.github/skills
.cursor/skills|../.github/skills
.kilocode/skills|../.github/skills
LINKS

echo "[skills] checking for SKILL.md outside canonical source"
mapfile -t outside_skills < <(
    find . \
        -path './.git' -prune -o \
        -path './vendor' -prune -o \
        -path './node_modules' -prune -o \
        -type f -name SKILL.md -print \
    | grep -v '^./.github/skills/' || true
)

if (( ${#outside_skills[@]} > 0 )); then
    echo "ERROR: found SKILL.md files outside $canonical_dir"
    printf ' - %s\n' "${outside_skills[@]}"
    exit 1
fi

echo "[skills] validating frontmatter keys and name/directory match"
while IFS= read -r skill_file; do
    if ! grep -qE '^name:' "$skill_file"; then
        echo "ERROR: missing name in $skill_file"
        exit 1
    fi

    if ! grep -qE '^description:' "$skill_file"; then
        echo "ERROR: missing description in $skill_file"
        exit 1
    fi

    dir_name=$(basename "$(dirname "$skill_file")")
    name_value=$(grep -E '^name:' "$skill_file" | head -1 | sed -E 's/^name:[[:space:]]*//; s/[[:space:]]*$//')
    if [[ "$name_value" != "$dir_name" ]]; then
        echo "ERROR: name '$name_value' must match directory '$dir_name' in $skill_file"
        exit 1
    fi
done < <(find "$canonical_dir" -mindepth 2 -maxdepth 2 -type f -name SKILL.md | sort)

echo "[skills] validation passed ($skill_count skills)"
