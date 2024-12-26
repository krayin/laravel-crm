
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('pricefinder', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('pricefinder::app.layouts.pricefinder'), route('admin.pricefinder.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('pricefinder.create', function (BreadcrumbTrail $trail) {
    $trail->parent('pricefinder');
    $trail->push(trans('pricefinder::app.pricefinders.create.title'), route('admin.pricefinder.create'));
});

// pricefinder Edit
Breadcrumbs::for('pricefinder.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('pricefinder');
    $trail->push(trans('pricefinder::app.pricefinders.edit.title'), route('admin.pricefinder.edit', $lead->id));
});

// Dashboard > pricefinder > Title
Breadcrumbs::for('pricefinder.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('pricefinder');
    $trail->push('#'.$lead->id, route('admin.pricefinder.view', $lead->id));
});
