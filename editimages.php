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
use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

require_once __DIR__ . '/../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_images_edittemplate.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
//require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoopsTpl, $xoopsDB, $xoTheme;

if ($xoopsUser) {
    $userID = $xoopsUser->getVar('uid');

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($userID);
    if ('yes' === $inspect['inspect']) {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1);
    }

    $check   = new Smallworld\SmallWorldUser();
    $profile = $check->CheckIfProfile($userID);
    if ($profile > 0) {
        $xoopsTpl->assign('check', $profile);
        $item = new smallworld\Form;
        $db   = new Smallworld\SwDatabase;

        $editimages = '';
        $sql        = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_images') . " WHERE userid = '" . $userID . "'";
        $result     = $xoopsDB->query($sql);
        $i          = 0;
        while ($sqlfetch = $xoopsDB->fetchArray($result)) {
            $editimages .= $item->edit_images($userID, $sqlfetch['imgurl'], $sqlfetch['desc'], $sqlfetch['id']);
            ++$i;
        }
        $xoopsTpl->append('editimages', $editimages);
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
        //$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/assets/css/colorbox.css');
    } else {
        redirect_header('index.php', 1);
    }
}
require_once XOOPS_ROOT_PATH . '/footer.php';
