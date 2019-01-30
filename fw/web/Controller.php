<?php

namespace fw\web;

use fw\Error;
use fw\Framework;


abstract class Controller
{
    /** @var \fw\web\Request */
    public $request = null;
    /** @var Application */
    public $app = null;
    public $id = null;
    public $actionId = null;

    public $layout = 'layouts/main';


    public static $httpStatuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        118 => 'Connection timed out',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        210 => 'Content Different',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        310 => 'Too many Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range unsatisfiable',
        417 => 'Expectation failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable entity',
        423 => 'Locked',
        424 => 'Method failure',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway or Proxy Error',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        507 => 'Insufficient storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];


    public function __construct(\fw\web\Application $app, $config = null)
    {
        $this->app = $app;
        if ( $config !== null ) {
            if ( !is_array($config) ) {
                throw new Error("Param \"config\" must be array");
            }
            foreach ( $config as $attributeName => $value ) {
                $this->$attributeName = $value;
            }
        }
        $this->init();
    }

    public function init()
    {
    }

    public function beforeAction()
    {
    }

    public function afterAction()
    {
    }

    /**
     * @param $id string action id
     * @throws \fw\HttpError if action not found
     */
    public function runAction($id)
    {
        $functionName = 'action' . Framework::slugToName($id);
        if (!method_exists($this, $functionName)) {
            throw new \fw\HttpError(404, 'Action not found');
        }
        $content = $this->$functionName();
        return $content;
    }

    public function render($viewName, $params = null)
    {
        $view = $this->createView($viewName);
        $content = $view->render($params);

        $layoutView = $this->createView($this->layout);
        $layoutView->title = $view->title;
        $content = $layoutView->render(['content' => $content]);

        return $content;
    }

    public function renderPartial($viewName, $params = null)
    {
        $view = $this->createView($viewName);
        $content = $view->render($params);

        return $content;
    }

    public function createView ( $viewName )
    {
        $view = new \fw\web\View($this->app, $this, $viewName);
        if (!$view->exist()) {
            throw new \fw\HttpError(404, "View [{$view->getFilepath()}] not found");
        }
        return $view;
    }

    public function redirect ( $path, $statusCode = 302 )
    {
        $version = $this->request->getVersion();
        $statusCode = (int) $statusCode;
        $statusText = self::$httpStatuses[$statusCode];

        header("HTTP/{$version} {$statusCode} {$statusText}");
        header("Location: $path");
        return '';
    }
}
