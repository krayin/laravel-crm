<?php

return [
    'common' => [
        'auth' => [
            'login'    => [
                'success' => 'Login successful.',
                'logout'  => 'Logout successful.',
            ],
        ],

        'resource-not-found'    => 'The requested resource could not be found.',
        'forbidden-error'       => 'You do not have permission to access this resource.',
        'unauthenticated'       => 'You are not authenticated. Please log in to continue.',
        'internal-server-error' => 'An unexpected error occurred on the server. Please try again later.',
    ],

    'products' => [
        'create-success'           => 'Product created successfully.',
        'updated-success'          => 'Product updated successfully.',
        'delete-success'           => 'Product deleted successfully.',
        'delete-failed'            => 'Product delete failed.',
        'inventory-create-success' => 'Inventory stored successfully.',
    ],

    'leads' => [
        'create-success'  => 'Lead created successfully.',
        'updated-success' => 'Lead updated successfully.',
        'delete-success'  => 'Lead deleted successfully.',
        'delete-failed'   => 'Lead delete failed.',
        'no-valid-files'  => 'No valid files found.',

        'view' => [
            'tags' => [
                'create-success' => 'Tag attached successfully.',
                'delete-success' => 'Tag detached successfully.',
            ],

            'quotes' => [
                'create-success' => 'Quote attached successfully.',
                'delete-success' => 'Quote detached successfully.',
            ],
        ],
    ],

    'quotes' => [
        'create-success'  => 'Quote created successfully.',
        'update-success'  => 'Quote updated successfully.',
        'delete-success'  => 'Quote deleted successfully.',
        'delete-failed'   => 'Quote delete failed.',
        'saved-to-draft'  => 'Quote saved to draft.',
    ],

    'mail' => [
        'create-success' => 'Email created successfully.',
        'update-success' => 'Email updated successfully.',
        'delete-success' => 'Email deleted successfully.',
        'delete-failed'  => 'Email delete failed.',
        'saved-to-draft' => 'Email saved to draft.',

        'view' => [
            'tags' => [
                'create-success' => 'Tag attached successfully.',
                'delete-success' => 'Tag detached successfully.',
            ],
        ],
    ],

    'activities' => [
        'create-success' => 'Activity created successfully.',
        'update-success' => 'Activity updated successfully.',
        'delete-success' => 'Activity deleted successfully.',
        'delete-failed'  => 'Activity delete failed.',
    ],

    'contacts' => [
        'persons' => [
            'create-success'  => 'Person created successfully.',
            'update-success'  => 'Person updated successfully.',
            'delete-success'  => 'Person deleted successfully.',
            'delete-failed'   => 'Person delete failed.',

            'view' => [
                'tags' => [
                    'create-success' => 'Tag attached successfully.',
                    'delete-success' => 'Tag detached successfully.',
                ],
            ],
        ],

        'organizations' => [
            'create-success'  => 'Organization created successfully.',
            'update-success'  => 'Organization updated successfully.',
            'delete-success'  => 'Organization deleted successfully.',
            'delete-failed'   => 'Organization delete failed.',
        ],
    ],

    'settings' => [
        'tags' => [
            'create-success' => 'Tag created successfully.',
            'update-success' => 'Tag updated successfully.',
            'delete-success' => 'Tag deleted successfully.',
            'delete-failed'  => 'Tag delete failed.',
        ],

        'web-forms' => [
            'create-success'  => 'Web form created successfully.',
            'updated-success' => 'Web form updated successfully.',
            'delete-success'  => 'Web form deleted successfully.',
            'delete-failed'   => 'Web form delete failed.',
        ],

        'attributes' => [
            'create-success'    => 'Attribute created successfully.',
            'update-success'    => 'Attribute updated successfully.',
            'destroy-success'   => 'Attribute deleted successfully.',
            'delete-failed'     => 'Attribute delete failed.',
            'user-define-error' => 'User defined attribute can not be deleted.',
        ],

        'groups' => [
            'create-success'  => 'Group created successfully.',
            'update-success'  => 'Group updated successfully.',
            'destroy-success' => 'Group deleted successfully.',
            'delete-failed'   => 'Group delete failed.',
        ],

        'marketing' => [
            'events' => [
                'create-success'  => 'Marketing event created successfully.',
                'update-success'  => 'Marketing event updated successfully.',
                'destroy-success' => 'Marketing event deleted successfully.',
                'delete-failed'   => 'Marketing event delete failed.',
            ],

            'campaigns' => [
                'create-success'  => 'Marketing campaigns created successfully.',
                'update-success'  => 'Marketing campaigns updated successfully.',
                'destroy-success' => 'Marketing campaigns deleted successfully.',
                'delete-failed'   => 'Marketing campaigns delete failed.',
            ],
        ],

        'roles' => [
            'create-success' => 'Role created successfully.',
            'update-success' => 'Role updated successfully.',
            'delete-success' => 'Role deleted successfully.',
            'delete-failed'  => 'Role delete failed.',
        ],

        'users' => [
            'create-success'      => 'User created successfully.',
            'updated-success'     => 'User updated successfully.',
            'delete-success'      => 'User deleted successfully.',
            'delete-failed'       => 'User delete failed.',
            'last-delete-error'   => 'At least one user is required.',
            'mass-delete-success' => 'Selected users deleted successfully.',
            'mass-delete-failed'  => 'Selected users delete failed.',
            'mass-update-success' => 'Selected users updated successfully.',
            'mass-update-failed'  => 'Selected users update failed.',
        ],

        'pipelines' => [
            'create-success'       => 'Pipeline created successfully.',
            'updated-success'      => 'Pipeline updated successfully.',
            'delete-success'       => 'Pipeline deleted successfully.',
            'default-delete-error' => 'Default pipeline can not be deleted.',
        ],

        'sources' => [
            'create-success' => 'Source created successfully.',
            'update-success' => 'Source updated successfully.',
            'delete-success' => 'Source deleted successfully.',
            'delete-failed'  => 'Source delete failed.',
        ],

        'types' => [
            'create-success' => 'Type created successfully.',
            'update-success' => 'Type updated successfully.',
            'delete-success' => 'Type deleted successfully.',
            'delete-failed'  => 'Type delete failed.',
        ],

        'email-templates' => [
            'create-success' => 'Email template created successfully.',
            'update-success' => 'Email template updated successfully.',
            'delete-success' => 'Email template deleted successfully.',
            'delete-failed'  => 'Email template delete failed.',
        ],

        'workflows' => [
            'create-success' => 'Workflow created successfully.',
            'update-success' => 'Workflow updated successfully.',
            'delete-success' => 'Workflow deleted successfully.',
            'delete-failed'  => 'Workflow delete failed.',
        ],

        'warehouses' => [
            'create-success' => 'Warehouse created successfully.',
            'update-success' => 'Warehouse updated successfully.',
            'delete-success' => 'Warehouse deleted successfully.',
            'delete-failed'  => 'Warehouse delete failed.',

            'view' => [
                'locations' => [
                    'create-success' => 'Location created successfully.',
                    'update-success' => 'Location updated successfully.',
                    'delete-success' => 'Location deleted successfully.',
                    'delete-failed'  => 'Location delete failed.',
                ],

                'tags' => [
                    'create-success' => 'Tag attached successfully.',
                    'delete-success' => 'Tag detached successfully.',
                ],
            ],
        ],

        'webhooks' => [
            'create-success' => 'Webhook created successfully.',
            'update-success' => 'Webhook updated successfully.',
            'delete-success' => 'Webhook deleted successfully.',
            'delete-failed'  => 'Webhook delete failed.',
        ],

        'data-transfer' => [
            'imports' => [
                'create-success'    => 'Import created successfully.',
                'delete-failed'     => 'Import deletion failed unexpectedly.',
                'delete-success'    => 'Import deleted successfully.',
                'not-valid'         => 'Import is invalid',
                'nothing-to-import' => 'There are no resources to import.',
                'setup-queue-error' => 'Please change your queue driver to "database" or "redis" to start the import process.',
                'update-success'    => 'Import updated successfully.',
            ],
        ],
    ],

    'configuration' => [
        'save-success' => 'Configuration saved successfully.',
    ],
];
