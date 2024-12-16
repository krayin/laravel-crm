
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('client', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('client::app.layouts.client'), route('admin.client.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('client.create', function (BreadcrumbTrail $trail) {
    $trail->parent('client');
    $trail->push(trans('client::app.clients.create.title'), route('admin.client.create'));
});

// client Edit
Breadcrumbs::for('client.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('client');
    $trail->push(trans('client::app.clients.edit.title'), route('admin.client.edit', $lead->id));
});

// Dashboard > client > Title
Breadcrumbs::for('client.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('client');
    $trail->push('#'.$lead->id, route('admin.client.view', $lead->id));
});
