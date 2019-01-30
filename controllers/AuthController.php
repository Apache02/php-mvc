<?php

namespace controllers;

use \models\LoginForm;


class AuthController extends \fw\web\Controller
{
    public $layout = 'layouts/login';

    public function actionIndex()
    {
        return $this->redirect('/auth/login');
    }

    public function actionLogin ()
    {
        $model = new LoginForm($this->app);
        if (
            $this->request->isPost()
            && $model->load($this->request->post())
            && $model->login()
        ) {
            return $this->redirect('/');
        }

        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout ()
    {
        $this->app->user->logout();
        return $this->redirect('/');
    }

}
