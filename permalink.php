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

use Xmf\Request;
use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_permalink.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$GLOBALS['xoopsLogger']->activated = false;
/* error_reporting(E_ALL); */

//$set   = smallworld_checkPrivateOrPublic();
//$check = new Smallworld\User();
//$pub   = smallworld_checkUserPubPostPerm();

if (Request::hasVar('updid', 'GET') && Request::hasVar('ownerid', 'GET')) {
    $updID   = Request::getInt('updid', 0, 'GET');
    $ownerID = Request::getInt('ownerid', 0, 'GET');
} else {
    $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, _SMALLWORLD_UPDATEID_NOT_EXIST);
}

$id = 0;
$is_admin   = $helper->isUserAdmin() ? 'YES' : 'NO';
$username   = '';
if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    $id       = $GLOBALS['xoopsUser']->uid();
    $username = $GLOBALS['xoopsUser']->uname();
    /** {@internal not used}}
    if ($helper->userIsAdmin()) {
        $pub = $helper->getHandler('SwUser')->allUsers();
        //$pub = $check->allUsers();
        $GLOBALS['xoopsTpl']->assign('ownerofpage', $id); - not used
    }
    */
}

//$friend         = $check->friendcheck($id, $ownerID);
$profile        = $helper->getHandler('SwUser')->checkIfProfile($id);
$menu_startpage = "<a href='" . $helper->url('publicindex.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/highrise.png') . "'>" . _SMALLWORLD_STARTPAGE . '</a>';
$menu_home      = "<a href='" . $helper->url('index.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';

// Things to do with wall
$wall = new Smallworld\WallUpdates();

// Follow array here - not used
//$followers = smallworld_array_flatten($wall->getFollowers($id), 0);

$updatesarray = $wall->updatesPermalink($updID, $id, $ownerID);
$wall->parsePubArray($updatesarray, $id);

$GLOBALS['xoopsTpl']->assign([
    'menu_startpage' => $menu_startpage,
    'menu_home'      => $menu_home,
    'myusername'     => $username,
    'pagename'       => 'index',
    'check'          => $profile,
    'isadminuser'    => $is_admin
]);

require_once XOOPS_ROOT_PATH . '/footer.php';
