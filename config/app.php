<?php


return [
    'db' => [
        'class' => 'fw\\db\\DbConnection',
        'dsn' => 'mysql:host=localhost;dbname=test',
        'user' => 'test',
        'password' => '1',
    ],
    'user' => [
        'class' => 'fw\\web\\User',
    ],
];