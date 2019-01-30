<?php

namespace fw\web;

use \fw\AppComponent;


class User extends AppComponent
{
    public $id = 0;
    public $username = 'anonymous';


    public function init()
    {
        @session_start();
        if (
            isset($_SESSION['user_id'])
            && isset($_SESSION['user_name'])
        ) {
            $this->id = (int)$_SESSION['user_id'];
            $this->username = $_SESSION['user_name'];
        }
    }

    public function destroy()
    {
    }

    public function login($id, $username)
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $username;
        return true;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
    }

    public function getSessionId()
    {
        return session_id();
    }

    public function isGuest()
    {
        return $this->id == 0;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getId()
    {
        return $this->id;
    }
}
