
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('budget', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('budget::app.layouts.budget'), route('admin.budget.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('budget.create', function (BreadcrumbTrail $trail) {
    $trail->parent('budget');
    $trail->push(trans('budget::app.budgets.create.title'), route('admin.budget.create'));
});

// budget Edit
Breadcrumbs::for('budget.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('budget');
    $trail->push(trans('budget::app.budgets.edit.title'), route('admin.budget.edit', $lead->id));
});

// Dashboard > budget > Title
Breadcrumbs::for('budget.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('budget');
    $trail->push('#'.$lead->id, route('admin.budget.view', $lead->id));
});
