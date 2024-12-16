
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('order', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('order::app.layouts.order'), route('admin.order.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('order.create', function (BreadcrumbTrail $trail) {
    $trail->parent('order');
    $trail->push(trans('order::app.orders.create.title'), route('admin.order.create'));
});

// order Edit
Breadcrumbs::for('order.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('order');
    $trail->push(trans('order::app.orders.edit.title'), route('admin.order.edit', $lead->id));
});

// Dashboard > order > Title
Breadcrumbs::for('order.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('order');
    $trail->push('#'.$lead->id, route('admin.order.view', $lead->id));
});
