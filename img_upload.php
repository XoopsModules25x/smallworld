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
 * @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/img_upload.php $
 * @Last       committed:        $Revision: 11659 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2013-06-12 19:42:10 +0200 (on, 12 jun 2013) $
 * @ID         :                    $Id: img_upload.php 11659 2013-06-12 17:42:10Z djculex $
 **/
include_once("../../mainfile.php");
$xoopsOption['template_main'] = 'smallworld_userprofile_imgupload.html';
include XOOPS_ROOT_PATH . '/header.php';
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/include/arrays.php");
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/class/class_collector.php");

if ($xoopsUser) {
    global $xoTheme;
    $xoopsLogger->activated = false;

    $id      = $xoopsUser->getVar('uid');
    $check   = new SmallWorldUser;
    $profile = $check->CheckIfProfile($id);
    if ($profile >= 2) {
        $xoopsTpl->assign('check', $profile);
        $item = new SmallWorldForm;
        $db   = new SmallWorldDB;

        // ------------ DISPLAY IMAGES ------------ //
        // ------------ IMAGE UPLOADER ------------ //
        // Image upload form
        $upload = $item->uploadform($id);
    } else {
        redirect_header("register.php", 1);
    }
} else {
    redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
}
include(XOOPS_ROOT_PATH . "/footer.php");
?>
