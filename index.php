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
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/PublicWallUpdates.php';
global $xoopsUser, $xoTheme, $xoopsConfig, $xoopsLogger, $xoopsModule;

$set = smallworld_checkPrivateOrPublic();

if ($xoopsUser) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_index.tpl';
} elseif (!$xoopsUser && 1 == $set['access']) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_publicindex.html';
} else {
    redirect_header(XOOPS_URL . '/user.php', 5, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/header.php';
if (1 == $set['access']) {
    $id    = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
    $user  = new XoopsUser($id);
    $dBase = new smallworld\SmallWorldDB;

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($id);
    if ('yes' === $inspect['inspect']) {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1);
    }

    $xoopsTpl->assign('ownerofpage', $id);
    if ($xoopsUser) {
        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $xoopsTpl->assign('isadminuser', 'YES');
            //$profile = 2;
        } else {
            $xoopsTpl->assign('isadminuser', 'NO');
        }
    }

    // Create form for private settings
    $form         = new smallworld\SmallWorldForm;
    $usersettings = $form->usersettings($id, $selected = null);
    $xoopsTpl->assign('usersetting', $usersettings);

    $username = $user->getVar('uname');
    $check    = new smallworld\SmallWorldUser;
    $profile  = $xoopsUser ? $check->CheckIfProfile($id) : 0;

    if ($profile >= 2) {
        $xuser = new smallworld\SmallWorldProfile;
        $xuser->ShowUser($id);
        $menu_startpage = "<a href='" . XOOPS_URL . "/modules/smallworld/publicindex.php'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/highrise.png'>" . _SMALLWORLD_STARTPAGE . '</a>';
        $menu_home      = "<a href='" . XOOPS_URL . "/modules/smallworld/'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/house.png'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile   = "<a href='" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $username . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/user_silhouette.png'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery   = "<a href='" . XOOPS_URL . '/modules/smallworld/galleryshow.php?username=' . $username . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/picture.png'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends   = "<a href='" . XOOPS_URL . '/modules/smallworld/friends.php?username=' . $username . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/group.png'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';
    }

    // Things to do with wall
    $Wall = ($profile >= 2) ? new smallworld\WallUpdates() : new PublicWallUpdates;
    if ($profile < 2 && 1 == $set['access']) {
        $pub          = smallworld_checkUserPubPostPerm();
        $updatesarray = $Wall->Updates(0, $pub);
    } else {
        // Follow array here
        $followers    = Smallworld_array_flatten($Wall->getFollowers($id), 0);
        $updatesarray = $Wall->Updates(0, $id, $followers);
    }

    //Get friends invitations
    $getInvitations = $xoopsUser ? $check->getRequests($id) : 0;
    $Wall->ParsePubArray($updatesarray, $id);

    if ($profile >= 2) {
        $xoopsTpl->assign('menu_startpage', $menu_startpage);
        $xoopsTpl->assign('menu_home', $menu_home);
        $xoopsTpl->assign('menu_profile', $menu_profile);
        $xoopsTpl->assign('menu_friends', $menu_friends);
        $xoopsTpl->assign('menu_gallery', $menu_gallery);
    }
    $xoopsTpl->assign('myusername', $username);
    $xoopsTpl->assign('pagename', 'index');
    $xoopsTpl->assign('check', $profile);

    $xoopsTpl->assign('friendinvitations', $getInvitations);
    $xoopsTpl->assign('access', $set['access']);

    //	}
}
if (1 == $profile && 0 == $set['access']) {
    redirect_header(XOOPS_URL . '/modules/smallworld/register.php');
}

// if ($profile == 1 && $set['access'] <= 1) {
// redirect_header(XOOPS_URL . "/modules/smallworld/register.php");
// }

if (0 == $profile && 0 == $set['access']) {
    //redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
}

require_once XOOPS_ROOT_PATH . '/footer.php';
