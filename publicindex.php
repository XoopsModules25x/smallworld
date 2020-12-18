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
 * @package      \XoopsModules\SmallWorld
 * @copyright    The XOOPS Project (https://xoops.org)
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

$page = basename(__FILE__, '.php');

// @ todo - figure out why the test for $page is here... $page will always equal 'publicindex'
if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser) && 'publicindex' !== $page) {
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
$swDB     = new Smallworld\SwDatabase();
$id       = Constants::DEFAULT_UID;
$username = '';
$profile  = Constants::PROFILE_NONE;

if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    $id       = $GLOBALS['xoopsUser']->uid();
    $username = $GLOBALS['xoopsUser']->uname();
    //$check    = new Smallworld\User();
    //$profile  = $check->checkIfProfile($id);
    $profile  = $helper->getHandler('SwUser')->checkIfProfile($id);
}

$pub     = smallworld_checkUserPubPostPerm();
$wall    = new Smallworld\PublicWallUpdates();
$updates = $wall->Updates(0, $pub);

if ($id > 0) {
    $GLOBALS['xoopsTpl']->assign('ownerofpage', $id);
    $GLOBALS['xoopsTpl']->assign('myusername', $username);
}

$tplAdmin = $helper->isUserAdmin() ? 'YES' : 'NO';

// Create form for private settings
$form         = new Smallworld\Form();
$usersettings = $form->usersettings($id, $selected = null);
$GLOBALS['xoopsTpl']->assign('usersetting', $usersettings);

//$xuser = new Smallworld\Profile();

$menu_home     = "<a href='" . $helper->url('/') . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';
$menu_register = ($profile < Constants::PROFILE_HAS_BOTH) ? "<a href='" . $helper->url('register.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/join.jpg') . "'>" . _MB_SYSTEM_RNOW . '</a>' : '';

$updatesarray = $wall->Updates(0, $pub);
$wall->parsePubArray($updatesarray, $id);

$GLOBALS['xoopsTpl']->assign(
    [
        'menu_home'     => $menu_home,
        'menu_register' => $menu_register,
        'pagename'      => 'publicindex',
        'check'         => $profile,
        'access'        => $set['access'],
        'isadminuser'   => $tplAdmin
    ]
);

require_once XOOPS_ROOT_PATH . '/footer.php';
