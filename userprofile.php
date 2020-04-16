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

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_template.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

$GLOBALS['xoopsLogger']->activated = false;
//error_reporting(E_ALL);

if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    SmallworldDeleteOldInspects();
    $id     = smallworld_isset_or(addslashes(Request::getString('username', '', 'GET'))); // Id of user which profile you want to see
    $id     = (int)$id;
    $yourid = $GLOBALS['xoopsUser']->uid(); // this user's uid
    $Xuser      = new \XoopsUser($id);
    $Xusername  = (($Xuser instanceof \XoopsUser) && !$Xuser->isNew() && !$Xuser->isGuest()) ? $Xuser->getVar('uname') : '';
    $check      = new Smallworld\User();
    $profile    = $helper->getHandler('SwUser')->checkIfProfile($yourid);
    $userNumMsg = smallworld_countUserWallMsges($id);
    $fr[0]      = '';
    $fl[0]      = '';

    $GLOBALS['xoopsTpl']->assign('isadminuser', $helper->isUserAdmin() ? 'YES' : 'NO');

    if ($profile >= Constants::PROFILE_HAS_BOTH) {
        $user  = new Smallworld\Profile();
        //$swDB = new Smallworld\SwDatabase();
        $user->showUser($id);
        $username  = $GLOBALS['xoopsUser']->getVar('uname'); //Myusername
        $inspected = smallworld_isInspected($id);
        $GLOBALS['xoopsTpl']->assign('inspect', $inspected['inspect']);
        if ('no' !== $inspected['inspect']) {
            $GLOBALS['xoopsTpl']->assign('inspecttime', $inspected['totaltime']);
        }
        $GLOBALS['xoopsTpl']->assign('ownerofpage', $id);

        // Check status for relationship
        $fr = $check->friendcheck($yourid, $id);
        switch ($fr[0]) {
            case 0:
                $friendship_text = _SMALLWORLD_JSON_ADDFR_TEXT;
                $GLOBALS['xoopsTpl']->assign('isuserafriend', $helper->isUserAdmin() ? 'yes' : 'no');
                break;
            case 1:
                $friendship_text = _SMALLWORLD_JSON_CANCELFR_TEXT;
                $GLOBALS['xoopsTpl']->assign('isuserafriend', $helper->isUserAdmin() ? 'yes' : 'no');
                break;
            case 2:
                $friendship_text = _SMALLWORLD_JSON_REMOVEFR_TEXT;
                $GLOBALS['xoopsTpl']->assign('isuserafriend', 'yes');
                break;
            default:
                $friendship_text = '';
                $GLOBALS['xoopsTpl']->assign('isuserafriend', 'no');
                break;
        }

        // Check status for follow
        $fl = $check->following_or($yourid, $id);
        if ($yourid == $id || (0 == $fl[0])) {
            $following_text = _SMALLWORLD_JSON_FLYES_TEXT;
        } elseif (1 == $fl[0]) {
            $following_text = _SMALLWORLD_JSON_FLNO_TEXT;
        }

        // Get requests
        $getInvitations = $check->getRequests($yourid);

        // Things to do with wall
        $wall = new Smallworld\WallUpdates();

        $visitorAvatar          = $wall->Gravatar($yourid);
        $visitorAvatarlink      = smallworld_getAvatarLink($yourid, $visitorAvatar);
        $visitorAvatar_size     = smallworld_getImageSize(80, 100, $visitorAvatarlink);
        $visitorAvatar_highwide = smallworld_imageResize($visitorAvatar_size[0], $visitorAvatar_size[1], 35);

        // Follow array here
        $followers    = $wall->getFollowers($id);
        $updatesarray = $wall->Updates(0, $id, $followers);
        $wall->parsePubArray($updatesarray, $id);

        // Create form for private settings
        $form         = new Smallworld\Form();
        $usersettings = $form->usersettings($yourid, $selected = null);
        $GLOBALS['xoopsTpl']->assign('usersetting', $usersettings);

        // Get usermenu to template
        $menu_startpage = "<a href='" . $helper->url('publicindex.php') . "'><img id='menuimg' src='" . $helper->url('assets/images/highrise.png') . "'>" . _SMALLWORLD_STARTPAGE . '</a>';
        $menu_home      = "<a href='" . $helper->url('/') . "'><img id='menuimg' src='" . $helper->url('assets/images/house.png') . "'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile   = "<a href='" . $helper->url('userprofile.php?username=' . $Xusername) . "'><img id='menuimg' src='" . $helper->url('assets/images/user_silhouette.png') . "'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery   = "<a href='" . $helper->url('galleryshow.php?username=' . $Xusername) . "'><img id='menuimg' src='" . $helper->url('assets/images/picture.png') . "'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends   = "<a href='" . $helper->url('friends.php?username=' . $Xusername) . "'><img id='menuimg' src='" . $helper->url('assets/images/group.png') . "'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';

        // Xim Module Inclusion
        if ($xim_helper = $helper->getHelper('xim')) { // checks for existance and if module is active
            if ($xim_helper->getModule()->version() > 102) { //load appropriate js based on XIM version
                $menu_xim_js = "javascript:xim_chatWith('" . $id . "','" . $Xusername . "')";
            } else {
                $menu_xim_js = "javascript:chatWith('" . $id . "','" . $Xusername . "')";
            }

            $menu_ximme = "<a href='javascript:void(0);' onClick=" . $menu_xim_js . "><img height='10px' width='10px' src='" . $helper->url('assets/images/messenger.png') . "'>" . _SMALLWORLD_XIMUSER . $Xusername . '</a>';

            $GLOBALS['xoopsTpl']->assign('sendxim', 'YES');
            if (2 == $fr[0] && ($yourid !== $id)) {
                $GLOBALS['xoopsTpl']->assign('menu_xim', $menu_ximme);
            }
        }

        $birthday_today_text = "<img width='20px' height='20px' src='" . $helper->url('assets/images/bdayballoons_l.png') . "'>" . ' ' . _SMALLWORLD_BDAY_TODAY . ' ' . "<img width='20px' height='20px' src='" . $helper->url('assets/images/bdayballoons_r.png') . "'>";

        $GLOBALS['xoopsTpl']->assign(
            [
                'menu_startpage'         => $menu_startpage,
                'menu_home'              => $menu_home,
                'menu_profile'           => $menu_profile,
                'menu_friends'           => $menu_friends,
                'menu_gallery'           => $menu_gallery,
                'check'                  => $profile,
                'friendID'               => $id,
                'myUid'                  => $yourid,
                'friendship_text'        => $friendship_text,
                'followfriend_text'      => $following_text,
                'friendinvitations'      => $getInvitations,
                'visitoravatar'          => $visitorAvatar,
                'visitoravatarlink'      => $visitorAvatarlink,
                'visitoravatar_highwide' => $visitorAvatar_highwide,
                'myusername'             => $username,
                'username'               => $Xusername,
                'bdaynow'                => $birthday_today_text,
                'isfollowing'            => $fl[0],
                'flds'                   => $helper->getConfig('smallworldusethesefields'),
                'userNumMsg'             => $userNumMsg,
            ]
        );
    }

    if ($profile < Constants::PROFILE_HAS_BOTH) {
        $helper->redirect('index.php');
    }
    //Check Language
    $lang = $GLOBALS['xoopsConfig']['language'];

    // GET various variables from language folder
    if (file_exists($helper->path("language/{$lang}/js/jquery.countdown.js"))) {
        $GLOBALS['xoTheme']->addScript($helper->url("language/{$lang}/js/jquery.countdown.js"));
    } else {
        $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/jquery.countdown.js'));
    }
} else {
    //redirect_header(XOOPS_URL . '/user.php', Constants::REDIRECT_DELAY_MEDIUM, _NOPERM);
}

require_once XOOPS_ROOT_PATH . '/footer.php';
