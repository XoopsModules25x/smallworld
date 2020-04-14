<?php
/**
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
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @license      GNU GPL (http://www.gnu.org/licenses/gpl-2.0.html/)
 * @package      SmallWorld
 * @since        1.0
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 */

use Xmf\Request;
use Xoopsmodules\smallworld;
require_once __DIR__ . '/header.php';

global $xoopsUser, $xoopsLogger;
require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
if ($xoopsUser) {
    $xoopsLogger->activated = false;
    $userID                 = $xoopsUser->getVar('uid');
    $user                   = new XoopsUser($userID);
    $db                     = new smallworld\SmallWorldDB;

    $uploaddir = XOOPS_ROOT_PATH . '/uploads/avatars/';
    $file      = $uploaddir . basename($_FILES['smallworld_uploadfile']['name']);
    $newname   = time() . mt_rand(0, 99999);

    // Generate new name for file
    $f       = explode('.', basename(stripslashes($_FILES['smallworld_uploadfile']['name'])));
    $newname = time() . mt_rand(0000, 9999) . '.' . $f[1];
    $newfile = $uploaddir . basename($newname);
    // Save new name to users profile in DB
    $dbuserimage = 'avatars/' . basename(stripslashes($newfile));
    $db->updateSingleValue('smallworld_user', $userID, 'userimage', $dbuserimage);
    $db->updateSingleValue('smallworld_admin', $userID, 'userimage', $dbuserimage);

    // Return json array [0] = succes text and [1]= basename of the new file name...
    if (move_uploaded_file($_FILES['smallworld_uploadfile']['tmp_name'], $newfile)) {
        echo json_encode(['success', basename(stripslashes($newfile))]);
    } else {
        echo 'error';
    }
}
