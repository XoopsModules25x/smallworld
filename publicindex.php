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
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @package      \XoopsModules\SmallWorld
 * @since        1.0
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 */

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

require_once dirname(dirname(__DIR__)) . '/mainfile.php';
$page = basename(__FILE__, '.php');

if ($GLOBALS['xoopsUser'] && 'publicindex' !== $page) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_index.tpl';
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_publicindex.tpl';
}
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
//$GLOBALS['xoopsLogger']->activated = true;
//error_reporting(E_ALL);

$set      = smallworld_checkPrivateOrPublic();
$dBase    = new Smallworld\SwDatabase();
$check    = new Smallworld\User();
$id       = 0;
$username = '';
$profile  = 0;

if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    $id       = $GLOBALS['xoopsUser']->uid();
    $username = $GLOBALS['xoopsUser']->uname();
    $profile  = $check->checkIfProfile($id);
}

$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname('smallworld');
$configHandler = xoops_getHandler('config');
$moduleConfig  = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
$pub           = smallworld_checkUserPubPostPerm();
$wall          = new Smallworld\PublicWallUpdates();
$updates       = $wall->Updates(0, $pub);

if ($id > 0) {
    $GLOBALS['xoopsTpl']->assign('ownerofpage', $id);
    $GLOBALS['xoopsTpl']->assign('myusername', $username);
}

$GLOBALS['xoopsTpl']->assign('isadminuser', $helper->isUserAdmin() ? 'YES' : 'NO');

// Create form for private settings
$form         = new Smallworld\Form();
$usersettings = $form->usersettings($id, $selected = null);
$GLOBALS['xoopsTpl']->assign('usersetting', $usersettings);

$xuser = new Smallworld\Profile();

$menu_home     = "<a href='" . $helper->url('/') . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';
$menu_register = ($profile < 2) ? "<a href='" . $helper->url('register.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/join.jpg') . "'>" . _MB_SYSTEM_RNOW . '</a>' : '';

$updatesarray = $wall->Updates(0, $pub);
$wall->parsePubArray($updatesarray, $id);

$GLOBALS['xoopsTpl']->assign(
    [
        'menu_home'     => $menu_home,
        'menu_register' => $menu_register,
        'pagename'      => 'publicindex',
        'check'         => $profile,
        'access'        => $set['access'],
    ]
);

require_once XOOPS_ROOT_PATH . '/footer.php';
