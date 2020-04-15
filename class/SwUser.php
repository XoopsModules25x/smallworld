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

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

class SwUser extends \XoopsObject
{
    /**
     * Class constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->initVar('id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('userid', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('regdate', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('username', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('userimage', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('realname', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('gender', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('intingender', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('relationship', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('partner', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('searchrelat', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('birthday', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('birthplace', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('birthplace_lat', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('birthplace_lng', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('birthplace_country', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('politic', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('religion', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('emailtype', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('screenname_type', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('screenname', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('mobile', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('phone', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('adress', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('present_city', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('present_lat', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('present_lng', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('present_country', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('website', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('interests', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('music', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('tvshow', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('movie', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('books', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('aboutme', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('school_type', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('school', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('schoolstart', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('schoolstop', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('employer', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('position', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('jobstart', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('jobstop', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('description', XOBJ_DTYPE_ARRAY, null, false, 255);
        $this->initVar('friends', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('followers', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('admin_flag', XOBJ_DTYPE_INT, 0, false);
    }
}
