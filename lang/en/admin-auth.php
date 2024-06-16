<?php

declare(strict_types=1);

return [
    // Name of the administration menu
    'navigation-group' => 'Admin',

    // Validation error messages
    'validations' => [
        'node-parent' => 'Invalid specification for :attribute',
        'email-required' => 'Destination email address is not set',
    ],

    // Pages
    'pages' => [
        'login' => [
            'heading' => 'Login',
            'username' => 'Username',
            'email' => 'Email Address',
            'username-or-email' => 'Username or Email Address',
            'or' => 'or',
            'login-with-google' => 'Login with Google',
            'login-with-microsoft' => 'Login with Microsoft Account',
        ],
        'password-expired' => [
            'heading' => 'Change password',
            'subheading' => 'Your password has expired',
            'current-password' => 'Current password',
            'new-password' => 'New password',
            'change-password' => 'Change password',
            'incorrect-password' => 'Incorrect password',
            'password-changed' => 'Password has been changed',
        ]
    ],

    // Emails
    'emails' => [
        'password-reset' => [
            'subject' => 'Password issued for :app',
            'email' => 'Email Address',
            'username' => 'Username',
            'password' => 'Password',
            'login' => 'Log in to :app',
        ],
    ],

    // Permissions
    'permissions' => [
        'admin' => [
            'group' => 'Admin',
            'super' => 'All operations',
            'manage-admin-user' => 'Manage all :user',
            'manage-admin-user-in-group' => 'Manage :user in :group',
            'edit-admin-user-role' => 'Assign role to :user',
            'reset-admin-user-password' => 'Reset password',
            'delete-admin-user' => 'Delete :user',
            'manage-admin-group' => 'Manage :group',
            'manage-admin-role' => 'Manage roles',
        ],
    ],

    // Administration Users
    'admin-user' => [
        'model' => 'Admin users',
        'name' => 'Display name',
        'email' => 'Email address',
        'username' => 'Username',
        'password' => 'Password',
        'generate-password' => 'Automatically generate password',
        'force-change-password' => 'Require password change at first login',
        'send-password' => 'Send password by email',
        'is-active' => 'Status',
        'avatar' => 'Avatar',
        'created-at' => 'Created',
        'updated-at' => 'Updated',
        'roles' => 'Roles',
        'last-login-at' => 'Last Login',
        'initial-user' => 'Admin',
        'actions' => [
            'suspend' => 'Suspend Login',
            'suspend-succeed' => 'Login has been suspended',
            'resume' => 'Resume Login',
            'resume-succeed' => 'Login has been resumed',
            'reset-password' => 'Reset Password',
            'reset-password-succeed' => 'Password has been reset',
        ]
    ],

    // Administration Groups
    'admin-group' => [
        'model' => 'Admin groups',
        'name' => 'Name',
        'created-at' => 'Created',
        'updated-at' => 'Updated',
        'parent-id' => 'Parent :group',
        'roles' => 'Roles',
        'initial-group' => 'Default Group',
    ],
];
