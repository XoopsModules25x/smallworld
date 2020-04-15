<?php

namespace XoopsModules\Smallworld;

/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use XoopsModules\Smallworld\Constants;

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
class Friends
{
    /**
     * Show friends of ID
     *
     * @deprecated - DO NOT USE
     * @todo unfinished - needs completion
     *
     * @param int $id
     * @return string
     */
    public function showFriends($id)
    {
        if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
            $user   = new \XoopsUser($id);
            $myName = $user->getUnameFromId((int)$id); // My name
            $swDB   = new SwDatabase();
            $check  = new User();
        }

        return [(int)$id => $myName];
    }

    /**
     * Get friends array of ID
     *
     * must be registered user to call this function
     *
     * @param int    $id
     * @param string $action
     * @return array - empty if nothing found or not authorized
     */
    public function getFriends($id, $action)
    {
        $id = (int)$id;
        $data   = [];
        if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
            switch ($action) {
                case 'pending':
                    $sql = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . $id . "' AND status = " . Constants::FRIEND_STATUS_PENDING;
                    break;
                case 'friends':
                    $sql = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . $id . "' AND status = " . Constants::FRIEND_STATUS_APPROVED;
                    break;
                case 'following':
                    $sql = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE me = '" . $id . "'";
                    break;
                case 'followingme':
                    $sql = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE you = '" . $id . "'";
                    break;
                default:
                    return $data;
            }
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $data[] = $row;
            }
        }

        return $data;
    }
}
