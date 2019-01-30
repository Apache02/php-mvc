<?php

namespace fw;

use \fw\Error;


abstract class AppComponent
{
    public function __construct($config = null)
    {
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

    public function init ()
    {
    }

    public function destroy ()
    {
    }
}
