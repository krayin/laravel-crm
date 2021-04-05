<?php
    return [
        'layouts' => [
            'dashboard'     => 'Dashboard',
            'leads'         => 'Leads',
            'contacts'      => 'Contacts',
            'customers'     => 'Customers',
            'companies'     => 'Companies',
            'settings'      => 'Settings',
            'roles'         => 'Roles',
            'users'         => 'Users',
            'my-account'    => 'My Account',
            'sign-out'      => 'Sign Out',
            'submit'        => 'Submit',
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
                'title' => 'Roles'
            ],

            'users' => [
                'title'             => 'Users',
                'create_user'       => 'Create User',
                'name'              => 'Name',
                'email'             => 'Email',
                'password'          => 'Password',
                'role'              => 'Role',
                'status'            => 'Status',
                'confirm_password'  => 'Confirm password',
            ]
        ],

        'datagrid' => [
            'id'    => 'Id',
            'name'  => 'Name',
        ],

        'response' => [
            'create-success' => 'Success: :name created successfully.',
        ]
    ];
?>