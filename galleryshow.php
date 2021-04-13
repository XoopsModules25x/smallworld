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

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_galleryshow.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$GLOBALS['xoopsLogger']->activated = false;

$admin = $helper->isUserAdmin() ? true: false;

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    /** @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler */
    $swUserHandler = $helper->getHandler('SwUser');
    $id            = $GLOBALS['xoopsUser']->uid();
    $check         = new Smallworld\User();
    $image         = new Smallworld\Images;
    $username      = Request::getString('username', '', 'GET');
    $userId        = $swUserHandler->getByName($username); // gets id of user which profile you want to see
    //$userId        = smallworld_isset_or($_GET['username']); // gets id of user which profile you want to see
    $userisfriend  = $check->friendcheck($id, $userId);
	
    $tpl_admin = $admin ? 'YES' : 'NO';
    $GLOBALS['xoopsTpl']->assign('isadminuser', $tpl_admin);

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = smallworld_isInspected($id);
    if ('yes' === $inspect['inspect']) {
        $helper->redirect('userprofile.php?username=' . $GLOBALS['xoopsUser']->uname(), Constants::REDIRECT_DELAY_SHORT, _SMALLWORLD_INSPEC_usermsg);
    }

    $profile = $swUserHandler->checkIfProfile($id);
    if ($profile >= Constants::PROFILE_HAS_BOTH || 2 == $userisfriend[0] || true === $admin) {
        $myusername  = $GLOBALS['xoopsUser']->uname();
        $countimages = $image->count($userId);
        //$gallery = $image->viewalbum ($id, $user=$GLOBALS['xoopsUser']->getVar('uid'));
        $gallery = $image->viewalbum($id, $userId);
        $GLOBALS['xoopsTpl']->assign([
            'countimages'        => $countimages,
            'userisfriend'       => $userisfriend[0],
            'gallery'            => $gallery,
            'closealbum'         => _SMALLWORLD_ALBUMTITLETEXT,
            'username'           => $username,
            'myusername'         => $myusername,
            'gallerytitleheader' => _SMALLWORLD_TITLEHEADER,
            'check'              => 1
        ]);
    } elseif (Constants::PROFILE_NONE == $profile) {
        $check->chkUser();
    } else {
        $helper->redirect('userprofile.php?username=' . $GLOBALS['xoopsUser']->uname(), Constants::REDIRECT_DELAY_SHORT, _NOPERM);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', Constants::REDIRECT_DELAY_SHORT, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
