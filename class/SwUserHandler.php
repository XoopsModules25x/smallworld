<?php

namespace XoopsModules\Smallworld;

/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Smallworld
 *
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @author       XOOPS Module Development Team
 * @copyright    Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.16
 */

/**
 * Smallworld User Object Handler
 *
 */
class SwUserHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Class constructor
     *
     * @param \XoopsDatabase $db
     * @return void
     */
    function __construct(&$db = null)
    {
        parent::__construct($db, 'smallworld_user', SwUser::class, 'id', 'username');
    }
}
