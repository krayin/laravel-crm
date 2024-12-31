
<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Leads
Breadcrumbs::for('announcement', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('announcement::app.layouts.announcement'), route('admin.announcement.index'));
});

// Dashboard > Leads > Create
Breadcrumbs::for('announcement.create', function (BreadcrumbTrail $trail) {
    $trail->parent('announcement');
    $trail->push(trans('announcement::app.announcements.create.title'), route('admin.announcement.create'));
});

// announcement Edit
Breadcrumbs::for('announcement.edit', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('announcement');
    $trail->push(trans('announcement::app.announcements.edit.title'), route('admin.announcement.edit', $lead->id));
});

// Dashboard > announcement > Title
Breadcrumbs::for('announcement.view', function (BreadcrumbTrail $trail, $lead) {
    $trail->parent('announcement');
    $trail->push('#'.$lead->id, route('admin.announcement.view', $lead->id));
});
