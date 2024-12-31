
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('transaction', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('transaction::app.layouts.transaction'), route('admin.transaction.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('transaction.create', function (BreadcrumbTrail $trail) {
    $trail->parent('transaction');
    $trail->push(trans('transaction::app.transactions.create.title'), route('admin.transaction.create'));
});

// transaction Edit
Breadcrumbs::for('transaction.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('transaction');
    $trail->push(trans('transaction::app.transactions.edit.title'), route('admin.transaction.edit', $lead->id));
});

// Dashboard > transaction > Title
Breadcrumbs::for('transaction.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('transaction');
    $trail->push('#'.$lead->id, route('admin.transaction.view', $lead->id));
});
