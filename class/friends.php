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
 * @Repository path:        $HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/class/friends.php $
 * @Last       committed:        $Revision: 8907 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2012-02-08 04:54:48 +0100 (on, 08 feb 2012) $
 * @ID         :                    $Id: friends.php 8907 2012-02-08 03:54:48Z djculex $
 **/

class SmallWorldFriends
{

    /**
     * @Show friends of ID
     * @param int $id
     * @return string
     */
    function Showfriends($id)
    {
        global $xoopsUser, $xoTheme, $xoopsTpl, $arr04, $arr05, $xoopsDB;
        if ($xoopsUser) {
            $user   = new XoopsUser($id);
            $myName = $xoopsUser->getUnameFromId($xoopsUser->getVar('uid')); // My name
            $db     = new SmallWorldDB;
            $check  = new SmallWorldUser;
        } else {
        }
    }

    /**
     * @Get friends array of ID
     * @param int    $id
     * @param string $action
     * @return array
     */
    function getFriends($id, $action)
    {
        global $xoopsUser, $xoopsDB;
        $meuser = $xoopsUser->getVar('uid');
        $data   = array();
        if ($xoopsUser) {
            if ($action == 'pending') {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE me = '" . $id . "' AND status = 1";
            } elseif ($action == 'friends') {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE me = '" . $id . "' AND status = 2";
            } elseif ($action == 'following') {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_followers') . " WHERE me = '" . $id . "'";
            } elseif ($action == 'followingme') {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_followers') . " WHERE you = '" . $id . "'";
            }
            $result = $xoopsDB->queryF($sql);
            $count  = $xoopsDB->getRowsNum($result);
            if ($count < 1) {
                return false;
            } else {
                while ($row = $xoopsDB->fetchArray($result)) {
                    $data[] = $row;
                }
            }
        }
        return $data;
    }
}

?>
