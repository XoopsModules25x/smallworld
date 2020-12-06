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
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_template.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;
//error_reporting(E_ALL);

if ($xoopsUser) {
    SmallworldDeleteOldInspects();
    $id         = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
    $yourid     = $xoopsUser->getVar('uid'); // your uid
    $Xuser      = new XoopsUser($id);
    $Xusername  = $Xuser->getVar('uname');
    $check      = new smallworld\SmallWorldUser;
    $profile    = $check->CheckIfProfile($yourid);
    $userNumMsg = smallworld_countUserWallMsges($id);
    $fr[0]      = '';
    $fl[0]      = '';
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $xoopsTpl->assign('isadminuser', 'YES');
    } else {
        $xoopsTpl->assign('isadminuser', 'NO');
    }

    if ($profile >= 2) {
        $user  = new smallworld\SmallWorldProfile;
        $dBase = new smallworld\SmallWorldDB;
        $user->ShowUser($id);
        $username  = $xoopsUser->getVar('uname'); //Myusername
        $inspected = Smallworld_isInspected($id);
        $xoopsTpl->assign('inspect', $inspected['inspect']);
        if ('no' !== $inspected['inspect']) {
            $xoopsTpl->assign('inspecttime', $inspected['totaltime']);
        }
        $xoopsTpl->assign('ownerofpage', $id);

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
            0 == $fl[0];
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
        $Wall = new smallworld\WallUpdates();

        $visitorAvatar          = $Wall->Gravatar($yourid);
        $visitorAvatarlink      = smallworld_getAvatarLink($yourid, $visitorAvatar);
        $visitorAvatar_size     = smallworld_getImageSize(80, 100, $visitorAvatarlink);
        $visitorAvatar_highwide = smallworld_imageResize($visitorAvatar_size[0], $visitorAvatar_size[1], 35);

        // Follow array here
        $followers    = $Wall->getFollowers($id);
        $updatesarray = $Wall->Updates(0, $id, $id);
        $Wall->ParsePubArray($updatesarray, $id);

        // Create form for private settings
        $form         = new smallworld\SmallWorldForm;
        $usersettings = $form->usersettings($yourid, $selected = null);
        $xoopsTpl->assign('usersetting', $usersettings);

        // Get usermenu to template
        $menu_startpage = "<a href='" . XOOPS_URL . "/modules/smallworld/publicindex.php'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/highrise.png'>" . _SMALLWORLD_STARTPAGE . '</a>';
        $menu_home      = "<a href='" . XOOPS_URL . "/modules/smallworld/'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/house.png'>" . _SMALLWORLD_HOME . '</a>';
        $menu_profile   = "<a href='" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $Xusername . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/user_silhouette.png'>" . _SMALLWORLD_PROFILEINDEX . '</a>';
        $menu_gallery   = "<a href='" . XOOPS_URL . '/modules/smallworld/galleryshow.php?username=' . $Xusername . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/picture.png'>" . _SMALLWORLD_GALLERY . '</a>';
        $menu_friends   = "<a href='" . XOOPS_URL . '/modules/smallworld/friends.php?username=' . $Xusername . "'><img id='menuimg' src='" . XOOPS_URL . "/modules/smallworld/assets/images/group.png'>" . _SMALLWORLD_FRIENDSPAGE . '</a>';

        if (smallworld_XIMversion() > 102) {
            $menu_xim_js = "javascript:xim_chatWith('" . $id . "','" . $Xusername . "')";
        } else {
            $menu_xim_js = "javascript:chatWith('" . $id . "','" . $Xusername . "')";
        }

        $menu_ximme = "<a href='javascript:void(0);' onClick=" . $menu_xim_js . "><img height='10px' width='10px' src='" . XOOPS_URL . "/modules/smallworld/assets/images/messenger.png'>" . _SMALLWORLD_XIMUSER . $Xusername . '</a>';

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

        $birthday_today_text = "<img width='20px' heigh='20px' src='images/bdayballoons_l.png'>";
        $birthday_today_text .= ' ' . _SMALLWORLD_BDAY_TODAY . ' ';
        $birthday_today_text .= "<img width='20px' heigh='20px' src='images/bdayballoons_r.png'>";

        $xoopsTpl->assign('menu_startpage', $menu_startpage);
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
        $xoopsTpl->assign('visitoravatar', $visitorAvatar);
        $xoopsTpl->assign('visitoravatarlink', $visitorAvatarlink);
        $xoopsTpl->assign('visitoravatar_highwide', $visitorAvatar_highwide);
        $xoopsTpl->assign('myusername', $username);
        $xoopsTpl->assign('username', $Xusername);
        $xoopsTpl->assign('bdaynow', $birthday_today_text);
        $xoopsTpl->assign('isfollowing', $fl[0]);
        $xoopsTpl->assign('flds', smallworld_GetModuleOption('smallworldusethesefields', $repmodule = 'smallworld'));
        $xoopsTpl->assign('userNumMsg', $userNumMsg);
    }

    if ($profile < 2) {
        redirect_header(XOOPS_URL . '/modules/smallworld/index.php');
    }
    //Check Language
    $lang = $xoopsConfig['language'];

    // GET various variables from language folder
    if (file_exists(XOOPS_ROOT_PATH . '/modules/smallworld/language/js/' . $lang . '/jquery.countdown.js')) {
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/' . $lang . '/js/jquery.countdown.js');
    } else {
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/english/js/jquery.countdown.js');
    }
} else {
    //redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
}

require_once XOOPS_ROOT_PATH . '/footer.php';
