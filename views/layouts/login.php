<?php

/**
 * @var $this \fw\web\View
 * @var $app \fw\web\Application
 * @var $controller \fw\web\Controller
 * @var $content string
 *
 * @var $title
 */

$title = 'Login | ' . $app->name;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" rel="stylesheet">
    <title><?= $title ?></title>
</head>
<body>

<style type="text/css">
    body {
        background-color: #dadada;
    }
    body > .grid {
        height: 100%;
    }
    .image {
        margin-top: -100px;
    }
    #content {
        max-width: 450px;
    }
</style>

<div class="ui middle aligned center aligned grid">
    <div id="content" class="column">

        <?= $content ?>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>

</body>
</html>
