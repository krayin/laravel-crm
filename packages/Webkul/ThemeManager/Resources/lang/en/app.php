<?php

return [
    'menu' => [
        'theme' => 'Theme',
        'theme-info' => 'Customize the visual appearance of your CRM',
    ],

    'settings' => [
        'title' => 'Theme Settings',
        'update-success' => 'Theme settings updated successfully!',
        'save-btn' => 'Save Settings',

        'activation' => [
            'title' => 'Activate Custom Theme',
            'description' => 'Enable or disable the custom theme. When disabled, the default Krayin theme will be used.',
            'is-active' => 'Theme Active',
            'info' => 'When disabled, the default Krayin theme will be used across the system.',
            'yes' => 'Yes',
            'no' => 'No',
        ],

        'colors' => [
            'title' => 'Theme Colors',
            'description' => 'Configure your theme\'s main colors. These colors will be applied throughout the system.',

            'primary' => 'Primary Brand Color',
            'primary-help' => 'Main color used in buttons, links and highlights',

            'primary-dark' => 'Dark Primary Color',
            'primary-dark-help' => 'Darker variation of primary color for hover states',

            'primary-light' => 'Light Primary Color',
            'primary-light-help' => 'Lighter variation of primary color for backgrounds and borders',

            'success' => 'Success Color',
            'success-help' => 'Color used for success messages and confirmations',

            'warning' => 'Warning Color',
            'warning-help' => 'Color used for warnings and alerts',

            'danger' => 'Danger Color',
            'danger-help' => 'Color used for errors and destructive actions',
        ],

        'logos' => [
            'title' => 'Logos and Favicon',
            'description' => 'Upload your custom logos. Recommended formats: SVG, PNG or ICO.',

            'main' => 'Main Logo',
            'main-help' => 'Used in the sidebar. Recommended: Transparent SVG or PNG (ideal height: 40-50px)',

            'light' => 'Light Logo',
            'light-help' => 'Used on dark backgrounds or dark mode. Recommended: Transparent SVG or PNG',

            'icon' => 'Logo Icon',
            'icon-help' => 'Used when the sidebar is collapsed. Square format (32x32px or 64x64px)',

            'favicon' => 'Favicon',
            'favicon-help' => 'Icon displayed in the browser tab. Recommended: ICO or PNG 32x32px',

            'delete-current' => 'Delete current image',
            'current-image' => 'Current image',
        ],

        'login' => [
            'title' => 'Login Page',
            'description' => 'Customize the appearance of the system login page.',

            'bg-image' => 'Background Image',
            'bg-image-help' => 'Recommended: JPG or PNG with minimum resolution of 1920x1080px',

            'bg-zoom' => 'Background Image Zoom',
            'bg-zoom-help' => 'Adjust the background image zoom (100% = original size)',

            'bg-opacity' => 'Overlay Opacity',
            'bg-opacity-help' => 'Opacity of the dark layer over the background image (0% = transparent, 100% = opaque)',

            'show-powered-by' => 'Show "Powered By"',
            'show-powered-by-help' => 'Display "Powered by Krayin" text in the login page footer',
        ],

        'login-card' => [
            'section-title' => 'Custom Login Card',
            'description' => 'Fully customize the appearance of the login box. Create a unique experience for your users.',

            'enabled' => 'Enable Custom Card',
            'enabled-help' => 'When enabled, the login card will have a custom appearance',

            'bg-image' => 'Card Background Image',
            'bg-image-help' => 'Background image displayed inside the login box',

            'bg-opacity' => 'Card Image Opacity',
            'bg-opacity-help' => 'Opacity of the login box background image',

            'overlay-color' => 'Overlay Color',
            'overlay-color-help' => 'Color of the layer over the image. Use rgba() format, e.g: rgba(10, 45, 15, 0.78)',

            'welcome-title' => 'Welcome Title',
            'welcome-title-help' => 'Main text displayed in the login box',

            'subtitle' => 'Subtitle',
            'subtitle-help' => 'Secondary text displayed below the title',

            'sparkles' => 'Sparkles Effect',
            'sparkles-help' => 'Adds animated sparkle points to the login box',

            'help-link' => 'Show Help Link',
            'help-link-help' => 'Display "Need help?" link in the login box',

            'support-email' => 'Support Email',
            'support-email-help' => 'Support contact email displayed in the help link',
        ],

        'empty-states' => [
            'title' => 'Empty States',
            'description' => 'Replace the empty state images displayed when there is no data. Accepts only SVG files.',

            'activities' => 'Activities',
            'calls' => 'Calls',
            'emails' => 'Emails',
            'meetings' => 'Meetings',
            'notes' => 'Notes',
            'organizations' => 'Organizations',
            'persons' => 'Persons',
            'leads' => 'Leads',
            'products' => 'Products',

            'delete-current' => 'Delete current SVG',
            'current-svg' => 'Current SVG',
            'info' => 'Recommended: optimized SVG files with dimensions between 200x200px and 400x400px',
        ],
    ],
];
