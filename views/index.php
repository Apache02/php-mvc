<?php

/**
 * @var $this \fw\web\View
 * @var $app \fw\web\Application
 * @var $controller \fw\web\Controller
 * @var $dataProvider \fw\data\DataProvider
 */

$this->title = 'Tasks';

$url = $controller->request->toUrl();

?>

<h1 class="ui header">Tasks</h1>

<div class="table-wrapper">

    <div class="ui horizontal menu">
        <a href="javascript:void(0);" class="item" onclick="showModalAdd()">
            <i class="icon plus"></i>
            New task
        </a>
    </div>

    <div class="ui pagination menu">
        <?php for ($page = 1; $page < $dataProvider->getTotalPages() + 1; $page++) : ?>
            <a class="item" href="<?= $this->url(array_merge($url, ['page' => $page])) ?>"><?= $page ?></a>
        <?php endfor; ?>
    </div>


    <table class="ui celled basic sortable table">
        <thead>
        <tr>
            <th>id</th>
            <th class="<?= $dataProvider->getSortCssClass('username') ?>">
                <a href="<?= $dataProvider->getSortUrl($url[0], 'username') ?>">Username</a>
            </th>
            <th class="<?= $dataProvider->getSortCssClass('email') ?>">
                <a href="<?= $dataProvider->getSortUrl($url[0], 'email') ?>">E-mail</a>
            </th>
            <th class="<?= $dataProvider->getSortCssClass('status') ?>">
                <a href="<?= $dataProvider->getSortUrl($url[0], 'status') ?>">Status</a>
            </th>
            <th>Text</th>
            <?php if ( $app->user->username == 'admin' ) : ?>
              <th></th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $model) : ?>
            <?php $deco = \models\TaskDecorator::decorate($model, $app) ?>
            <tr>
                <td><?= $model->id ?></td>
                <td><?= $deco->username ?></td>
                <td><?= $deco->email ?></td>
                <td><?= $deco->statusLabelHtml ?></td>
                <td><?= $deco->text ?></td>
                <?php if ( $app->user->username == 'admin' ) : ?>
                    <td style="width:3em;">
                        <a href="<?= $this->url(['site/edit-task', 'id'=>$model->id]) ?>" class="action-edit"><i class="icon edit"></i></a>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="ui pagination menu">
        <?php for ($page = 1; $page < $dataProvider->getTotalPages() + 1; $page++) : ?>
            <a class="item" href="<?= $this->url(array_merge($url, ['page' => $page])) ?>"><?= $page ?></a>
        <?php endfor; ?>
    </div>

</div>


<div id="form-modal" class="ui small modal">
    <div class="header">Task</div>
    <div class="content">
        <form action="/site/create-task" method="post" class="ui form">

            <div class="field">
                <div class="ui input left icon">
                    <i class="icon user"></i>
                    <input type="text" name="username" placeholder="User name"/>
                </div>
            </div>

            <div class="field">
                <div class="ui input left icon">
                    <i class="icon at"></i>
                    <input type="email" name="email" placeholder="Email"/>
                </div>
            </div>

            <div class="field">
                <div class="ui input">
                    <textarea name="text" placeholder="Text"></textarea>
                </div>
                <div class="ui pointing error label"></div>
            </div>

            <?php if ( $app->user->username == 'admin' ) : ?>
                <div class="field">
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="status" tabindex="0" class="hidden" value="1">
                        <label>Complete</label>
                    </div>
                </div>
            <?php endif; ?>

        </form>
    </div>
    <div class="actions">
        <button class="ui green inverted button ok">Ok</button>
        <button class="ui red inverted button cancel">Cancel</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function ($event) {
        var $modal = $('#form-modal');
        var $form = $modal.find('form');
        $modal.modal({
            onApprove: function ($button) {
                $button.addClass('loading');
                $.ajax({
                    url: $form.attr('action'),
                    method: $form.attr('method'),
                    data: $form.serialize(),
                    success: function (data) {
                        if (data.status == 'error') {
                            alert("Errors:\n" + data.errors.join("\n"));
                            return;
                        }
                        $modal.modal('hide');
                        location.href = '/';
                    },
                    error: function (err) {
                        alert(err);
                    },
                    complete: function () {
                        $button.removeClass('loading');
                    },
                });
                return false;
            }
        });

        $('.ui.checkbox').checkbox();
    });

    function showModalAdd() {
        $('#form-modal')
            .modal('show')
            .attr('action', '/site/create-task');
    }

</script>