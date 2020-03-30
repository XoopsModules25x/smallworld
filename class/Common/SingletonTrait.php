<?php

namespace XoopsModules\Smallworld\Common;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 *
 * @copyright      XOOPS Project  (https://xoops.org)
 * @license        GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author         Michael Beck <mambax7@gmailc.com>
 */

/**
 * Singleton patter in php
 **/
trait SingletonTrait
{
    protected static $instance = null;

    /**
     * call this method to get instance
     **/
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * protected so no one else can instance it
     **/
    protected function __construct() { }

    protected function __clone() { }

    protected function __sleep() { }

    protected function __wakeup() { }
}
