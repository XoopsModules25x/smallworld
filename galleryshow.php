<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  :            {@link https://xoops.org 2001-2017 XOOPS Project}
 * @license    :                {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * @module     :                Smallworld
 * @Author     :                Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @copyright  :            2011 Culex
 * @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/galleryshow.php $
 * @Last       committed:        $Revision: 12114 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2013-10-01 19:11:18 +0200 (ti, 01 okt 2013) $
 * @ID         :                    $Id: galleryshow.php 12114 2013-10-01 17:11:18Z djculex $
 **/
include_once '../../mainfile.php';
$xoopsOption['template_main'] = 'smallworld_galleryshow.html';
include_once XOOPS_ROOT_PATH . '/header.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
include_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoTheme, $xoopsLogger;
$xoopsLogger->activated = false;

$admin = false;

if ($xoopsUser) {
    $id           = $xoopsUser->getVar('uid');
    $check        = new SmallWorldUser;
    $image        = new SmallWorldImages;
    $username     = $_GET['username'];
    $userID       = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
    $userisfriend = $check->friendcheck($id, $userID);

    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        $xoopsTpl->assign('isadminuser', 'YES');
        $admin = true;
    }

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($id);
    if ($inspect['inspect'] == 'yes') {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1, _SMALLWORLD_INSPEC_usermsg);
    }

    $profile = $check->checkIfProfile($id);
    if ($profile >= 2 || $userisfriend[0] == 2 || $admin == true) {
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
    } elseif ($profile == 0) {
        $check->chkUser();
    } else {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
include XOOPS_ROOT_PATH . '/footer.php';
?>
