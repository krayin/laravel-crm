# Sidebar Collapse / Expand Toggle

**Date:** 2026-07-30
**Status:** Implemented
**Scope:** Admin desktop sidebar (`lg` and above)

## Problem

The admin sidebar expanded only on mouse hover. There was no way to keep it
open, the state reset on every navigation, and because the sidebar is `fixed`
with `z-[10002]` while the content wrapper had a fixed `ltr:lg:pl-[85px]`, the
expanded menu **overlaid** the page instead of making room for it.

## Outcome

Ported Bagisto's sidebar collapse implementation
(`bagisto/common/packages/Webkul/Admin/.../layouts/sidebar/index.blade.php`)
rather than inventing a parallel mechanism. Krayin and Bagisto share this admin
architecture, so the proven implementation transfers directly.

## Behaviour

| | |
|---|---|
| Default (no cookie) | **Expanded**, 200px, content padded 215px |
| Collapsed | 70px, content padded 85px |
| Toggle | Fixed strip at the bottom of the sidebar, chevron only |
| Persistence | `sidebar_collapsed` cookie, 1 month, read server-side in Blade |
| Hover-to-expand | **Removed** — see below |

### Hover-to-expand was removed

This is a deliberate change from the original design, which kept hover as a
"peek" alongside a pin toggle. That hybrid did not work, for a concrete reason:

Krayin's hover JS already used `sidebar-not-collapsed` as its *peeking* marker,
while Bagisto's model uses that same class to mean *expanded* and drives the
content padding from it. Running both meant two systems mutating the same
classes with different intent, and the sidebar behaved erratically.

Bagisto has no hover-expand. Removing it eliminates the conflict and matches the
reference implementation. If hover-peek is wanted later it needs its own class
name, distinct from `sidebar-collapsed` / `sidebar-not-collapsed`.

## Components

### 1. `bootstrap/app.php`

```php
$middleware->encryptCookies(except: [
    'dark_mode',
    'sidebar_collapsed',   // added
]);
```

Laravel encrypts cookies by default. The cookie is written by `document.cookie`,
so without this exemption `request()->cookie()` cannot decrypt it, returns
`null`, and the sidebar silently renders expanded on every navigation. Bagisto
carries the same exemption.

### 2. `components/layouts/index.blade.php`

Container, matching Bagisto exactly:

```blade
<div class="group/container {{ request()->cookie('sidebar_collapsed') ?? 0 ? 'sidebar-collapsed' : 'sidebar-not-collapsed' }} flex gap-4" ref="appLayout">
```

Content wrapper, using Bagisto's variant ordering (`lg:` first) with Krayin's
200px sidebar width:

```
lg:ltr:pl-[215px]
lg:group-[.sidebar-collapsed]/container:ltr:pl-[85px]
lg:rtl:pr-[215px]
lg:group-[.sidebar-collapsed]/container:rtl:pr-[85px]
```

All four rules compile inside `@media (min-width: 1024px)`, so no padding is
applied below `lg` where the mobile drawer takes over.

### 3. `components/layouts/sidebar/desktop/index.blade.php`

- `@mouseover` / `@mouseleave` removed from the sidebar root.
- `<v-sidebar-collapse>` added, with Bagisto's template and script.

The component is a `fixed bottom-0` strip whose width mirrors the sidebar
(`max-w-[200px]`, `max-w-[70px]` when collapsed). Krayin has no `icon-collapse`
in its icon font, so it uses `icon-left-arrow` with Bagisto's rotation bindings.

`toggle()` writes the cookie and flips both `sidebar-collapsed` and
`sidebar-not-collapsed` on `$root.$refs.appLayout`.

### 4. `assets/js/app.js`

- `handleMouseOver` / `handleMouseLeave` deleted — the sidebar width is now
  owned solely by the toggle.
- `handleFocusOut` reduced to closing an open submenu flyout. It no longer
  touches `sidebar-collapsed`, so it cannot fight the toggle.

### 5. Translations

`layouts.sidebar.collapse` / `.expand` in all eight locales (`ar`, `en`, `es`,
`fa`, `ko`, `pt_BR`, `tr`, `vi`), used for the toggle's `title` tooltip.

## Verification

Server-side, per cookie state:

| Cookie | Container class | Vue `isCollapsed` |
|---|---|---|
| absent | `sidebar-not-collapsed` | 0 |
| `0` | `sidebar-not-collapsed` | 0 |
| `1` | `sidebar-collapsed` | 1 |

Confirmed on all eight admin sections (dashboard, leads, quotes, persons,
products, activities, mail, configuration): toggle present, correct container
class, both padding variants emitted, no template source leaking into the HTML.

## Manual testing still required

Browser checks that cannot be made from the shell:

1. Chevron direction reads correctly in each state, LTR and RTL.
2. Content reflows smoothly when toggling, with no overlap.
3. State survives navigation with no flicker on first paint.
4. Below `lg`, the strip is hidden and the mobile drawer is unaffected.
5. Submenu flyouts still open and close correctly now that hover no longer
   changes the sidebar width.
