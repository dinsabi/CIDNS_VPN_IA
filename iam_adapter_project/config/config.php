<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'AI Secure Gateway - IAM Adapter',
        'url' => 'http://localhost:8080',
        'env' => 'dev',
    ],

    'security' => [
        'session_user_key' => 'auth_user',
        'session_mfa_ok_key' => 'mfa_verified',
        'session_state_key' => 'oidc_state',
    ],

    'oidc' => [
        'issuer' => 'https://idp.example.com',
        'authorization_endpoint' => 'https://idp.example.com/oauth2/authorize',
        'token_endpoint' => 'https://idp.example.com/oauth2/token',
        'userinfo_endpoint' => 'https://idp.example.com/oauth2/userinfo',
        'client_id' => 'replace-me',
        'client_secret' => 'replace-me',
        'redirect_uri' => 'http://localhost:8080/?action=callback',
        'scopes' => 'openid profile email groups',
    ],

    'mfa' => [
        // Demo only. In production: encrypted store / vault.
        'totp_secrets' => [
            'alice@company.example' => 'JBSWY3DPEHPK3PXP',
            'bob@company.example'   => 'KRUGS4ZANFZSAYJA',
        ],
        // Demo override to simplify the walkthrough
        'demo_codes' => [
            'alice@company.example' => '123456',
            'bob@company.example' => '654321',
        ],
    ],

    'policies' => [
        '/' => [
            'mfa_required' => false,
            'allowed_roles' => ['*'],
            'classification' => 'public',
        ],
        '/hr' => [
            'mfa_required' => true,
            'allowed_roles' => ['admin', 'hr_manager', 'hr_analyst'],
            'required_department' => 'HR',
            'classification' => 'confidential',
        ],
        '/finance' => [
            'mfa_required' => true,
            'allowed_roles' => ['admin', 'finance_manager', 'finance_analyst'],
            'required_department' => 'Finance',
            'classification' => 'confidential',
        ],
        '/it' => [
            'mfa_required' => true,
            'allowed_roles' => ['admin', 'it_admin', 'security_architect'],
            'required_department' => 'IT',
            'classification' => 'internal',
        ],
        '/admin' => [
            'mfa_required' => true,
            'allowed_roles' => ['admin'],
            'classification' => 'restricted',
        ],
    ],

    'group_role_map' => [
        'grp-admin' => 'admin',
        'grp-hr-manager' => 'hr_manager',
        'grp-hr-analyst' => 'hr_analyst',
        'grp-finance-manager' => 'finance_manager',
        'grp-finance-analyst' => 'finance_analyst',
        'grp-it-admin' => 'it_admin',
        'grp-security-architect' => 'security_architect',
    ],
];
