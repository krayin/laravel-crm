<?php

return [
    [
        'key'   => 'approval',
        'name'  => 'Approval',
        'route' => 'admin.approval.index',
        'sort'  => 3
    ],
    [
        'key'   => 'approval.edit',
        'name'  => 'Edit Approval',
        'route' => 'admin.approval.edit',
        'sort'  => 1
    ],
    [
        'key'   => 'approval.delete',
        'name'  => 'Delete Approval',
        'route' => 'admin.approval.delete',
        'sort'  => 1
    ],
    // mass_delete
    [
        'key'   => 'approval.mass_delete',
        'name'  => 'Mass Delete Approval',
        'route' => 'admin.approval.mass_delete',
        'sort'  => 1
    ],
    
];
