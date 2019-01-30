<?php

namespace models;


class LoginForm
{
    public $id = null;
    public $username = null;
    public $password = null;

    private $app = null;
    private $_attributes = [];
    private $_errors = [];

    public static $users = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => '123',
        ],
    ];


    public function __construct(\fw\web\Application $app)
    {
        $this->app = $app;
    }

    public function getSafeAttributes()
    {
        return ['username', 'password'];
    }

    public function load($attributes)
    {
        $this->_attributes = $attributes;
        return true;
    }

    public function addError($attribute, $text)
    {
        if (!isset($this->_errors[$attribute])) {
            $this->_errors[$attribute] = [];
        }
        $this->_errors[$attribute][] = $text;
    }

    public function getError($attribute = null)
    {
        if ($attribute !== null) {
            return $this->_errors[$attribute][0] ?? null;
        }
        return $this->_errors;
    }

    public function getErrors()
    {
        $list = [];
        foreach ($this->_errors as $attribute => $errors) {
            foreach ($errors as $text) {
                $list[] = $text;
            }
        }
        return $list;
    }

    public function hasErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors !== [];
        }
        return isset($this->_errors[$attribute]);
    }

    public function validate()
    {
        foreach ($this->getSafeAttributes() as $attribute) {
            $this->$attribute = trim($this->_attributes[$attribute]);
        }

        $user = null;
        foreach (self::$users as $userData) {
            if (
                mb_strtolower($this->username) == $userData['username']
                && $this->password == $userData['password']
            ) {
                $user = $userData;
                break;
            }
        }
        if ($user) {
            $this->id = $userData['id'];
            $this->username = $userData['username'];
        } else {
            $this->addError('password', 'Invalid username or password.');
        }

        return !$this->hasErrors();
    }

    public function login()
    {
        if (!$this->validate()) {
            return false;
        }
        return $this->app->user->login($this->id, $this->username);
    }
}