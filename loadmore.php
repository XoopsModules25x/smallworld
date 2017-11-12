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
 * @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/loadmore.php $
 * @Last       committed:        $Revision: 12114 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2013-10-01 19:11:18 +0200 (ti, 01 okt 2013) $
 * @ID         :                    $Id: loadmore.php 12114 2013-10-01 17:11:18Z djculex $
 **/

include '../../mainfile.php';
include_once XOOPS_ROOT_PATH . '/class/template.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/class/publicWall.php';
$set = smallworld_checkPrivateOrPublic();
$pub = smallworld_checkUserPubPostPerm();
$hm  = smallworld_GetModuleOption('msgtoshow');

$last = mysql_real_escape_string($_POST['last']);
$page = mysql_real_escape_string($_POST['page']);

global $xoopsUser, $xoTheme, $xoopsTpl, $xoopsLogger;
$xoopsLogger->activated = false;
/* error_reporting(E_ALL); */
$xoopsTpl = new XoopsTpl();
$id       = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
if ($id <= 0 || $page == 'publicindex' && $set['access'] = 1) {
    $Wall = new Public_Wall_Updates();
} else {
    $Wall = new Wall_Updates();
}
if (isset($_POST['userid'])) {
    $userid = intval($_POST['userid']);
} else {
    $userid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
}
$Xuser    = ($id > 0) ? new XoopsUser($id) : 0;
$username = ($id > 0) ? $Xuser->getVar('uname') : '';
$dBase    = new SmallWorldDB;
$check    = new SmallWorldUser;
$profile  = $xoopsUser ? $check->checkIfProfile($id) : 0;

if ($id > 0) {
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $pub = $check->allUsers();
        $xoopsTpl->assign('isadminuser', 'YES');
    }
} else {
    $xoopsTpl->assign('isadminuser', 'NO');
    $pub = smallworld_checkUserPubPostPerm();
}

if ($id <= 0 && $set['access'] == 1) {
    //$pub = $check->allUsers();
    $followers = $pub;
} elseif ($id > 0 && $set['access'] == 1 && $page == 'publicindex') {
    //$pub = $check->allUsers();
    $followers = $pub;
} else {
    $followers = Smallworld_array_flatten($Wall->getFollowers($id), 0);
}

if ($page == 'index') {
    $updatesarray = ($id > 0) ? $Wall->Updates($_POST['last'], $id, $followers) : $Wall->Updates($_POST['last'], $followers);
} elseif ($page == 'profile') {
    $updatesarray = ($id > 0) ? $Wall->Updates($_POST['last'], $userid, $userid) : $Wall->Updates($_POST['last'], $userid);
} elseif ($page == 'publicindex') {
    $updatesarray = $Wall->Updates($_POST['last'], $followers);
}

$Wall->ParsePubArray($updatesarray, $id);

$xoopsTpl->assign('sCountResp', count($updatesarray));
$xoopsTpl->assign('msgtoshow', $hm);
$xoopsTpl->assign('myusername', $username);
$xoopsTpl->assign('pagename', $page);

if ($id > 0) {
    $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/getmore.html');
} else {
    $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/getmorepublic.html');
}
?>
