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

//require_once __DIR__ . '/../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_friends_template.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
global $xoopsUser;

if ($xoopsUser) {
    $id        = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
    $yourid    = $xoopsUser->getVar('uid'); // your uid
    $Xuser     = new XoopsUser($id);
    $Xusername = $Xuser->getVar('uname');
    $check     = new smallworld\SmallWorldUser;
    $profile   = $check->CheckIfProfile($id);
    $friends   = new smallworld\SmallWorldFriends;

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($yourid);
    if ('yes' === $inspect['inspect']) {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1);
    }

    if ($profile >= 2) {
        $user = new smallworld\SmallWorldProfile;
        $user->ShowUser($id);
        $username = $xoopsUser->getVar('uname'); //Myusername
        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $xoopsTpl->assign('isadminuser', 'YES');
            $xoopsTpl->assign('ownerofpage', $id);
        }

        // Check status for relationship
        $fr = $check->friendcheck($yourid, $id);
        if (0 == $fr[0]) {
            $friendship_text = _SMALLWORLD_JSON_ADDFR_TEXT;
            if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                $xoopsTpl->assign('isuserafriend', 'yes');
            } else {
                $xoopsTpl->assign('isuserafriend', 'no');
            }
        }

        if (1 == $fr[0]) {
            $friendship_text = _SMALLWORLD_JSON_CANCELFR_TEXT;
            if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                $xoopsTpl->assign('isuserafriend', 'yes');
            } else {
                $xoopsTpl->assign('isuserafriend', 'no');
            }
        }

        if (2 == $fr[0]) {
            $friendship_text = _SMALLWORLD_JSON_REMOVEFR_TEXT;
            $xoopsTpl->assign('isuserafriend', 'yes');
        }

        // Check status for follow
        $fl = $check->following_or($yourid, $id);
        if ($yourid == $id) {
            $following_text = _SMALLWORLD_JSON_FLYES_TEXT;
        }
        if (0 == $fl[0]) {
            $following_text = _SMALLWORLD_JSON_FLYES_TEXT;
        }
        if (1 == $fl[0]) {
            $following_text = _SMALLWORLD_JSON_FLNO_TEXT;
        }

        // Get requests
        $getInvitations = $check->getRequests($yourid);

        // Things to do with wall
        $Wall         = new smallworld\WallUpdates();
        $myavatar     = $Wall->Gravatar($id);
        $myavatarlink = smallworld_getAvatarLink($id, $myavatar);

        // Get your followers array
        $followers = $Wall->getFollowers($id);

        // Pendings array here
        $pending_array = $friends->getFriends($id, 'pending');

        // Friends array here
        $friends_array = $friends->getFriends($id, 'friends');

        // Pendings array here
        $following_array = $friends->getFriends($id, 'following');

        // Users Following you
        $followingme_array = $friends->getFriends($id, 'followingme');

        //Srinivas Tamada http://9lessons.info
        //Loading Comments link with load_updates.php
        if (!empty($pending_array)) {
            foreach ($pending_array as $data) {
                $fp['friend_id']       = $data['you'];
                $fp['friendname']      = Smallworld_getName($data['you']);
                $fp['avatar']          = $Wall->Gravatar($data['you']);
                $fp['avatar_link']     = smallworld_getAvatarLink($data['you'], $fp['avatar']);
                $fp['avatar_size']     = smallworld_getImageSize(80, 100, $fp['avatar_link']);
                $fp['avatar_highwide'] = smallworld_imageResize($fp['avatar_size'][0], $fp['avatar_size'][1], 50);
                $xoopsTpl->append('pendingfriends', $fp);
            }
        } else {
            $xoopsTpl->append('nouserspending', _SMALLWORLD_NOUSERS);
        }

        if (!empty($friends_array)) {
            foreach ($friends_array as $data) {
                $ff['friend_id']       = $data['you'];
                $ff['friendname']      = Smallworld_getName($data['you']);
                $ff['avatar']          = $Wall->Gravatar($data['you']);
                $ff['avatar_link']     = smallworld_getAvatarLink($data['you'], $ff['avatar']);
                $ff['avatar_size']     = smallworld_getImageSize(80, 100, $ff['avatar_link']);
                $ff['avatar_highwide'] = smallworld_imageResize($ff['avatar_size'][0], $ff['avatar_size'][1], 50);
                $xoopsTpl->append('verifiedfriends', $ff);
            }
        } else {
            $xoopsTpl->append('nousersfriends', _SMALLWORLD_NOUSERS);
        }

        if (!empty($following_array)) {
            foreach ($following_array as $data) {
                $fy['friend_id']       = $data['you'];
                $fy['friendname']      = Smallworld_getName($data['you']);
                $fy['avatar']          = $Wall->Gravatar($data['you']);
                $fy['avatar_link']     = smallworld_getAvatarLink($data['you'], $fy['avatar']);
                $fy['avatar_size']     = smallworld_getImageSize(80, 100, $fy['avatar_link']);
                $fy['avatar_highwide'] = smallworld_imageResize($fy['avatar_size'][0], $fy['avatar_size'][1], 50);
                $xoopsTpl->append('followingyou', $fy);
            }
        } else {
            $xoopsTpl->append('nousersfollowingyou', _SMALLWORLD_NOUSERS);
        }

        if (!empty($followingme_array)) {
            foreach ($followingme_array as $data) {
                $fm['friend_id']       = $data['me'];
                $fm['friendname']      = Smallworld_getName($data['me']);
                $fm['avatar']          = $Wall->Gravatar($data['me']);
                $fm['avatar_link']     = smallworld_getAvatarLink($data['me'], $fm['avatar']);
                $fm['avatar_size']     = smallworld_getImageSize(80, 100, $fm['avatar_link']);
                $fm['avatar_highwide'] = smallworld_imageResize($fm['avatar_size'][0], $fm['avatar_size'][1], 50);
                $xoopsTpl->append('followingme', $fm);
            }
        } else {
            $xoopsTpl->append('nousersfollowingme', _SMALLWORLD_NOUSERS);
        }

        // Create form for private settings
        $form         = new smallworld\SmallWorldForm;
        $usersettings = $form->usersettings($yourid, $selected = null);
        $xoopsTpl->assign('usersetting', $usersettings);

        // Get usermenu to template
        $menu_home    = "<a href='" . XOOPS_URL . "/modules/smallworld/'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/house.png'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile = "<a href='" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $Xusername . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/user_silhouette.png'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery = "<a href='" . XOOPS_URL . '/modules/smallworld/galleryshow.php?username=' . $Xusername . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/picture.png'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends = "<a href='" . XOOPS_URL . '/modules/smallworld/friends.php?username=' . $Xusername . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/group.png'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';
        $menu_xim_js  = "javascript:chatWith('" . $id . "','" . $Xusername . "')";
        $menu_ximme   = "<a href='javascript:void(0);' onClick=" . $menu_xim_js . "><img height='10px' width='10px' src='" . XOOPS_URL . "/modules/smallworld/assets/images/messenger.png'>" . _SMALLWORLD_XIMUSER . $Xusername . '</a>';

        // Check for folder xim to add messenger user to menu items
        $hasxim = smallworld_checkForXim();
        if (true === $hasxim) {
            $xoopsTpl->assign('sendxim', 'YES');
            if (2 == $fr[0]) {
                if ($yourid != $id) {
                    $xoopsTpl->assign('menu_xim', $menu_ximme);
                }
            }
        }

        $xoopsTpl->assign('menu_home', $menu_home);
        $xoopsTpl->assign('menu_profile', $menu_profile);
        $xoopsTpl->assign('menu_friends', $menu_friends);
        $xoopsTpl->assign('menu_gallery', $menu_gallery);
        $xoopsTpl->assign('check', $profile);
        $xoopsTpl->assign('friendID', $id);
        $xoopsTpl->assign('myUid', $yourid);
        $xoopsTpl->assign('friendship_text', $friendship_text);
        $xoopsTpl->assign('followfriend_text', $following_text);
        $xoopsTpl->assign('friendinvitations', $getInvitations);
        $xoopsTpl->assign('myavatar', $myavatar);
        $xoopsTpl->assign('myavatarlink', $myavatarlink);
        $xoopsTpl->assign('myusername', $username);
        $xoopsTpl->assign('username', $Xusername);
    } else {
        $check->chkUser();
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
