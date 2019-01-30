<?php

namespace controllers;


use fw\HttpError;
use models\Task;

class SiteController extends \fw\web\Controller
{

    public function renderJson($data)
    {
        header('Content-Type: application/json');
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function actionIndex()
    {
        $request = $this->request;
        $page = $request->get('page', 1);
        $sortAttribute = $request->get('sort', null);
        $sortAsc = $request->get('sort-dir', 'asc') != 'desc';

        $dataProvider = new \fw\data\DataProvider([
            'query' => Task::find(),
            'pageSize' => 3,
            'page' => $page,
        ]);

        $allowedSorts = ['username', 'email', 'status'];
        if (in_array($sortAttribute, $allowedSorts)) {
            $dataProvider->sort($sortAttribute, $sortAsc);
        }

        $dataProvider->updateCounters();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreateTask()
    {
        $model = new Task();
        $model->load($this->request->post());
        if ($model->save()) {
            return $this->renderJson([
                'status' => 'ok',
            ]);
        }

        return $this->renderJson([
            'status' => 'error',
            'errors' => $model->getErrors(),
        ]);
    }

    public function actionEditTask()
    {
        $id = $this->request->get('id', null);
        if ($id === null) {
            throw new HttpError(404, 'Not Found');
        }
        if ($this->app->user->username !== 'admin') {
            throw new HttpError(403, 'Forbidden');
        }
        $id = (int)$id;
        $model = Task::find()->byPk($id)->one();
        if (
            $this->request->isPost()
            && $model->load($this->request->post())
            && $model->save()
        ) {
            return $this->redirect('/');
        }
        return $this->render('task/edit', [
            'model' => $model,
        ]);
    }

}
