<?php
return [
    'db' => [
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'shopware',
        'host' => 'localhost',
        'port' => '3306'
    ],

    'front' => [
        'throwExceptions' => true,
        'showException' => true
    ],

    'phpsettings' => [
        'display_errors' => 1
    ],

    'template' => [
        'forceCompile' => true
    ],

    'csrfProtection' => [
        'frontend' => true,
        'backend' => true
    ],

    'httpcache' => [
        'debug' => true
    ]
];