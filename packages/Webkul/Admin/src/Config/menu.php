<?php

return [
    [
        'key'        => 'dashboard',
        'name'       => 'admin::app.layouts.dashboard',
        'route'      => 'admin.dashboard.index',
        'sort'       => 1,
        'icon-class' => 'dashboard-icon',
    ], [
        'key'        => 'leads',
        'name'       => 'admin::app.layouts.leads',
        'route'      => 'admin.leads.index',
        'sort'       => 2,
        'icon-class' => 'leads-icon',
    ], [
        'key'        => 'contacts',
        'name'       => 'admin::app.layouts.contacts',
        'route'      => 'admin.customers.index',
        'sort'       => 3,
        'icon-class' => 'phone-icon',
    ], [
        'key'        => 'contacts.customers',
        'name'       => 'admin::app.layouts.customers',
        'route'      => 'admin.customers.index',
        'sort'       => 1,
    ], [
        'key'        => 'contacts.companies',
        'name'       => 'admin::app.layouts.companies',
        'route'      => 'admin.companies.index',
        'sort'       => 2,
    ]
];