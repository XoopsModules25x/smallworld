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

use Xmf\Request;
use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';
require_once $helper->path('include/functions.php');

$set = smallworld_checkPrivateOrPublic();

if (($GLOBALS['xoopsUser'] instanceof \XoopsUser) && !$GLOBALS['xoopsUser']->isGuest()) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_index.tpl';
} elseif (((!$GLOBALS['xoopsUser'] instanceof \XoopsUser) || $GLOBALS['xoopsUser']->isGuest()) && 1 == $set['access']) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_publicindex.html';
} else {
    redirect_header(XOOPS_URL . '/user.php', 5, _NOPERM);
}

require_once XOOPS_ROOT_PATH . '/header.php';
if (1 == $set['access']) {
    $id    = ($GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->getVar('uid') : 0;
    $user  = new \XoopsUser($id);
    $dBase = new Smallworld\SwDatabase();

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($id);
    if ('yes' === $inspect['inspect']) {
        redirect_header('userprofile.php?username=' . $GLOBALS['xoopsUser']->getVar('uname'), 1);
    }

    $GLOBALS['xoopsTpl']->assign('ownerofpage', $id);
    $GLOBALS['xoopsTpl']->assign('isadminuser', ($helper->isUserAdmin() ? 'YES' : 'NO'));

    // Create form for private settings
    $form         = new  Smallworld\Form();
    $usersettings = $form->usersettings($id, $selected = null);
    $xoopsTpl->assign('usersetting', $usersettings);

    $username = $user->getVar('uname');
    $check    = new Smallworld\User();
    $profile  = $GLOBALS['xoopsUser'] ? $check->checkIfProfile($id) : 0;

    if ($profile >= 2) {
        $xuser = new Smallworld\Profile();
        $xuser->ShowUser($id);
        $menu_startpage = "<a href='" . $helper->url('publicindex.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/highrise.png') . "'>" . _SMALLWORLD_STARTPAGE . '</a>';
        $menu_home      = "<a href='" . $helper->url('index.php' . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile   = "<a href='" . $helper->url('userprofile.php?username=' . $username) . "'><img id='menuimg' src='" . $helper->url('assets/images/user_silhouette.png') . "'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery   = "<a href='" . $helper->url('galleryshow.php?username=' . $username) . "'><img id='menuimg' src='" . $helper->url('assets/images/picture.png') . "'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends   = "<a href='" . $helper->url('friends.php?username=' . $username) . "'><img id='menuimg' src='" . $helper->url('assets/images/group.png') . "'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';
    }

    // Things to do with wall
    $Wall = ($profile >= 2) ? new  Smallworld\WallUpdates() : new Smallworld\PublicWallUpdates();
    if ($profile < 2 && 1 == $set['access']) {
        $pub          = smallworld_checkUserPubPostPerm();
        $updatesarray = $Wall->Updates(0, $pub);
    } else {
        // Follow array here
        $followers    = Smallworld_array_flatten($Wall->getFollowers($id), 0);
        $updatesarray = $Wall->Updates(0, $id, $followers);
    }

    //Get friends invitations
    $getInvitations = $GLOBALS['xoopsUser'] ? $check->getRequests($id) : 0;
    $Wall->ParsePubArray($updatesarray, $id);

    if ($profile >= 2) {
        $GLOBALS['xoopsTpl']->assign([
            'menu_startpage' => $menu_startpage,
            'menu_home'      => $menu_home,
            'menu_profile'   => $menu_profile.
            'menu_friends'   => $menu_friends,
            'menu_gallery'   => $menu_gallery
        ]);
    }
    $GLOBALS['xoopsTpl']->assign([
        'myusername'        => $username,
        'pagename'          => 'index',
        'check'             => $profile,
        'friendinvitations' => $getInvitations,
        'access'            => $set['access']
    ]);
}
if (1 == $profile && 0 == $set['access']) {
    $helper->redirect('register.php');
}

// if ($profile == 1 && $set['access'] <= 1) {
//     $helper->redirect('register.php');
// }

//if (0 == $profile && 0 == $set['access']) {
//    redirect_header(XOOPS_URL . "/user.php", 3, _NOPERM);
//}

require_once XOOPS_ROOT_PATH . '/footer.php';
