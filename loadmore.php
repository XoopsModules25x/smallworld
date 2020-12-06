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

require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/PublicWallUpdates.php';
$set = smallworld_checkPrivateOrPublic();
$pub = smallworld_checkUserPubPostPerm();
$hm  = smallworld_GetModuleOption('msgtoshow');

$last = $GLOBALS['xoopsDB']->escape($_POST['last']);
$page = $GLOBALS['xoopsDB']->escape($_POST['page']);

global $xoopsUser, $xoTheme, $xoopsTpl, $xoopsLogger;
$xoopsLogger->activated = false;
/* error_reporting(E_ALL); */
$xoopsTpl = new XoopsTpl();
$id       = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
if ($id <= 0 || 'publicindex' === $page && $set['access'] = 1) {
    $Wall = new smallworld\PublicWallUpdates();
} else {
    $Wall = new smallworld\WallUpdates();
}
if (isset($_POST['userid'])) {
    $userid = (int)$_POST['userid'];
} else {
    $userid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
}
$Xuser    = ($id > 0) ? new XoopsUser($id) : 0;
$username = ($id > 0) ? $Xuser->getVar('uname') : '';
$dBase    = new smallworld\SmallWorldDB;
$check    = new smallworld\SmallWorldUser;
$profile  = $xoopsUser ? $check->CheckIfProfile($id) : 0;

if ($id > 0) {
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $pub = $check->allUsers();
        $xoopsTpl->assign('isadminuser', 'YES');
    }
} else {
    $xoopsTpl->assign('isadminuser', 'NO');
    $pub = smallworld_checkUserPubPostPerm();
}

if ($id <= 0 && 1 == $set['access']) {
    //$pub = $check->allUsers();
    $followers = $pub;
} elseif ($id > 0 && 1 == $set['access'] && 'publicindex' === $page) {
    //$pub = $check->allUsers();
    $followers = $pub;
} else {
    $followers = Smallworld_array_flatten($Wall->getFollowers($id), 0);
}

if ('index' === $page) {
    $updatesarray = ($id > 0) ? $Wall->Updates($_POST['last'], $id, $followers) : $Wall->Updates($_POST['last'], $followers);
} elseif ('profile' === $page) {
    $updatesarray = ($id > 0) ? $Wall->Updates($_POST['last'], $userid, $userid) : $Wall->Updates($_POST['last'], $userid);
} elseif ('publicindex' === $page) {
    $updatesarray = $Wall->Updates($_POST['last'], $followers);
}

$Wall->ParsePubArray($updatesarray, $id);

$xoopsTpl->assign('sCountResp', count($updatesarray));
$xoopsTpl->assign('msgtoshow', $hm);
$xoopsTpl->assign('myusername', $username);
$xoopsTpl->assign('pagename', $page);

if ($id > 0) {
    $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/getmore.tpl');
} else {
    $xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/getmorepublic.tpl');
}
