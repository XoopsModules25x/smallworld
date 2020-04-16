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
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_imgupload.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $GLOBALS['xoopsLogger']->activated = false;

    $id      = $GLOBALS['xoopsUser']->getVar('uid');
    $check   = new Smallworld\User();
    $profile = $check->checkIfProfile($id);
    if ($profile >= Constants::PROFILE_HAS_BOTH) {
        $GLOBALS['xoopsTpl']->assign('check', $profile);
        $item = new Smallworld\Form();
        //$swDB = new Smallworld\SwDatabase();

        // ------------ DISPLAY IMAGES ------------ //
        // ------------ IMAGE UPLOADER ------------ //
        // Image upload form
        $upload = $item->uploadform($id);
    } else {
        $helper->redirect('register.php', Constants::REDIRECT_DELAY_SHORT);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', Constants::REDIRECT_DELAY_SHORT, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
