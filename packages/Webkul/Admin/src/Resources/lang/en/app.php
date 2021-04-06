<?php
    return [
        'layouts' => [
            'dashboard'         => 'Dashboard',
            'leads'             => 'Leads',
            'contacts'          => 'Contacts',
            'customers'         => 'Customers',
            'companies'         => 'Companies',
            'settings'          => 'Settings',
            'roles'             => 'Roles',
            'users'             => 'Users',
            'attributes'        => 'Attributes',
            'my-account'        => 'My Account',
            'sign-out'          => 'Sign Out',
            'back'              => 'Back',
            'name'              => 'Name',
        ],

        'contacts' => [
            'customers' => [
                'title' => 'Customers'
            ]
        ],

        'sessions' => [
            'login' => [
                'title'             => 'Login',
                'welcome'           => 'Welcome Back',
                'email'             => 'Email',
                'password'          => 'Password',
                'login'             => 'Login',
                'forgot-password'   => 'Forgot Password?',
                'login-error'       => 'Please check your credentials and try again.'
            ],

            'forgot-password' => [
                'title'                     => 'Forgot Password ?',
                'email'                     => 'Email',
                'send-reset-password-email' => 'Send Reset Password Email',
                'reset-link-sent'           => 'We have e-mailed your reset password link.',
                'email-not-exist'           => "We can not find a user with this e-mail address.",
                'back-to-login'             => 'Back to login'
            ],

            'reset-password' => [
                'title'             => 'Reset Password',
                'email'             => 'Email',
                'password'          => 'Password',
                'confirm-password'  => 'Confirm Password',
                'reset-password'    => 'Reset Password'
            ]
        ],

        'settings' => [
            'roles' => [
                'title'             => 'Roles',
                'role'              => 'Role',
                'edit_role'         => 'Edit Role',
                'description'       => 'Description',
                'create_role'       => 'Create Role',
                'permission_type'   => 'Permission type',
                'custom'            => 'Custom',
                'all'               => 'All',
                'save-btn-title'    => 'Save as Role',
                'update-btn-title'  => 'Update Role',
                'create-success'    => 'Role created successfully.',
                'update-success'    => 'Role updated successfully.',
                'delete-success'    => 'Role deleted successfully.',
                'delete-failed'     => 'Role can not be deleted.',
                'user-define-error' => 'Can not delete system role.',
                'being-used'        => 'Role can not be deleted, as this is being used in admin user.',
                'last-delete-error' => 'At least one role is required.'
            ],

            'users' => [
                'title'             => 'Users',
                'create_user'       => 'Create User',
                'edit_user'         => 'Edit User',
                'name'              => 'Name',
                'email'             => 'Email',
                'back'              => 'Back',
                'password'          => 'Password',
                'role'              => 'Role',
                'status'            => 'Status',
                'save-btn-title'    => 'Save as User',
                'update-btn-title'  => 'Update User',
                'confirm_password'  => 'Confirm password',
                'create-success'    => 'User created successfully.',
                'update-success'    => 'User updated successfully.',
                'delete-success'    => 'User deleted successfully.',
                'delete-failed'     => 'User can not be deleted.',
                'user-define-error' => 'Can not delete system user.',
                'last-delete-error' => 'At least one user is required.',
                'user-define-error' => 'Can not delete system user.',
            ],

            'attributes' => [
                'title'                 => 'Attributes',
                'add-title'             => 'Add Attribute',
                'edit-title'            => 'Edit Attribute',
                'back'                  => 'Back',
                'code'                  => 'Code',
                'name'                  => 'Name',
                'type'                  => 'Type',
                'text'                  => 'Text',
                'textarea'              => 'Textarea',
                'price'                 => 'Price',
                'boolean'               => 'Boolean',
                'select'                => 'Select',
                'multiselect'           => 'Multiselect',
                'datetime'              => 'Datetime',
                'date'                  => 'Date',
                'image'                 => 'Image',
                'file'                  => 'File',
                'checkbox'              => 'Checkbox',
                'is_required'           => 'Is Required',
                'is_unique'             => 'Is Unique',
                'yes'                   => 'Yes',
                'no'                    => 'No',
                'input_validation'      => 'Input Validation',
                'number'                => 'Number',
                'decimal'               => 'Decimal',
                'email'                 => 'Email',
                'url'                   => 'Url',
                'options'               => 'Options',
                'save-btn-title'        => 'Save as Attribute',
                'sort-order'            => 'Sort Order',
                'add-option-btn-title'  => 'Add Option',
                'create-success'        => 'Attribute created successfully.',
                'update-success'        => 'Attribute updated successfully.',
                'delete-success'        => 'Attribute deleted successfully.',
                'delete-failed'         => 'Attribute can not be deleted.',
                'user-define-error'     => 'Can not delete system attribute.'
            ],
        ],

        'datagrid' => [
            'id'                => 'Id',
            'name'              => 'Name',
            'email'             => 'Email',
            'status'            => 'Status',
            'active'            => 'Active',
            'inactive'          => 'Inactive',
            'description'       => 'Description',
            'permission_type'   => 'Permission type',
        ],

        'response' => [
            'create-success' => 'Success: :name created successfully.',
        ]
    ];
?>