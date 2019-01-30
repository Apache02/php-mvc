<?php

namespace fw;


class ClassNotFoundError extends \fw\Error
{
    public function getName()
    {
        return self::class;
    }
}
