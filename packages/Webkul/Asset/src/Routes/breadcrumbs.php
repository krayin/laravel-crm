
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('asset', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('asset::app.layouts.asset'), route('admin.asset.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('asset.create', function (BreadcrumbTrail $trail) {
    $trail->parent('asset');
    $trail->push(trans('asset::app.assets.create.title'), route('admin.asset.create'));
});

// asset Edit
Breadcrumbs::for('asset.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('asset');
    $trail->push(trans('asset::app.assets.edit.title'), route('admin.asset.edit', $lead->id));
});

// Dashboard > asset > Title
Breadcrumbs::for('asset.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('asset');
    $trail->push('#'.$lead->id, route('admin.asset.view', $lead->id));
});
