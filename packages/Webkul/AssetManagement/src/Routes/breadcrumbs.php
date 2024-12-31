
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > warehouse
Breadcrumbs::for('warehouse', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('warehouse::app.layouts.warehouse'), route('admin.warehouse.index'));
});

// Dashboard > warehouse > Create
Breadcrumbs::for('warehouse.create', function (BreadcrumbTrail $trail) {
    $trail->parent('warehouse');
    $trail->push(trans('warehouse::app.warehouses.create.title'), route('admin.warehouse.create'));
});

// warehouse Edit
Breadcrumbs::for('warehouse.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('warehouse');
    $trail->push(trans('warehouse::app.warehouses.edit.title'), route('admin.warehouse.edit', $lead->id));
});

// Dashboard > warehouse > Title
Breadcrumbs::for('warehouse.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('warehouse');
    $trail->push('#'.$lead->id, route('admin.warehouse.view', $lead->id));
});

// instrument
Breadcrumbs::for('instrument', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('instrument::app.layouts.instrument'), route('admin.instrument.index'));
});

// Dashboard > instrument > Create
Breadcrumbs::for('instrument.create', function (BreadcrumbTrail $trail) {
    $trail->parent('instrument');
    $trail->push(trans('instrument::app.instrument.create.title'), route('admin.instrument.create'));
});

// instrument Edit
Breadcrumbs::for('instrument.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('instrument');
    $trail->push(trans('instrument::app.instrument.edit.title'), route('admin.instrument.edit', $lead->id));
});

// Dashboard > instrument > Title
Breadcrumbs::for('instrument.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('instrument');
    $trail->push('#'.$lead->id, route('admin.instrument.view', $lead->id));
});


// assetUtilization
// instrument
Breadcrumbs::for('assetUtilization', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('assetUtilization::app.layouts.assetUtilization'), route('admin.assetUtilization.index'));
});

// Dashboard > assetUtilization > Create
Breadcrumbs::for('assetUtilization.create', function (BreadcrumbTrail $trail) {
    $trail->parent('assetUtilization');
    $trail->push(trans('assetUtilization::app.assetUtilization.create.title'), route('admin.assetUtilization.create'));
});

// assetUtilization Edit
Breadcrumbs::for('assetUtilization.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('assetUtilization');
    $trail->push(trans('assetUtilization::app.assetUtilization.edit.title'), route('admin.assetUtilization.edit', $lead->id));
});

// Dashboard > assetUtilization > Title
Breadcrumbs::for('assetUtilization.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('assetUtilization');
    $trail->push('#'.$lead->id, route('admin.assetUtilization.view', $lead->id));
});
