<?php

namespace models;

/**
 * Class TaskDecorator
 * @package models
 * @property \models\Task $entity
 */
class TaskDecorator extends \fw\web\Decorator
{
    public function getStatusLabel ()
    {
        return $this->status
            ? 'Complete'
            : 'New';
    }

    public function getStatusLabelHtml ()
    {
        $color = $this->entity->status ? 'blue' : 'yellow';
        $label = $this->getStatusLabel();
        return "<div class=\"ui label {$color}\">{$label}</div>";
    }

    public function getUsername ()
    {
        return $this->asEncoded($this->entity->username);
    }

    public function getEmail ()
    {
        return $this->asEncoded($this->entity->email);
    }

    public function getEmailTag ()
    {
        return $this->asEmailLink($this->entity->email);
    }

    public function getText ()
    {
        $text = $this->entity->text;
        $text = $this->asEncoded($text);
        $text = nl2br($text);
        return $text;
    }

}