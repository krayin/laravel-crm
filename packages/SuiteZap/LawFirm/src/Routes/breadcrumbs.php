<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Dashboard > Processos
Breadcrumbs::for('lawfirm.processos.index', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard');
    $trail->push(trans('lawfirm::app.processos.title'), route('admin.processos.index'));
});

// Dashboard > Processos > Create
Breadcrumbs::for('lawfirm.processos.create', function (BreadcrumbTrail $trail) {
    $trail->parent('lawfirm.processos.index');
    $trail->push(trans('lawfirm::app.processos.create'), route('admin.processos.create'));
});

// Dashboard > Processos > Edit
Breadcrumbs::for('lawfirm.processos.edit', function (BreadcrumbTrail $trail, $processo) {
    $trail->parent('lawfirm.processos.index');
    $trail->push(trans('lawfirm::app.processos.edit'), route('admin.processos.edit', $processo->id));
});

// Dashboard > Processos > View
Breadcrumbs::for('lawfirm.processos.show', function (BreadcrumbTrail $trail, $processo) {
    $trail->parent('lawfirm.processos.index');
    $trail->push($processo->titulo, route('admin.processos.show', $processo->id));
});
