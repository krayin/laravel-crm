
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('employee', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('employee::app.layouts.employee'), route('admin.employee.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('employee.create', function (BreadcrumbTrail $trail) {
    $trail->parent('employee');
    $trail->push(trans('employee::app.employees.create.title'), route('admin.employee.create'));
});

// employee Edit
Breadcrumbs::for('employee.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('employee');
    $trail->push(trans('employee::app.employees.edit.title'), route('admin.employee.edit', $lead->id));
});

// Dashboard > employee > Title
Breadcrumbs::for('employee.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('employee');
    $trail->push('#'.$lead->id, route('admin.employee.view', $lead->id));
});
