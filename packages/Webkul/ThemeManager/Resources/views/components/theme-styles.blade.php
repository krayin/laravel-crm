<style>
    {!! app('theme')->getCssVariables() !!}

    /* =================================
       BUTTONS - PRIMARY BRAND COLOR
       ================================= */

    /* Primary buttons */
    .primary-button,
    .btn-primary,
    button[type="submit"]:not(.secondary-button),
    .button.primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .primary-button:hover,
    .btn-primary:hover,
    button[type="submit"]:not(.secondary-button):hover,
    .button.primary:hover {
        background-color: var(--primary-dark-color) !important;
        border-color: var(--primary-dark-color) !important;
    }

    .primary-button:focus,
    .btn-primary:focus,
    button[type="submit"]:not(.secondary-button):focus,
    .button.primary:focus {
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.25) !important;
    }

    /* Primary outlined buttons */
    .primary-button.outlined,
    .btn-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        background-color: transparent !important;
    }

    .primary-button.outlined:hover,
    .btn-outline-primary:hover {
        color: white !important;
        background-color: var(--primary-color) !important;
    }

    /* =================================
       LINKS
       ================================= */

    a:not(.button):not(.btn),
    .text-primary {
        color: var(--primary-color) !important;
    }

    a:not(.button):not(.btn):hover {
        color: var(--primary-dark-color) !important;
    }

    /* =================================
       FORMS
       ================================= */

    /* Input focus */
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="number"]:focus,
    input[type="tel"]:focus,
    input[type="url"]:focus,
    textarea:focus,
    select:focus {
        border-color: var(--primary-light-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(var(--primary-rgb), 0.1) !important;
    }

    /* Checkbox and radio checked state */
    input[type="checkbox"]:checked,
    input[type="radio"]:checked {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    /* Toggle switch */
    .toggle-switch input:checked + .slider {
        background-color: var(--primary-color) !important;
    }

    /* =================================
       NAVIGATION
       ================================= */

    /* Active menu items */
    .sidebar .menu-item.active,
    .sidebar .menu-item:hover,
    .nav-tabs .nav-link.active {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    /* Active tab underline */
    .nav-tabs .nav-link.active {
        border-bottom-color: var(--primary-color) !important;
    }

    /* Sidebar selected item */
    .sidebar .selected,
    .sidebar .active > a {
        background-color: var(--primary-light-color) !important;
        border-left-color: var(--primary-color) !important;
    }

    /* =================================
       BADGES & PILLS
       ================================= */

    .badge-primary,
    .pill-primary {
        background-color: var(--primary-color) !important;
    }

    .badge-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    /* =================================
       ALERTS & MESSAGES
       ================================= */

    /* Success */
    .alert-success,
    .badge-success,
    .text-success,
    .bg-success {
        background-color: var(--success-color) !important;
        border-color: var(--success-color) !important;
    }

    .alert-success {
        background-color: rgba(var(--success-rgb), 0.1) !important;
        color: var(--success-color) !important;
        border-color: rgba(var(--success-rgb), 0.3) !important;
    }

    /* Warning */
    .alert-warning,
    .badge-warning,
    .text-warning,
    .bg-warning {
        background-color: var(--warning-color) !important;
        border-color: var(--warning-color) !important;
    }

    .alert-warning {
        background-color: rgba(var(--warning-rgb), 0.1) !important;
        color: var(--warning-color) !important;
        border-color: rgba(var(--warning-rgb), 0.3) !important;
    }

    /* Danger */
    .alert-danger,
    .badge-danger,
    .text-danger,
    .bg-danger {
        background-color: var(--danger-color) !important;
        border-color: var(--danger-color) !important;
    }

    .alert-danger {
        background-color: rgba(var(--danger-rgb), 0.1) !important;
        color: var(--danger-color) !important;
        border-color: rgba(var(--danger-rgb), 0.3) !important;
    }

    /* =================================
       PROGRESS & LOADERS
       ================================= */

    .progress-bar {
        background-color: var(--primary-color) !important;
    }

    .spinner-border,
    .loader {
        border-color: var(--primary-light-color) !important;
        border-top-color: var(--primary-color) !important;
    }

    /* =================================
       TABLES
       ================================= */

    /* Table header */
    .table thead th {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    /* Table row hover */
    .table tbody tr:hover {
        background-color: rgba(var(--primary-rgb), 0.05) !important;
    }

    /* Table striped */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(var(--primary-rgb), 0.03) !important;
    }

    /* =================================
       PAGINATION
       ================================= */

    .pagination .page-item.active .page-link {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .pagination .page-link {
        color: var(--primary-color) !important;
    }

    .pagination .page-link:hover {
        background-color: var(--primary-light-color) !important;
        border-color: var(--primary-light-color) !important;
    }

    /* =================================
       DROPDOWNS
       ================================= */

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    .dropdown-item.active {
        background-color: var(--primary-color) !important;
    }

    /* =================================
       MODALS
       ================================= */

    .modal-header {
        background-color: var(--primary-light-color) !important;
        color: var(--primary-color) !important;
    }

    /* =================================
       CARDS
       ================================= */

    .card-header {
        background-color: var(--primary-light-color) !important;
        border-bottom-color: var(--primary-color) !important;
    }

    /* =================================
       TOOLTIPS & POPOVERS
       ================================= */

    .tooltip-inner {
        background-color: var(--primary-dark-color) !important;
    }

    .tooltip.bs-tooltip-top .arrow::before,
    .tooltip.bs-tooltip-auto[x-placement^="top"] .arrow::before {
        border-top-color: var(--primary-dark-color) !important;
    }

    /* =================================
       ICONS & HIGHLIGHTS
       ================================= */

    .icon-primary,
    .text-primary-emphasis {
        color: var(--primary-color) !important;
    }

    .bg-primary-light {
        background-color: var(--primary-light-color) !important;
    }

    /* Selected items */
    .selected,
    .highlighted {
        background-color: var(--primary-light-color) !important;
        border-left: 3px solid var(--primary-color) !important;
    }

    /* =================================
       KANBAN & DRAG-DROP
       ================================= */

    .kanban-item.dragging {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 10px rgba(var(--primary-rgb), 0.3) !important;
    }

    .kanban-column.drag-over {
        background-color: var(--primary-light-color) !important;
    }

    /* =================================
       DATETIME PICKER
       ================================= */

    .flatpickr-day.selected,
    .flatpickr-day.startRange,
    .flatpickr-day.endRange {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .flatpickr-day:hover {
        background-color: var(--primary-light-color) !important;
    }

    /* =================================
       STATUS INDICATORS
       ================================= */

    .status-success,
    .status-dot.success {
        background-color: var(--success-color) !important;
    }

    .status-warning,
    .status-dot.warning {
        background-color: var(--warning-color) !important;
    }

    .status-danger,
    .status-dot.danger {
        background-color: var(--danger-color) !important;
    }

    /* =================================
       CHARTS (if using Chart.js)
       ================================= */

    .chart-primary {
        color: var(--primary-color) !important;
    }

    /* =================================
       SCROLLBAR
       ================================= */

    ::-webkit-scrollbar-thumb {
        background-color: var(--primary-light-color) !important;
    }

    ::-webkit-scrollbar-thumb:hover {
        background-color: var(--primary-color) !important;
    }

    /* =================================
       UTILITY CLASSES
       ================================= */

    .border-primary {
        border-color: var(--primary-color) !important;
    }

    .border-success {
        border-color: var(--success-color) !important;
    }

    .border-warning {
        border-color: var(--warning-color) !important;
    }

    .border-danger {
        border-color: var(--danger-color) !important;
    }

    /* =================================
       FOCUS STATES
       ================================= */

    *:focus-visible {
        outline-color: var(--primary-color) !important;
    }

    /* =================================
       SELECTION
       ================================= */

    ::selection {
        background-color: var(--primary-color) !important;
        color: white !important;
    }

    ::-moz-selection {
        background-color: var(--primary-color) !important;
        color: white !important;
    }

    /* =================================
       LOGOS CUSTOMIZADOS
       ================================= */

    @php
        $themeConfig = app('theme')->getConfig();
    @endphp

    @if($themeConfig->logo_main)
        /* Logo principal (sidebar desktop, mobile) */
        img[src*="logo.svg"]:not([src*="dark-logo"]):not([src*="mobile"]) {
            content: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_main) }}') !important;
        }
    @endif

    @if($themeConfig->logo_light)
        /* Logo claro (modo escuro) */
        img[src*="dark-logo.svg"] {
            content: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_light) }}') !important;
        }
    @endif

    @if($themeConfig->logo_icon)
        /* Logo mobile */
        img[src*="mobile-light-logo.svg"],
        img[src*="mobile-dark-logo.svg"] {
            content: url('{{ asset("storage/theme-manager/" . $themeConfig->logo_icon) }}') !important;
        }
    @endif

    @if($themeConfig->favicon)
        /* Favicon */
        link[rel="icon"] {
            href: url('{{ asset("storage/theme-manager/" . $themeConfig->favicon) }}') !important;
        }
    @endif
</style>
