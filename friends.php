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

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_friends_template.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $id            = smallworld_isset_or(Request::getString('username', '', 'GET')); // Id of user which profile you want to see
    $yourid        = $GLOBALS['xoopsUser']->uid(); // your uid
    $Xuser         = new \XoopsUser($id);
    $Xusername     = $Xuser->getVar('uname');
    $check         = new Smallworld\User();
    //$profile       = $check->checkIfProfile($id);
    $swUserHandler = $helper->getHandler('SwUser');
    $profile       = $swUserHandler->checkIfProfile($id);
    $friends       = new Smallworld\Friends();

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = smallworld_isInspected($yourid);
    if ('yes' === $inspect['inspect']) {
        $helper->redirect('userprofile.php?username=' . $GLOBALS['xoopsUser']->getVar('uname'), 1);
    }

    if ($profile >= Constants::PROFILE_HAS_BOTH) {
        $user = new Smallworld\Profile();
        $user->showUser($id);
        $username  = $GLOBALS['xoopsUser']->getVar('uname'); //Myusername
        $tpl_admin = 'NO';
        if ($helper->isUserAdmin()) {
            $tpl_admin = 'YES';
            $GLOBALS['xoopsTpl']->assign('ownerofpage', $id);
        }
        $GLOBALS['xoopsTpl']->assign('isadminuser', $tpl_admin);

        // Check status for relationship
        $fr         = $check->friendcheck($yourid, $id);
        $tpl_friend = $helper->isUserAdmin() ? 'yes' : 'no';
        switch ($fr[0]) {
            case 0:
                $friendship_text = _SMALLWORLD_JSON_ADDFR_TEXT;
                $GLOBALS['xoopsTpl']->assign('isuserafriend', $tpl_friend);
                break;
            case 1:
                $friendship_text = _SMALLWORLD_JSON_CANCELFR_TEXT;
                $GLOBALS['xoopsTpl']->assign('isuserafriend', $tpl_friend);
                break;
            case 2:
                $friendship_text = _SMALLWORLD_JSON_REMOVEFR_TEXT;
                $GLOBALS['xoopsTpl']->assign('isuserafriend', 'yes');
                break;
            default:
                //@todo - figure out what to do here if it ever happens
        }

        // Check status for follow
        if ($yourid == $id) {
            $following_text = _SMALLWORLD_JSON_FLYES_TEXT;
        } else {
            $fl             = $check->following_or($yourid, $id);
            $following_text = (1 == $fl[0]) ? _SMALLWORLD_JSON_FLNO_TEXT : _SMALLWORLD_JSON_FLYES_TEXT;
        }

        // Get requests
        $getInvitations = $check->getRequests($yourid);

        // Things to do with wall
        $wall         = new Smallworld\WallUpdates();
        $myavatar     = $wall->Gravatar($id);
        $myavatarlink = smallworld_getAvatarLink($id, $myavatar);

        // Get your followers array
        $followers = $wall->getFollowers($id);

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
        foreach ($pending_array as $data) {
            $fp['friend_id']       = $data['you'];
            $fp['friendname']      = $swUserHandler->getName($data['you']);
            $fp['avatar']          = $wall->Gravatar($data['you']);
            $fp['avatar_link']     = smallworld_getAvatarLink($data['you'], $fp['avatar']);
            $fp['avatar_size']     = smallworld_getImageSize(80, 100, $fp['avatar_link']);
            $fp['avatar_highwide'] = smallworld_imageResize($fp['avatar_size'][0], $fp['avatar_size'][1], 50);
            $GLOBALS['xoopsTpl']->append('pendingfriends', $fp);
        }

        foreach ($friends_array as $data) {
            $ff['friend_id']       = $data['you'];
            $ff['friendname']      = $swUserHandler->getName($data['you']);
            $ff['avatar']          = $wall->Gravatar($data['you']);
            $ff['avatar_link']     = smallworld_getAvatarLink($data['you'], $ff['avatar']);
            $ff['avatar_size']     = smallworld_getImageSize(80, 100, $ff['avatar_link']);
            $ff['avatar_highwide'] = smallworld_imageResize($ff['avatar_size'][0], $ff['avatar_size'][1], 50);
            $GLOBALS['xoopsTpl']->append('verifiedfriends', $ff);
        }

        foreach ($following_array as $data) {
            $fy['friend_id']       = $data['you'];
            $fy['friendname']      = $swUserHandler->getName($data['you']);
            $fy['avatar']          = $wall->Gravatar($data['you']);
            $fy['avatar_link']     = smallworld_getAvatarLink($data['you'], $fy['avatar']);
            $fy['avatar_size']     = smallworld_getImageSize(80, 100, $fy['avatar_link']);
            $fy['avatar_highwide'] = smallworld_imageResize($fy['avatar_size'][0], $fy['avatar_size'][1], 50);
            $GLOBALS['xoopsTpl']->append('followingyou', $fy);
        }

        foreach ($followingme_array as $data) {
            $fm['friend_id']       = $data['me'];
            $fm['friendname']      = $swUserHandler->getName($data['me']);
            $fm['avatar']          = $wall->Gravatar($data['me']);
            $fm['avatar_link']     = smallworld_getAvatarLink($data['me'], $fm['avatar']);
            $fm['avatar_size']     = smallworld_getImageSize(80, 100, $fm['avatar_link']);
            $fm['avatar_highwide'] = smallworld_imageResize($fm['avatar_size'][0], $fm['avatar_size'][1], 50);
            $GLOBALS['xoopsTpl']->append('followingme', $fm);
        }

        // Create form for private settings
        $form         = new Smallworld\Form();
        $usersettings = $form->usersettings($yourid, $selected = null);
        $GLOBALS['xoopsTpl']->assign('usersetting', $usersettings);

        // Get usermenu to template
        $menu_home    = "<a href='" . $helper->url('index.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile = "<a href='" . $helper->url('userprofile.php?username=' . $Xusername) . "'><img id='menuimg' src='" . $helper->url('assets/images/user_silhouette.png') . "'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery = "<a href='" . $helper->url('galleryshow.php?username=' . $Xusername) . "'><img id='menuimg' src='" . $helper->url('assets/images/picture.png') . "'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends = "<a href='" . $helper->url('friends.php?username=' . $Xusername) . "'><img id='menuimg' src='" . $helper->url('assets/images/group.png') . "'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';
        $menu_xim_js  = "javascript:chatWith('" . $id . "','" . $Xusername . "')";
        $menu_ximme   = "<a href='javascript:void(0);' onClick=" . $menu_xim_js . "><img height='10px' width='10px' src='" . $helper->url('assets/images/messenger.png') . "'>" . _SMALLWORLD_XIMUSER . $Xusername . '</a>';

        // Check for folder xim to add messenger user to menu items
        $hasxim = smallworld_checkForXim();
        if (true === $hasxim) {
            $GLOBALS['xoopsTpl']->assign('sendxim', 'YES');
            if (2 == $fr[0]) {
                if ($yourid != $id) {
                    $GLOBALS['xoopsTpl']->assign('menu_xim', $menu_ximme);
                }
            }
        }

        $GLOBALS['xoopsTpl']->assign([
            'menu_home'           => $menu_home,
            'menu_profile'        => $menu_profile,
            'menu_friends'        => $menu_friends,
            'menu_gallery'        => $menu_gallery,
            'check'               => $profile,
            'friendID'            => $id,
            'myUid'               => $yourid,
            'friendship_text'     => $friendship_text,
            'followfriend_text'   => $following_text,
            'friendinvitations'   => $getInvitations,
            'myavatar'            => $myavatar,
            'myavatarlink'        => $myavatarlink,
            'myusername'          => $username,
            'username'            => $Xusername,
            'nouserspending'      => _SMALLWORLD_NOUSERS,
            'nousersfriends'      => _SMALLWORLD_NOUSERS,
            'nousersfollowingyou' => _SMALLWORLD_NOUSERS,
            'nousersfollowingme'  => _SMALLWORLD_NOUSERS
        ]);
    } else {
        $check->chkUser();
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', Constants::REDIRECT_DELAY_SHORT, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
