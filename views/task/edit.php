<?php

/**
 * @var $this \fw\web\View
 * @var $app \fw\web\Application
 * @var $controller \fw\web\Controller
 * @var $model \models\Task;
 */

$this->title = 'Task';

$url = $controller->request->toUrl();

?>

<h1 class="ui header">Edit Tasks</h1>

<form action="<?= $this->url($url) ?>" method="post" class="ui form">

    <div class="field">
        <div class="ui input left icon">
            <i class="icon user"></i>
            <input type="text" name="username" placeholder="User name" value="<?= $model->username ?>"/>
        </div>
    </div>

    <div class="field">
        <div class="ui input left icon">
            <i class="icon at"></i>
            <input type="email" name="email" placeholder="Email" value="<?= $model->email ?>"/>
        </div>
    </div>

    <div class="field">
        <div class="ui input">
            <textarea name="text" placeholder="Text"><?= $this->encode($model->text) ?></textarea>
        </div>
    </div>

    <?php if ( $app->user->username == 'admin' ) : ?>
        <div class="field">
            <div class="ui toggle checkbox">
                <input type="checkbox" name="status" tabindex="0" class="hidden" value="1" <?= $model->status == $model::STATUS_COMPLETE ? ' checked="checked"':'' ?>>
                <label>Complete</label>
            </div>
        </div>
    <?php endif; ?>

    <div class="ui divider"></div>

    <button class="ui green button ok" type="submit">Save</button>

</form>

<script>
    document.addEventListener('DOMContentLoaded', function ($event) {
        $('.ui.checkbox').checkbox();
    });
</script>