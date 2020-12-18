<?php

namespace XoopsModules\Smallworld;

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

/**
 *
 * Images Class
 *
 * Handles album creation and retrieval
 *
 */
class Images
{
    /**
     * Create album folders
	 *
	 * @todo use \Smallworld\SysUtility::createFolder() to refactor this method
     * @param int $userId
     * @return void
     */
    public function createAlbum($userId)
    {
        //@todo change $dir to XOOPS_UPLOAD_PATH . /smallworld/albums/[$userId]
        $dir = XOOPS_UPLOAD_PATH . '/albums_smallworld';
        if (!file_exists($dir . '/' . $userId . '/thumbnails') || !file_exists($dir . '/' . $userId . '/')) {
            if (!is_dir($dir . '/')) {
                if (!mkdir($dir, 0777) && !is_dir($dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }
            } else {
                if (!mkdir($concurrentDirectory = $dir . '/' . $userId, 0777) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
                if (!mkdir($concurrentDirectory = $dir . '/' . $userId . '/thumbnails', 0777) && !is_dir($concurrentDirectory)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
                }
                file_put_contents("{$dir}/index.html", '<script>history.go(-1);</script>');
                file_put_contents("{$dir}/{$userId}/index.html", '<script>history.go(-1);</script>');
                file_put_contents("{$dir}/{$userId}/thumbnails/index.html", '<script>history.go(-1);</script>');
            }
        }
    }

    /**
     * View user album
     *
     * @param int $userId owner
     * @param int $user visitors
     * @return array|bool
     */
    public function viewalbum($userId, $user)
    {
        $post        = [];
        $checkFriend = new User();
        $foundArray = $checkFriend->friendcheck((int)$userId, (int)$user);
        if (0 < count($foundArray)) {
            // check friend is good to go
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " WHERE userid = '" . (int)$user . "'";
            $result = $GLOBALS['xoopsDB']->query($sql);
            while (false !== ($sqlfetch = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $post[] = [
                    'id'      => stripslashes($sqlfetch['id']),
                    'userid'  => stripslashes($sqlfetch['userid']),
                    'imgurl'  => stripslashes($sqlfetch['imgurl']),
                    'desc'    => SwFunc\cleanupString($sqlfetch['desc']),
                    'alt'     => SwFunc\cleanupString($sqlfetch['desc']),
                    'time'    => stripslashes($sqlfetch['time']),
                    'editimg' => "<span class='smallworld_edit_imgdesc_holder'><img src='" . Helper::getInstance()->url('images/edit_icon.png') . "'></span> <a class='smallworld_edit_imgdesc' href='editimages.php'>" . _SMALLWORLD_EDITDESCRIPTION . '</a>'
                ];
            }

            return $post;
        }
        //Not a friends album
        return false;
    }

    /**
     * Get image count for user
     *
     * @param int $userid
     * @return int
     */
    public function count($userid)
    {
        $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " WHERE userid = '" . $userid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $GLOBALS['xoopsDB']->getRowsNum($result);
    }
}
