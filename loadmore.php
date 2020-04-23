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

require_once XOOPS_ROOT_PATH . '/class/template.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

$set = smallworld_checkPrivateOrPublic();
$pub = smallworld_checkUserPubPostPerm();
$hm  = smallworld_GetModuleOption('msgtoshow');

$last = $GLOBALS['xoopsDB']->escape($_POST['last']);
$page = $GLOBALS['xoopsDB']->escape($_POST['page']);

$GLOBALS['xoopsLogger']->activated = false;
/* error_reporting(E_ALL); */

//$GLOBALS['xoopsTpl'] = new \XoopsTpl();
//$check    = new Smallworld\User();
$id       = Constants::DEFAULT_UID;
$username = '';
$profile  = Constants::PROFILE_NONE;
$isAdmin  = $helper->isUserAdmin();
$tplAdmin= $isAdmin ? 'YES' : 'NO';

/**@var \XoopsModules\Smallworld\SwUserHandler $swUserHandler */
$swUserHandler = $helper->getHandler('SwUser');

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $id       = $GLOBALS['xoopsUser']->uid();
    $username = $GLOBALS['xoopsUser']->uname();
    //$profile  = $check->checkIfProfile($id);
    $profile  = $sWUserHandler->checkIfProfile($id);
    if ($isAdmin) {
        $pub = $swUserHandler->allUsers();
        /*
        $check = new Smallworld\User();
        $pub   = $check->allUsers();
        */
    }
}

$userid = Request::getInt('userid', $id, 'POST');
if (Constants::DEFAULT_UID < $userid && $userid !== $id) {
    $xUser = new \XoopsUser($userid);
    if ($xUser && ($xUser instanceof \XoopsUser)) {
        $id       = $userid;
        $username = $xUser->uname();
        //$profile  = $check->checkIfProfile($id);
        $profile  = $swUserHandler->checkIfProfile($id);
    }
}

if (Constants::DEFAULT_UID >= $id || ('publicindex' === $page) && (Constants::HAS_ACCESS == $set['access'])) {
    $wall = new Smallworld\PublicWallUpdates();
} else {
    $wall = new Smallworld\WallUpdates();
}

if (Constants::DEFAULT_UID >= $id && Constants::HAS_ACCESS == $set['access']) {
    $followers = $pub;
} else {
    $followers = smallworld_array_flatten($wall->getFollowers($id), 0);
}

$last = Request::getString('last', 'a', 'POST');
switch ($page) {
    case ('index'):
        $updatesArray = (Constants::DEFAULT_UID < $id) ? $wall->Updates($last, $id, $followers) : $wall->Updates($last, $followers);
        break;
    case ('profile'):
        $updatesArray = (Constants::DEFAULT_UID < $id) ? $wall->Updates($last, $userid, $userid) : $wall->Updates($last, $userid);
        break;
    case ('publicindex'):
        $updatesArray = $wall->Updates($last, $followers);
        break;
}

$wall->parsePubArray($updatesArray, $id);

$GLOBALS['xoopsTpl']->assign([
    'sCountResp'  => count($updatesArray),
    'msgtoshow'   => $hm,
    'myusername'  => $username,
    'pagename'    => $page,
    'isadminuser' => $tplAdmin
]);

$tplFileName = (Constants::DEFAULT_UID < $id) ? $helper->path('templates/getmore.tpl') : $helper->path('templates/getmorepublic.tpl');
$GLOBALS['xoopsTpl']->display($tplFileName);

