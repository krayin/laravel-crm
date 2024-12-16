
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('invoice', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('invoice::app.layouts.invoice'), route('admin.invoice.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('invoice.create', function (BreadcrumbTrail $trail) {
    $trail->parent('invoice');
    $trail->push(trans('invoice::app.invoices.create.title'), route('admin.invoice.create'));
});

// invoice Edit
Breadcrumbs::for('invoice.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('invoice');
    $trail->push(trans('invoice::app.invoices.edit.title'), route('admin.invoice.edit', $lead->id));
});

// Dashboard > invoice > Title
Breadcrumbs::for('invoice.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('invoice');
    $trail->push('#'.$lead->id, route('admin.invoice.view', $lead->id));
});
