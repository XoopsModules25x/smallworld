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
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_galleryshow.html';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoTheme, $xoopsLogger;
$xoopsLogger->activated = false;

$admin = false;

if ($xoopsUser) {
    $id           = $xoopsUser->getVar('uid');
    $check        = new smallworld\SmallWorldUser;
    $image        = new smallworld\SmallWorldImages;
    $username     = $_GET['username'];
    $userID       = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
    $userisfriend = $check->friendcheck($id, $userID);

    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $xoopsTpl->assign('isadminuser', 'YES');
        $admin = true;
    }

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($id);
    if ('yes' === $inspect['inspect']) {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1, _SMALLWORLD_INSPEC_usermsg);
    }

    $profile = $check->CheckIfProfile($id);
    if ($profile >= 2 || 2 == $userisfriend[0] || true === $admin) {
        $myusername = $xoopsUser->getVar('uname');

        $user        = new XoopsUser($id);
        $countimages = $image->count($userID);

        //$gallery = $image->viewalbum ($id, $user=$xoopsUser->getVar('uid'));
        $gallery = $image->viewalbum($id, $userID);
        $xoopsTpl->assign('countimages', $countimages);
        $xoopsTpl->assign('userisfriend', $userisfriend[0]);
        $xoopsTpl->assign('gallery', $gallery);
        $xoopsTpl->assign('closealbum', _SMALLWORLD_ALBUMTITLETEXT);
        $xoopsTpl->assign('username', $username);
        $xoopsTpl->assign('myusername', $myusername);
        $xoopsTpl->assign('gallerytitleheader', _SMALLWORLD_TITLEHEADER);
        $xoopsTpl->assign('check', 1);
    } elseif (0 == $profile) {
        $check->chkUser();
    } else {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
