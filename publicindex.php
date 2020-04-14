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
$page = basename($_SERVER['PHP_SELF'], '.php');

if ($xoopsUser && 'publicindex' !== $page) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_index.tpl';
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_publicindex.tpl';
}
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/PublicWallUpdates.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
global $xoopsUser, $xoTheme, $xoopsConfig, $xoopsLogger;
//$xoopsLogger->activated = true;
//error_reporting(E_ALL);

$set   = smallworld_checkPrivateOrPublic();
$dBase = new smallworld\SmallWorldDB;
$check = new smallworld\SmallWorldUser;

$id       = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$username = $xoopsUser ? $xoopsUser->getVar('uname') : '';
$profile  = $xoopsUser ? $check->CheckIfProfile($id) : 0;

$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname('smallworld');
$configHandler = xoops_getHandler('config');
$moduleConfig  = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
$pub            = smallworld_checkUserPubPostPerm();
$wall           = new smallworld\PublicWallUpdates;
$updates        = $wall->Updates(0, $pub);

if ($id > 0) {
    $xoopsTpl->assign('ownerofpage', $id);
    $xoopsTpl->assign('myusername', $username);
}

if ($xoopsUser) {
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $pub = $check->allUsers();
        $xoopsTpl->assign('isadminuser', 'YES');
    }
} else {
    $xoopsTpl->assign('isadminuser', 'NO');
}

// Create form for private settings
$form         = new smallworld\SmallWorldForm;
$usersettings = $form->usersettings($id, $selected = null);
$xoopsTpl->assign('usersetting', $usersettings);

$xuser = new smallworld\SmallWorldProfile;

$menu_home     = "<a href='" . XOOPS_URL . "/modules/smallworld/'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/house.png'>" . _SMALLWORLD_HOME . '</a>';
$menu_register = ($profile < 2) ? "<a href='" . XOOPS_URL . "/modules/smallworld/register.php'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/join.jpg'>" . _MB_SYSTEM_RNOW . '</a>' : '';

$updatesarray = $wall->Updates(0, $pub);
$wall->ParsePubArray($updatesarray, $id);

$xoopsTpl->assign('menu_home', $menu_home);
$xoopsTpl->assign('menu_register', $menu_register);
$xoopsTpl->assign('pagename', 'publicindex');
$xoopsTpl->assign('check', $profile);
$xoopsTpl->assign('access', $set['access']);

require_once XOOPS_ROOT_PATH . '/footer.php';
