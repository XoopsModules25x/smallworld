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

/*
 *  Does a sync to remove orphans from smallworld db
 *
 */

/**
 * Class DoSync
 */
class DoSync
{
    /**
     * check for orphans (xoops_users <-> smallworld_users) and remove from smallworld
     */
    public function checkOrphans()
    {
        $sql    = 'SELECT userid FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' WHERE userid NOT IN ( SELECT uid FROM ' . $GLOBALS['xoopsDB']->prefix('users') . ')';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        if ($result) {
            while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $this->deleteAccount($r['userid']);
            }
        }
    }

    /**
     * deleteAccount function
     * - Delete user account and associate rows across tables
     *
     * @param int $userid
     * @return bool true on success, false on failure
     */
    public function deleteAccount($userid)
    {
        $user     = new \XoopsUser($userid);
        //$username = $user->uname();
        $sql01    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " WHERE userid = '" . $userid . "'";
        $sql02    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE uid_fk = '" . $userid . "'";
        $sql03    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE me = '" . $userid . "' OR you = '" . $userid . "'";
        $sql04    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . $userid . "' OR you = '" . $userid . "'";
        $sql05    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " WHERE userid = '" . $userid . "'";
        $sql06    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE uid_fk = '" . $userid . "'";
        $sql07    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . $userid . "'";
        $sql08    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE user_id = '" . $userid . "'";
        $sql09    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_complaints') . " WHERE owner = '" . $userid . "' OR byuser_id = '" . $userid . "'";
        $sql10    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . " WHERE userid = '" . $userid . "'";

        $result01 = $GLOBALS['xoopsDB']->queryF($sql01);
        $result02 = $GLOBALS['xoopsDB']->queryF($sql02);
        $result03 = $GLOBALS['xoopsDB']->queryF($sql03);
        $result04 = $GLOBALS['xoopsDB']->queryF($sql04);
        $result05 = $GLOBALS['xoopsDB']->queryF($sql05);
        $result06 = $GLOBALS['xoopsDB']->queryF($sql06);
        $result07 = $GLOBALS['xoopsDB']->queryF($sql07);
        $result08 = $GLOBALS['xoopsDB']->queryF($sql08);
        $result09 = $GLOBALS['xoopsDB']->queryF($sql09);
        $result10 = $GLOBALS['xoopsDB']->queryF($sql10);
        // Remove picture dir
        $dirname = XOOPS_ROOT_PATH . '/uploads/albums_smallworld' . '/' . $userid . '/';
        $result11 = $this->smallworld_remDir($userid, $dirname, $empty = false);

        return $result01 && $result02 && $result03 && $result04 && $result05 && $result06 && $result07 && $result08 && $result09 && $result10 && $result11;
    }

    /**
     * smallworld_remDir function
     * - Remove user image dir in uploads.
     * @param int         $userid
     * @param string|bool $directory
     * @param bool|int    $empty
     * @return bool
     */
    public function smallworld_remDir($userid, $directory, $empty = false)
    {
        if ('' != $userid) {
            if ('/' === mb_substr($directory, -1)) {
                $directory = mb_substr($directory, 0, -1);
            }

            if (!file_exists($directory) || !is_dir($directory)) {
                return false;
            } elseif (!is_readable($directory)) {
                return false;
            }
            $directoryHandle = opendir($directory);
            while (false !== ($contents = readdir($directoryHandle))) {
                if ('.' !== $contents && '..' !== $contents) {
                    $path = $directory . '/' . $contents;
                    if (is_dir($path)) {
                        $this->smallworld_remDir($userid, $path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($directoryHandle);
            if (false === $empty) {
                if (!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * SmallworldDeleteDirectory function
     * - Delete images from users on delete
     * @param int $userid
     * @return true
     */
    public function SmallworldDeleteDirectory($userid)
    {
        $dirname = XOOPS_ROOT_PATH . '/uploads/albums_smallworld' . '/' . $userid . '/';
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ('.' != $file && '..' != $file) {
                if (!is_dir($dirname . '/' . $file)) {
                    unlink($dirname . '/' . $file);
                } else {
                    $this->SmallworldDeleteDirectory($dirname . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);

        return true;
    }
}
