<?php

namespace fw;


class Error extends \Exception
{
    public function getName()
    {
        return self::class;
    }
}
