
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('product', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('product::app.layouts.product'), route('admin.productmanagement.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('product.create', function (BreadcrumbTrail $trail) {
    $trail->parent('product');
    $trail->push(trans('product::app.products.create.title'), route('admin.productmanagement.create'));
});

// product Edit
Breadcrumbs::for('product.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('product');
    $trail->push(trans('product::app.products.edit.title'), route('admin.productmanagement.edit', $lead->id));
});

// Dashboard > product > Title
Breadcrumbs::for('product.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('product');
    $trail->push('#'.$lead->id, route('admin.productmanagement.view', $lead->id));
});
