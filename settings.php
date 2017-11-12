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
 * @Repository path:        $HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/friendinvite.php $
 * @Last       committed:        $Revision: 8815 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2012-01-26 16:33:48 +0100 (to, 26 jan 2012) $
 * @ID         :                    $Id: friendinvite.php 8815 2012-01-26 15:33:48Z djculex $
 **/
global $xoopsUser;
include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/include/functions.php");
global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;

if ($xoopsUser) {
    $check    = new SmallWorldUser;
    $db       = new SmallWorldDB;
    $id       = $xoopsUser->getVar('uid');
    $settings = array();
    if (isset($_POST['posts']) && isset($_POST['comments'])) {
        $post = serialize(array(
                              'posts'    => $_POST['posts'],
                              'comments' => $_POST['comments'],
                              'notify'   => $_POST['notify']
                          ));

        if ($id >= 1) {
            $db->saveSettings($id, $post);
        }
        echo json_encode($post);
    } else {
        $posts = stripslashes($db->GetSettings($id));
        echo json_encode($posts);
    }
} else {
    header('HTTP/1.1 403 Forbidden');
}

?>
