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

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_images_edittemplate.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $userID = $GLOBALS['xoopsUser']->getVar('uid');

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = smallworld_isInspected($userID);
    if ('yes' === $inspect['inspect']) {
        $helper->redirect('userprofile.php?username=' . $GLOBALS['xoopsUser']->uname());
    }

    $check   = new Smallworld\User();
    $profile = $check->checkIfProfile($userID);
    if (Constants::PROFILE_HAS_BOTH <= $profile) {
        $GLOBALS['xoopsTpl']->assign('check', $profile);
        $item       = new Smallworld\Form();
        $editimages = '';
        $sql        = 'SELECT *FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " WHERE userid = '" . $userID . "'";
        $result     = $GLOBALS['xoopsDB']->query($sql);
        $i          = 0;
        while (false !== ($sqlfetch = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $editimages .= $item->edit_images($userID, $sqlfetch['imgurl'], $sqlfetch['desc'], $sqlfetch['id']);
            ++$i;
        }
        $GLOBALS['xoopsTpl']->append('editimages', $editimages);
        $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
        //$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/colorbox.css'));
    } else {
        redirect_header('index.php', Constants::REDIRECT_DELAY_SHORT);
    }
}
require_once XOOPS_ROOT_PATH . '/footer.php';
