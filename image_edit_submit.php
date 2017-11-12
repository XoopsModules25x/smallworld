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
 * @Repository path:        $HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/image_edit_submit.php $
 * @Last       committed:        $Revision: 9032 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2012-02-20 06:15:13 +0100 (ma, 20 feb 2012) $
 * @ID         :                    $Id: image_edit_submit.php 9032 2012-02-20 05:15:13Z djculex $
 **/
include_once('../../mainfile.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php');
global $xoopsUser, $xoTheme, $xoopsTpl, $xoopsLogger;
$xoopsLogger->activated = false;
$db                     = new SmallWorldDB;
$upd                    = new Wall_Updates;
$userID                 = $xoopsUser->getVar('uid');
$user                   = new XoopsUser($userID);
$username               = $user->getVar('uname');
$ri                     = smallworld_getRndImg($userID);
if ($ri != '') {
    $riUrl = XOOPS_URL . '/uploads/albums_smallworld/' . $userID . '/' . $ri;
}

$update = 'UPLIMAGE' . ' ' . $riUrl;

$upd->Insert_Update($userID, $update);
$db->handleImageEdit();
?>
