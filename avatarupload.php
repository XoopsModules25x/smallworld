<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  :            {@link https://xoops.org 2001-2017 XOOPS Project}
 * @license    :                {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * @module     :                Smallworld
 * @Author     :                Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @copyright  :            2011 Culex
 * @Repository path:        $HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/avatarupload.php $
 * @Last       committed:        $Revision: 8749 $
 * @Last       changed by:        $Author: beckmi $
 * @Last       changed date:    $Date: 2012-01-18 06:17:13 +0100 (on, 18 jan 2012) $
 * @ID         :                    $Id: avatarupload.php 8749 2012-01-18 05:17:13Z beckmi $
 **/
global $xoopsUser, $xoopsLogger;
include_once '../../mainfile.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
if ($xoopsUser) {
    $xoopsLogger->activated = false;
    $userID                 = $xoopsUser->getVar('uid');
    $user                   = new XoopsUser($userID);
    $db                     = new SmallWorldDB;

    $uploaddir = XOOPS_ROOT_PATH . '/uploads/avatars/';
    $file      = $uploaddir . basename($_FILES['smallworld_uploadfile']['name']);
    $newname   = time() . rand(0, 99999);

    // Generate new name for file
    $f       = explode('.', basename(stripslashes($_FILES['smallworld_uploadfile']['name'])));
    $newname = time() . rand(0000, 9999) . '.' . $f[1];
    $newfile = $uploaddir . basename($newname);
    // Save new name to users profile in DB
    $dbuserimage = 'avatars/' . basename(stripslashes($newfile));
    $db->updateSingleValue('smallworld_user', $userID, 'userimage', $dbuserimage);
    $db->updateSingleValue('smallworld_admin', $userID, 'userimage', $dbuserimage);

    // Return json array [0] = succes text and [1]= basename of the new file name...
    if (move_uploaded_file($_FILES['smallworld_uploadfile']['tmp_name'], $newfile)) {
        echo json_encode(array('success', basename(stripslashes($newfile))));
    } else {
        echo 'error';
    }
}
?>
