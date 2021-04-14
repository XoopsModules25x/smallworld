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

use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_index.tpl';
} elseif (((!$GLOBALS['xoopsUser'] instanceof \XoopsUser)) || Constants::HAS_ACCESS == $set['access']) {
    $GLOBALS['xoopsOption']['template_main'] = 'smallworld_publicindex.tpl';
} else {
    redirect_header(XOOPS_URL . '/user.php', Constants::REDIRECT_DELAY_MEDIUM, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/header.php';

$set = smallworld_checkPrivateOrPublic();

if (Constants::HAS_ACCESS == $set['access']) {
    $id    = ($GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->uid() : Constants::DEFAULT_UID;
    $user  = new \XoopsUser($id);  //creates Guest XOOPS user if not current XOOPS user

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = smallworld_isInspected($id);
    if ('yes' === $inspect['inspect']) {
        $helper->redirect('userprofile.php?username=' . $GLOBALS['xoopsUser']->getVar('uname'), Constants::REDIRECT_DELAY_NONE);
    }

    $GLOBALS['xoopsTpl']->assign('ownerofpage', $id);
    $GLOBALS['xoopsTpl']->assign('isadminuser', ($helper->isUserAdmin() ? 'YES' : 'NO'));

    // Create form for private settings
    $form     = new  Smallworld\Form();
    //$usersettings = $form->usersettings($id, $selected = null);
    $GLOBALS['xoopsTpl']->assign('usersetting', $form->usersettings($id));
    $username = $user->uname();
    $profile  = $helper->getHandler('SwUser')->checkIfProfile($id);
    if (Constants::PROFILE_HAS_BOTH <= $profile) {
        $profileObj = new Smallworld\Profile();
        $profileObj->showUser($id);
        $menu_startpage = "<a href='" . $helper->url('publicindex.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/highrise.png') . "'>" . _SMALLWORLD_STARTPAGE . '</a>';
        $menu_home      = "<a href='" . $helper->url('index.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile   = "<a href='" . $helper->url('userprofile.php?username=' . $username) . "'><img id='menuimg' src='" . $helper->url('assets/images/user_silhouette.png') . "'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery   = "<a href='" . $helper->url('galleryshow.php?username=' . $username) . "'><img id='menuimg' src='" . $helper->url('assets/images/picture.png') . "'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends   = "<a href='" . $helper->url('friends.php?username=' . $username) . "'><img id='menuimg' src='" . $helper->url('assets/images/group.png') . "'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';
        $wall = new Smallworld\WallUpdates();
        // Follow array here
        $followers    = smallworld_array_flatten($wall->getFollowers($id), 0);
        $updatesarray = $wall->Updates(0, $id, $followers);
        $GLOBALS['xoopsTpl']->assign(
            [
                'menu_startpage' => $menu_startpage,
                'menu_home'      => $menu_home,
                'menu_profile'   => $menu_profile,
                'menu_friends'   => $menu_friends,
                'menu_gallery'   => $menu_gallery,
            ]
        );
    } else {
        $wall         = new Smallworld\PublicWallUpdates();
        $pub          = smallworld_checkUserPubPostPerm();
        $updatesarray = $wall->Updates(0, $pub);
    }

    //Get friends invitations
    $check          = new Smallworld\User();
    $getInvitations = $GLOBALS['xoopsUser'] ? $check->getRequests($id) : 0;
    $wall->parsePubArray($updatesarray, $id);
    $GLOBALS['xoopsTpl']->assign(
        [
            'myusername'        => $username,
            'pagename'          => 'index',
            'check'             => $profile,
            'friendinvitations' => $getInvitations,
            'access'            => $set['access'],
        ]
    );
}
if (Constants::PROFILE_XOOPS_ONLY == $profile && Constants::NO_ACCESS == $set['access']) {
    $helper->redirect('register.php');
}

// if ($profile == Constants::PROFILE_XOOPS_ONLY && $set['access'] <= Constants::HAS_ACCESS) {
//     $helper->redirect('register.php');
// }

//if (Constants::PROFILE_NONE == $profile && Constants::NO_ACCESS == $set['access']) {
//    redirect_header(XOOPS_URL . "/user.php", Constants::REDIRECT_DELAY_MEDIUM, _NOPERM);
//}

require_once XOOPS_ROOT_PATH . '/footer.php';
