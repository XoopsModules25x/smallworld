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
 * @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/search.php $
 * @Last       committed:        $Revision: 12175 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2013-10-15 19:41:43 +0200 (ti, 15 okt 2013) $
 * @ID         :                    $Id: search.php 12175 2013-10-15 17:41:43Z djculex $
 **/
include_once('../../mainfile.php');
$xoopsOption['template_main'] = 'smallworld_index.html';
include XOOPS_ROOT_PATH . '/header.php';
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php');
global $xoopsUser, $xoTheme, $xoopsLogger, $xoopsDB;
$xoopsLogger->activated = false;
if ($_GET) {
    $q      = Smallworld_sanitize($_GET['term']);
    $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE realname LIKE '%" . $q . "%' OR username LIKE '%" . $q . "%' ORDER BY userid LIMIT 5";
    $result = $xoopsDB->query($sql);
    $data   = array();

    while ($row = $xoopsDB->fetchArray($result)) {
        $user  = new xoopsUser($row['userid']);
        $image = '<img src="' . smallworld_getAvatarLink($row['userid'], $row['userimage']) . '" height="20" />';

        $imageHw = smallworld_imageResize($imageSize[0], $imageSize[1], 30);
        $data[]  = array(
            'label' => $image . ' ' . '<span class="searchusername">' . $row['realname'] . ' (' . $row['username'] . ')</span>',
            'value' => $user->uname()
        );
    }
    // jQuery wants JSON data
    echo json_encode($data);

    flush();
} else {
}
?>
