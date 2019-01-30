<?php

namespace fw\web;


class Request
{

    public function __construct()
    {
    }

    public function toArray()
    {
        return [
            'method' => $this->getMethod(),
            'get' => $this->get(),
            'post' => $this->post(),
            'cookies' => $this->cookies(),
        ];
    }

    /**
     * @param string|null $attribute
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function get($attribute = null, $defaultValue = null)
    {
        if ($attribute !== null) {
            return $_GET[$attribute] ?? $defaultValue;
        }
        return $_GET;
    }

    /**
     * @param string|null $attribute
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function post($attribute = null, $defaultValue = null)
    {
        if ($attribute !== null) {
            return $_POST[$attribute] ?? $defaultValue;
        }
        return $_POST;
    }

    public function cookies($attribute = null, $defaultValue = null)
    {
        if ($attribute !== null) {
            return $_COOKIE[$attribute] ?? $defaultValue;
        }
        return $_COOKIE;
    }

    /**
     * @return string
     */
    public function pathInfo()
    {
        return $_SERVER['PATH_INFO'] ?? '/';
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return 'GET';
    }

    /**
     * @return bool
     */
    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function getVersion ()
    {
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
            return '1.0';
        }
        return '1.1';
    }

    public function toUrl ()
    {
        $path = $this->pathInfo();
        $path = trim($path, '/');
        $path = explode('/', $path, 3);
        $path = $path[0] . (isset($path[1]) ? '/'.$path[1] : '');
        return array_merge([$path], $this->get());
    }

}

