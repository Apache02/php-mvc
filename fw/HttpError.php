<?php

namespace fw;


class HttpError extends \Exception
{

    public function __construct($code, $message)
    {
        parent::__construct($message, $code, null);
    }

    public function getName()
    {
        return self::class;
    }
}
