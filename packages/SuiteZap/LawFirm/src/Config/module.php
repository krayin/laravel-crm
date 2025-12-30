<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Information
    |--------------------------------------------------------------------------
    |
    | This file contains the basic information about the LawFirm module.
    |
    */

    'name' => 'LawFirm',

    'version' => '1.0.0',

    'description' => 'Law Firm Extension for Krayin CRM - Specialized features for legal practice management',

    /*
    |--------------------------------------------------------------------------
    | Module Dependencies
    |--------------------------------------------------------------------------
    |
    | List of modules that this module depends on.
    |
    */

    'dependencies' => [
        'Activity',
        'Attribute',
        'Contact',
        'Core',
        'Email',
        'Lead',
        'Product',
        'User',
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Settings
    |--------------------------------------------------------------------------
    |
    | Custom settings for the LawFirm module.
    |
    */

    'settings' => [
        'enable_case_management' => true,
        'enable_hearing_calendar' => true,
        'enable_document_management' => true,
        'enable_billing_integration' => true,
    ],
];
