<?php

namespace fw\web;

use fw\Framework;


class View
{
    public $app = null;
    public $controller = null;
    public $id = null;

    public $title = null;


    public function __construct(\fw\web\Application $app, \fw\web\Controller $controller, string $viewName)
    {
        $this->app = $app;
        $this->controller = $controller;
        $this->id = $viewName;

        $this->init();
    }

    public function init()
    {

    }

    public function getFilepath()
    {
        return strtr('{root}/views/{id}.php', [
            '{root}' => \fw\Framework::getRootPath(),
            '{id}' => $this->id,
        ]);
    }

    public function exist()
    {
        return is_file($this->getFilepath());
    }


    public function render($_params_)
    {
        $_file_ = $this->getFilepath();
        $_ob_level_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        if ($_params_) {
            extract($_params_, EXTR_OVERWRITE);
        }
        $app = $this->app;
        $controller = $this->controller;
        try {
            require $_file_;
            return ob_get_clean();
        } catch (\Exception $e) {
            while (ob_get_level() > $_ob_level_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_ob_level_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }

    public function renderPartial($viewName, $params)
    {
        $view = $this->controller->createView($viewName);
        $content = $view->render($params);

        return $content;
    }

    public function encode($text)
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    public function url($url)
    {
        $path = $url[0];
        unset($url[0]);
        $path = trim($path, '/');
        $path = explode('/', $path, 3);
        $path = $path[0] . (isset($path[1]) ? '/' . $path[1] : '');
        $query = http_build_query($url);
        $url = '/' . $path;
        if (!empty($query)) {
            $url .= '?' . $query;
        }
        return $url;
    }

}
