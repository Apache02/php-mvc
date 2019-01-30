<?php

/**
 * @var $this \fw\web\View
 * @var $app \fw\web\Application
 * @var $controller \fw\web\Controller
 * @var $content string
 *
 * @var $title
 */

$title = strtr('{page} | {app}', [
    '{page}' => $this->title ?? 'Untitled page',
    '{app}' => $app->name,
]);

$user = $app->user;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" rel="stylesheet">
    <title><?= $this->encode($title) ?></title>
    <style>
        .wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .wrapper > .ui.segment {
            flex: 1 0 auto;
        }
        .wrapper > .ui#topmenu,
        .wrapper > .ui#footer {
            flex: 0 0 auto;
        }

        #topmenu {
            border-radius: 0;
        }

        .ui.sortable.table > thead > tr > th {
            padding-top: 0;
            padding-bottom: 0;
            line-height: 3.28564286em;
        }
        .ui.sortable.table > thead > tr > th > a {
            display: inline-block;
            min-width: calc(100% - 1em);
        }

        .ui.form .field > .ui.error.label,
        .ui.form .field > .ui.checkbox > .ui.error.label {
            display: none;
        }
        .ui.form .field.error > .ui.error.label,
        .ui.form .field.error > .ui.checkbox > .ui.error.label {
            display: inline-block;
        }

    </style>
</head>
<body>

<div class="wrapper">

    <div id="topmenu" class="ui large menu">
        <div class="ui container">
            <a class="header item" href="/">
                <?= $this->encode($app->name) ?>
            </a>
            <div id="w0" class="right menu">
                <div class="item"><?= $this->encode($user->username) ?></div>

                <?php if ($user->isGuest()) : ?>
                    <a class="item" href="/auth/login"><i class="icon sign in"></i> Login </a>
                <?php else : ?>
                    <a class="item" href="/auth/logout"><i class="icon sign out"></i> Logout </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="ui main vertical segment">
        <div class="ui container" id="content">

            <?= $content ?>

        </div>
    </div>


    <footer id="footer" class="ui vertical inverted very padded segment">
        <div class="ui container">
            <div id="w1" class="ui grid equal height divided stackable inverted">
                <div class="three wide column">
                    <h4 class="ui header inverted">Application</h4>
                    <div class="ui link list inverted">
                        <a class="item" href="/"> Home</a>
                        <a class="item" href="/auth/login"><i class="icon sign in"></i> Login</a></div>
                </div>
                <div class="three wide column"><h4 class="ui header inverted">Links</h4>
                    <div class="ui link list inverted">
                        <a class="disabled item" href="#"><i class="icon github"></i> GitHub</a>
                        <a class="disabled item" href="#"><i class="icon facebook"></i> Favebook</a>
                        <a class="disabled item" href="#"><i class="icon vk"></i> VKontakte</a>
                        <a class="disabled item" href="#"><i class="icon youtube"></i> Youtube</a>
                    </div>
                </div>
                <div class="ten wide column"><h4 class="ui header inverted">About</h4>
                    <div>Powered by <a href="https://semantic-ui.com/" target="_blank">Semantic UI</a> v.2.4.1</div>
                    <div class="ui divider"></div>
                    <p>&copy; <?= $app->name ?> 2018</p></div>
            </div>
        </div>
    </footer>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>

</body>
</html>
