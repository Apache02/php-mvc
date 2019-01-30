<?php

namespace models;


class Task extends \fw\db\ActiveRecord
{
    const STATUS_NONE = 0;
    const STATUS_COMPLETE = 1;

    public $id;
    public $username;
    public $email;
    public $text;
    public $status;


    public static function tableName ()
    {
        return 'task';
    }

    public static function tableColumns ()
    {
        return [
            'id',
            'username',
            'email',
            'text',
            'status',
        ];
    }

    public static function tablePk ()
    {
        return 'id';
    }



    public function getSafeAttributes()
    {
        return ['username', 'email', 'text', 'status'];
    }

    public function validate()
    {
        $_attributes = $this->getDirtyAttributes();
        foreach ($this->getSafeAttributes() as $attributeName) {
            if ( isset($_attributes[$attributeName]) ) {
                $this->$attributeName = $_attributes[$attributeName];
            }
        }

        if ( !preg_match('/^[\w\d\s]+$/', $this->username) ) {
            $this->addError('username', 'Недопустимые символы');
        }

        if ( !preg_match('/^\w[\w\d_-]+@\w[\w\d_-]+\.\w+$/', $this->email) ) {
            $this->addError('email', 'Неверный email');
        }

        if ( $this->text === null ) {
            $this->text = '';
        }

        if ( $this->status === null ) {
            $this->status = self::STATUS_NONE;
        }

        return !$this->hasErrors();
    }

}