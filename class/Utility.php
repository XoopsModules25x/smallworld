<?php

namespace XoopsModules\Smallworld;

use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Common;
use XoopsModules\Smallworld\Constants;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------

    /**
     * Access the only instance of this class
     *
     * @return object
     *
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

}

