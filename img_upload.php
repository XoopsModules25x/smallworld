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
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_imgupload.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';

if ($xoopsUser) {
    global $xoTheme;
    $xoopsLogger->activated = false;

    $id      = $xoopsUser->getVar('uid');
    $check   = new smallworld\SmallWorldUser;
    $profile = $check->CheckIfProfile($id);
    if ($profile >= 2) {
        $xoopsTpl->assign('check', $profile);
        $item = new smallworld\SmallWorldForm;
        $db   = new smallworld\SmallWorldDB;

        // ------------ DISPLAY IMAGES ------------ //
        // ------------ IMAGE UPLOADER ------------ //
        // Image upload form
        $upload = $item->uploadform($id);
    } else {
        redirect_header('register.php', 1);
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
