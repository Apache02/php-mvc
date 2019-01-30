<?php

require __DIR__ . '/../fw/Framework.php';


$config = require __DIR__ . '/../config/app.php';

$request = new \fw\web\Request();
$app = new \fw\web\Application($config);
$app->route($request)
    ->end();

