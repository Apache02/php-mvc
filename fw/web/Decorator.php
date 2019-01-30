<?php

namespace fw\web;


class Decorator
{
    public $app = null;

    protected $entity = null;

    /**
     * Decorator constructor.
     * @param \fw\db\ActiveRecord $model
     * @param \fw\web\Application $app
     */
    public function __construct($model, $app)
    {
        $this->entity = $model;
        $this->app = $app;
    }

    public static function decorate($model, $app)
    {
        if (!($model instanceof \fw\db\ActiveRecord)) {
            throw new \fw\Error('Attribute "model" must be instance of ActiveRecord');
        }
        $decoratedModel = new static($model, $app);
        return $decoratedModel;
    }

    // magic

    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        return $this->entity->{$name};
    }

    public function __call($name, $arguments)
    {
        return $this->entity->$name($arguments);
    }

    // end magic

    public function asEncoded($text)
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    public function asEmailLink ( $email )
    {
        $email = $this->asEncoded($email);
        return "<a href=\"mailto:$email\">$email</a>";
    }

}
