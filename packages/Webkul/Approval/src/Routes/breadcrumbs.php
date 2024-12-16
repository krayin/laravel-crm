
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('approval', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('approval::app.layouts.approval'), route('admin.approval.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('approval.create', function (BreadcrumbTrail $trail) {
    $trail->parent('approval');
    $trail->push(trans('approval::app.approvals.create.title'), route('admin.approval.create'));
});

// approval Edit
Breadcrumbs::for('approval.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('approval');
    $trail->push(trans('approval::app.approvals.edit.title'), route('admin.approval.edit', $lead->id));
});

// Dashboard > approval > Title
Breadcrumbs::for('approval.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('approval');
    $trail->push('#'.$lead->id, route('admin.approval.view', $lead->id));
});
