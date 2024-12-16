
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('expense', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('expense::app.layouts.expense'), route('admin.expense.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('expense.create', function (BreadcrumbTrail $trail) {
    $trail->parent('expense');
    $trail->push(trans('expense::app.expenses.create.title'), route('admin.expense.create'));
});

// expense Edit
Breadcrumbs::for('expense.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('expense');
    $trail->push(trans('expense::app.expenses.edit.title'), route('admin.expense.edit', $lead->id));
});

// Dashboard > expense > Title
Breadcrumbs::for('expense.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('expense');
    $trail->push('#'.$lead->id, route('admin.expense.view', $lead->id));
});
