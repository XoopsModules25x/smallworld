<?php namespace Xoopsmodules\smallworld;
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
class SmallWorldFriends
{

    /**
     * @Show friends of ID
     * @param int $id
     * @return string
     */
    public function Showfriends($id)
    {
        global $xoopsUser, $xoTheme, $xoopsTpl, $arr04, $arr05, $xoopsDB;
        if ($xoopsUser) {
            $user   = new \XoopsUser($id);
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
     * @return array|bool
     */
    public function getFriends($id, $action)
    {
        global $xoopsUser, $xoopsDB;
        $meuser = $xoopsUser->getVar('uid');
        $data   = [];
        if ($xoopsUser) {
            if ('pending' === $action) {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE me = '" . $id . "' AND status = 1";
            } elseif ('friends' === $action) {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE me = '" . $id . "' AND status = 2";
            } elseif ('following' === $action) {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_followers') . " WHERE me = '" . $id . "'";
            } elseif ('followingme' === $action) {
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
