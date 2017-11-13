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


use Xmf\Request;
use Xoopsmodules\smallworld;
require_once __DIR__ . '/header.php';

global $xoopsUser, $xoopsLogger;
require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/UploadHandler.php';
if ($xoopsUser) {
    $xoopsLogger->activated = false;
    $img                    = new smallworld\SmallWorldImages;
    $userID                 = $xoopsUser->getVar('uid');
    $user                   = new XoopsUser($userID);
    $uploadHandler          = new UploadHandler();
}
