<?php
/*
 * jQuery File Upload Plugin PHP Example 4.2.4
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $GLOBALS['xoopsLogger']->activated = false;
    $img                    = new Smallworld\Images();
    $userID                 = $GLOBALS['xoopsUser']->getVar('uid');
    $user                   = new \XoopsUser($userID);
    $uploadHandler          = new Smallworld\UploadHandler();
}
