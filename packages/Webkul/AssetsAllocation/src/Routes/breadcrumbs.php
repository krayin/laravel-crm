
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('assetsAllocation', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('assetsAllocation::app.layouts.assetsAllocation'), route('admin.assetsAllocation.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('assetsAllocation.create', function (BreadcrumbTrail $trail) {
    $trail->parent('assetsAllocation');
    $trail->push(trans('assetsAllocation::app.assetsAllocations.create.title'), route('admin.assetsAllocation.create'));
});

// assetsAllocation Edit
Breadcrumbs::for('assetsAllocation.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('assetsAllocation');
    $trail->push(trans('assetsAllocation::app.assetsAllocations.edit.title'), route('admin.assetsAllocation.edit', $lead->id));
});

// Dashboard > assetsAllocation > Title
Breadcrumbs::for('assetsAllocation.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('assetsAllocation');
    $trail->push('#'.$lead->id, route('admin.assetsAllocation.view', $lead->id));
});
