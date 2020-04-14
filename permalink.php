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
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_permalink.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoTheme, $xoopsLogger, $xoopsModule;

$xoopsLogger->activated = false;
/* error_reporting(E_ALL); */

$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname('smallworld');
$configHandler = xoops_getHandler('config');
$moduleConfig  = $configHandler->getConfigsByCat(0, $module->getVar('mid'));

$set   = smallworld_checkPrivateOrPublic();
$check = new smallworld\SmallWorldUser;
$pub   = smallworld_checkUserPubPostPerm();
$dBase = new smallworld\SmallWorldDB;

if (isset($_GET['updid']) and isset($_GET['ownerid'])) {
    $updID   = $_GET['updid'];
    $ownerID = $_GET['ownerid'];
} else {
    redirect_header(XOOPS_URL . '/modules/smallworld/index.php', 5, _SMALLWORLD_UPDATEID_NOT_EXIST);
}

$id   = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$user = $xoopsUser ? new XoopsUser($id) : 0;

if ($xoopsUser) {
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $pub = $check->allUsers();
        $xoopsTpl->assign('isadminuser', 'YES');
        $xoopsTpl->assign('ownerofpage', $id);
    } else {
        $xoopsTpl->assign('isadminuser', 'NO');
    }
}
$username = $xoopsUser ? $user->getVar('uname') : '';

$friend = $check->friendcheck($id, $ownerID);

$profile        = $check->CheckIfProfile($id);
$menu_startpage = "<a href='" . XOOPS_URL . "/modules/smallworld/publicindex.php'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/highrise.png'>" . _SMALLWORLD_STARTPAGE . '</a>';
$menu_home      = "<a href='" . XOOPS_URL . "/modules/smallworld/'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/house.png'>" . _SMALLWORLD_HOME . '</a>';

// Things to do with wall
$Wall = new smallworld\WallUpdates();

// Follow array here
$followers = Smallworld_array_flatten($Wall->getFollowers($id), 0);

$updatesarray = $Wall->UpdatesPermalink($updID, $id, $ownerID);
$Wall->ParsePubArray($updatesarray, $id);

$xoopsTpl->assign('menu_startpage', $menu_startpage);
$xoopsTpl->assign('menu_home', $menu_home);
$xoopsTpl->assign('myusername', $username);
$xoopsTpl->assign('pagename', 'index');
$xoopsTpl->assign('check', $profile);

require_once XOOPS_ROOT_PATH . '/footer.php';
