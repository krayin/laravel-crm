
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('inventory', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('inventory::app.layouts.inventory'), route('admin.inventory.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('inventory.create', function (BreadcrumbTrail $trail) {
    $trail->parent('inventory');
    $trail->push(trans('inventory::app.inventories.create.title'), route('admin.inventory.create'));
});

// inventory Edit
Breadcrumbs::for('inventory.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('inventory');
    $trail->push(trans('inventory::app.inventories.edit.title'), route('admin.inventory.edit', $lead->id));
});

// Dashboard > inventory > Title
Breadcrumbs::for('inventory.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('inventory');
    $trail->push('#'.$lead->id, route('admin.inventory.view', $lead->id));
});
