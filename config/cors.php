<?php

return [
    'paths' => ['api/*','email/verify/*', 'sanctum/*','login', 'logout', 'register', 'email/*', 'password/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
    'https://aiboostusa.com',
    'https://www.aiboostusa.com',
    'https://api.aiboostusa.com'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => ['*'],
    'max_age' => 0,
    'supports_credentials' => true,
];
