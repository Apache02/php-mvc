<?php

namespace fw\web;

use fw\db\ActiveRecord;
use \fw\web\Request;
use \fw\AppComponent;
use \fw\Framework;


/**
 * Class Application
 * @package fw\web
 *
 * @property \fw\web\User $user
 */
class Application
{
    public $name = 'App';
    public $controllersNamespace = 'controllers';
    public $defaultController = 'site';

    public $exitCode = 1;
    public $bootstrap = [];

    private $componentsConfig = [];
    private $components = [];


    public function __construct($config = null)
    {
        $reflection = new \ReflectionClass(self::class);
        if ($config) {
            foreach ($config as $attributeName => $value) {
                if ($reflection->hasProperty($attributeName)) {
                    // this is single property
                    $this->$attributeName = $value;
                    continue;
                }
                if (is_string($value)) {
                    $value = ['class' => $value];
                }
                if (!is_array($value) || !isset($value['class'])) {
                    throw new \fw\Error("Component config error");
                }
                $this->componentsConfig[$attributeName] = $value;
            }
        }
        $this->init();
    }

    public function init()
    {
        foreach ($this->bootstrap as $componentName) {
            $this->loadComponent($componentName);
        }
        ActiveRecord::$db = $this->db;
    }

    /**
     * @param $exitCode integer
     */
    public function end($exitCode = null)
    {
        if ($exitCode === null) {
            $exitCode = $this->exitCode;
        }
        // unload components
        foreach ($this->components as $component) {
            /** AppComponent $component */

        }
        exit($exitCode);
    }

    public function __get($componentName)
    {
        return $this->loadComponent($componentName);
    }


    /**
     * @param $componentName
     * @return AppComponent
     * @throws \fw\Error
     */
    public function loadComponent($componentName)
    {
        if (isset($this->components[$componentName])) {
            return $this->components[$componentName];
        }
        if (!isset($this->componentsConfig[$componentName])) {
            throw new \fw\Error("Component [$componentName] not found");
        }
        $config = $this->componentsConfig[$componentName];
        $component = $this->createComponent($config);
        $this->components[$componentName] = $component;
        return $component;
    }

    /**
     * @param $config
     * @return \fw\AppComponent
     * @throws \ReflectionException
     * @throws \fw\Error
     */
    public function createComponent($config)
    {
        $className = $config['class'];
        unset($config['class']);

        $reflectionClass = new \ReflectionClass($className);
        $reflectionConstructor = $reflectionClass->getConstructor();

        $params = [];
        foreach ($reflectionConstructor->getParameters() as $reflectionParameter) {
            $paramName = $reflectionParameter->name;
            if ($paramName == 'config') {
                $params[] = $config;
                continue;
            }
            $params[] = $this->loadComponent($paramName);
        }

        return $reflectionClass->newInstanceArgs($params);
    }

    public function instance($className)
    {
        return $this->createComponent(['class' => $className]);
    }

    public function parseRequest(Request $request)
    {
        $path = $request->pathInfo();
        $path = trim($path, ' /');
        $path = explode('/', $path, 3);
        $controllerId = empty(trim($path[0])) ? $this->defaultController : $path[0];
        $actionId = $path[1] ?? 'index';

        return [$controllerId, $actionId];
    }

    /**
     * @param $controllerId string
     * @param $config array
     * @return \fw\web\Controller
     * @throws \fw\Error
     * @throws \fw\HttpError
     */
    public function loadController($controllerId, $config = null)
    {
        try {
            $className = $this->controllersNamespace . '\\' . Framework::slugToName($controllerId) . 'Controller';
            $controller = new $className($this, $config);
            $controller->id = $controllerId;
            return $controller;
        } catch (\fw\ClassNotFoundError $e) {
            throw new \fw\HttpError(404, "Controller [$controllerId] not found");
        }
        throw new \fw\Error("Can't create controller [$controllerId]");
    }

    public function route(Request $request)
    {
        if (!($request instanceof Request)) {
            throw new \fw\Error("Required instance of Request");
        }
        $this->components['request'] = $request;

        try {
            list($controllerId, $actionId) = $this->parseRequest($request);
            $controller = $this->loadController($controllerId, [
                'request' => $request,
                'actionId' => $actionId,
            ]);
            $controller->beforeAction();
            $content = $controller->runAction($actionId);
            $controller->afterAction();
            echo $content;
        } catch (\fw\HttpError $e) {
            $statusCode = $e->getCode();
            $message = $e->getMessage();
            echo "<h1>{$statusCode}</h1><div>{$message}</div>";
        } catch (\fw\Error $e) {
            $statusCode = 500;
            $message = $e->getMessage();
            echo "<h1>{$statusCode}</h1><div>{$message}</div>";
        }

        return $this;
    }
}
