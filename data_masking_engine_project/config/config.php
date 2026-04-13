<?php
declare(strict_types=1);
return [
    'app' => ['name' => 'Data Masking / Tokenization Engine', 'env' => 'dev'],
    'default_mode' => 'hybrid',
    'token_prefixes' => [
        'email' => 'EMAIL','iban' => 'IBAN','phone' => 'PHONE','person_name_like' => 'PERSON',
        'national_id_like' => 'NID','aws_access_key' => 'AWSKEY','generic_api_key' => 'APIKEY',
        'password_assignment' => 'SECRET','private_key_block' => 'PRIVATEKEY','connection_string' => 'CONNSTR',
        'env_variable' => 'SECRET',
    ],
];
