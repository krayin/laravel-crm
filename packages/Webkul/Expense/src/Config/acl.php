<?php

return [
    [
        'key'   => 'expense',
        'name'  => 'Expense',
        'route' => 'admin.expense.index',
        'sort'  => 2
    ],
    [
        'key'   => 'expense.create',
        'name'  => 'Create Expense',
        'route' => 'admin.expense.create',
        'sort'  => 1
    ],
    [
        'key'   => 'expense.edit',
        'name'  => 'Edit Expense',
        'route' => 'admin.expense.edit',
        'sort'  => 1
    ],
    [
        'key'   => 'expense.delete',
        'name'  => 'Delete Expense',
        'route' => 'admin.expense.delete',
        'sort'  => 1
    ],
    // mass_delete
    [
        'key'   => 'expense.mass_delete',
        'name'  => 'Mass Delete Expense',
        'route' => 'admin.expense.mass_delete',
        'sort'  => 1
    ],
];
