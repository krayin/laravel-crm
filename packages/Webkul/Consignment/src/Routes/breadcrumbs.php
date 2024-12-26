
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('consignment', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('consignment::app.layouts.consignment'), route('admin.consignment.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('consignment.create', function (BreadcrumbTrail $trail) {
    $trail->parent('consignment');
    $trail->push(trans('consignment::app.consignments.create.title'), route('admin.consignment.create'));
});

// consignment Edit
Breadcrumbs::for('consignment.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('consignment');
    $trail->push(trans('consignment::app.consignments.edit.title'), route('admin.consignment.edit', $lead->id));
});

// Dashboard > consignment > Title
Breadcrumbs::for('consignment.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('consignment');
    $trail->push('#'.$lead->id, route('admin.consignment.view', $lead->id));
});
