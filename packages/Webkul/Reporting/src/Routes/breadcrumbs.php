
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('reporting', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('reporting::app.layouts.reporting'), route('admin.reporting.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('reporting.create', function (BreadcrumbTrail $trail) {
    $trail->parent('reporting');
    $trail->push(trans('reporting::app.reportings.create.title'), route('admin.reporting.create'));
});

// reporting Edit
Breadcrumbs::for('reporting.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('reporting');
    $trail->push(trans('reporting::app.reportings.edit.title'), route('admin.reporting.edit', $lead->id));
});

// Dashboard > reporting > Title
Breadcrumbs::for('reporting.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('reporting');
    $trail->push('#'.$lead->id, route('admin.reporting.view', $lead->id));
});
