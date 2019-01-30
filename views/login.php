<?php

/**
 * @var $this \fw\web\View
 * @var $app \fw\web\Application
 * @var $controller \fw\web\Controller
 * @var $model \models\LoginForm
 */
 
?>

<h2 class="ui image header">
    <div class="content">Log-in to your account</div>
</h2>

<form id="w0" class="ui form" action="/auth/login" method="post">
    <div class="ui stacked left aligned segment">

        <div class="field note description">
            <div>Name: admin</div>
            <div>Password: 123</div>
        </div>

        <div class="field required <?= $model->hasErrors('username') ? 'error' : '' ?>">

            <div class="ui input left icon">
                <i class="icon user"></i>
                <input type="text" name="username" autofocus="autofocus" placeholder="Name" aria-required="true" value="<?= $this->encode($model->username) ?>">
            </div>

        </div>
        <div class="field required<?= $model->hasErrors('password') ? 'error' : '' ?>">

            <div class="ui input left icon">
                <i class="icon lock"></i>
                <input type="password" name="password" value="" placeholder="Password" aria-required="true" value="">
            </div>

        </div>

        <?php if ( $model->hasErrors() ) ?>
            <div class="field note description error">
                <ul>
                    <?php foreach ( $model->getErrors() as $errorText ) : ?>
                        <li><?= $this->encode($errorText) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <? endif; ?>

        <button type="submit" class="ui fluid large teal submit button">Login</button>
    </div>


</form>