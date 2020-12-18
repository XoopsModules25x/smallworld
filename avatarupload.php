<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * SmallWorld
 *
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $prevLogStat           = $GLOBALS['xoopsLogger']->activated;
    $GLOBALS['xoopsLogger']->activated = false;
    $userID                 = $GLOBALS['xoopsUser']->getVar('uid');
    $swDB                   = new Smallworld\SwDatabase();

    $uploaddir = XOOPS_ROOT_PATH . '/uploads/avatars/';
    //$file      = $uploaddir . basename($_FILES['smallworld_uploadfile']['name']);

    // Generate new name for file
    $f       = explode('.', basename(stripslashes($_FILES['smallworld_uploadfile']['name'])));
    $newname = time() . mt_rand(0000, 9999) . '.' . $f[1];
    $newfile = $uploaddir . basename($newname);
    // Save new name to users profile in DB
    $dbuserimage = 'avatars/' . basename(stripslashes($newfile));
    $swDB->updateSingleValue('smallworld_user', $userID, 'userimage', $dbuserimage);
    $swDB->updateSingleValue('smallworld_admin', $userID, 'userimage', $dbuserimage);

    // Return json array [0] = succes text and [1]= basename of the new file name...
    if (move_uploaded_file($_FILES['smallworld_uploadfile']['tmp_name'], $newfile)) {
        echo json_encode(['success', basename(stripslashes($newfile))]);
    } else {
        echo 'error';
    }
    // now restore the logger
    //$GLOBALS['xoopsLogger']->activated = $prevLogStat;
}
