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
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

//include_once $GLOBALS['xoops']->path('include/common.php');
// Moderated and fitted from the tutorial by Srinivas Tamada http://9lessons.info

/**
 * Wall Update class
 *
 * Performs CRUD operations for updating the walldata
 *
 */
class WallUpdates
{
    /**
     * @deprecated - not used
     *
     * @return array
     */
    private function getAdminModerators()
    {
        $data   = [];
        $sql    = 'SELECT userid
                FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' su
                LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('groups_users_link') . ' xu ON su.userid = xu.uid
                WHERE xu.uid IN (1)';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * @param $last
     * @param $uid
     * @param $followers
     * @return array
     */
    public function Updates($last, $uid, $followers)
    {
        $uid       = (int)$uid;
        $query     = '';
        $hm        = \XoopsModules\Smallworld\Helper::getInstance()->getConfig('msgtoshow');
        //$set       = smallworld_checkPrivateOrPublic();
        $followers = is_array($followers) ? $followers : [$followers];
        $followers = array_unique(smallworld_array_flatten($followers, 0));
        //$followers = is_array($followers) ? $followers : [$uid];
        $fQuery    = '';
        foreach ($followers as $follower) {
            if ($last > 0) {
                $fQuery .= " OR M.uid_fk=U.userid AND M.uid_fk= '" . $follower . "' and M.msg_id < '" . $last . "'";
            } elseif (0 == $last) {
                $fQuery .= " OR M.uid_fk=U.userid AND M.uid_fk= '" . $follower . "'";
            } elseif ('a' === $last) {
                $fQuery .= " OR M.uid_fk=U.userid AND M.uid_fk= '" . $follower . "'";
            }
        }

        if (0 == $last) {
            $query = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U  WHERE M.uid_fk=U.userid AND M.uid_fk='" . $uid . "'"
                   . $fQuery . ' ORDER BY created DESC LIMIT ' . $hm;
        } elseif ($last > 0) {
            $query = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U  WHERE M.uid_fk=U.userid AND M.uid_fk='" . $uid . "' AND M.msg_id < '" . $last . "'"
                   . $fQuery . ' ORDER BY created DESC LIMIT ' . $hm;
        } elseif ('a' === $last) {
            $query = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U  WHERE M.uid_fk=U.userid AND M.uid_fk='" . $uid . "'"
                   . $fQuery . ' ORDER BY M.msg_id DESC LIMIT ' . $hm;
        } else {
            return [];
        }

        $result = $GLOBALS['xoopsDB']->queryF($query);
        $data   = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get comments based on msg id
     *
     * @param int $msg_id
     * @return array
     */
    public function Comments($msg_id)
    {
        $data = []; //init data array
        $query  = 'SELECT C.msg_id_fk, C.com_id, C.uid_fk, C.comment, C.created, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . ' C, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U WHERE C.uid_fk=U.userid AND C.msg_id_fk='" . $msg_id . "' ORDER BY C.com_id ASC ";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get user image based on uid
     *
     * @deprecated
     * @param int $uid
     * @return string
     */
    public function Gravatar($uid)
    {
        $depMsg = get_class() . __FUNCTION__ . " is deprecated use SwUserHandler::gravatar() instead.";
        if (isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
        } else {
            trigger_error($depMsg, E_USER_WARNING);
        }

        $image  = $avatar = '';
        $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');
        $criteria = new \Criteria('userimage', (int)$uid);
        $criteria->setLimit(1);
        $swUserArray = $swUserHandler->getAll($criteria, ['userimage'], false);
        if (0 < count($swUserArray)) {
            $swUser = array_pop($swUserArray);
            $image = $swUser['userimage'];
        }
        /*
        $sql    = 'SELECT userimage FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . $uid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $image = $r['userimage'];
        }
        */
        $image = ('' == $image || 'blank.gif' === $image) ? $swUserHandler->getAvatarLink($uid, $image) : $image;

        $type = [
            1 => 'jpg',
            2 => 'jpeg',
            3 => 'png',
            4 => 'gif',
        ];

        $ext    = explode('.', $image);
        if (array_key_exists(1, $ext) && in_array(mb_strtolower($ext[1]), $type)) {
            $avatar = $image;
        }

        return $avatar;
    }

    /**
     * Insert update
     *
     * @param int          $uid
     * @param string|array $update
     * @param int          $priv
     * @return array|bool
     */
    public function insertUpdate($uid, $update, $priv = 0)
    {
        $uid    = (int)$uid;
        $priv   = (int)$priv;
        $update = smallworld_sanitize(htmlentities($update, ENT_QUOTES, 'UTF-8'));
        $time   = time();
        $query  = 'SELECT msg_id,message FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE uid_fk='" . $uid . "' ORDER BY msg_id DESC LIMIT 1";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        $row    = $GLOBALS['xoopsDB']->fetchArray($result);
        if ($update != $row['message']) {
            $query    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " (message, uid_fk, priv, created) VALUES ('" . $update . "', '" . $uid . "', '" . $priv . "', '" . $time . "')";
            $result   = $GLOBALS['xoopsDB']->queryF($query);
            $newquery = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U WHERE M.uid_fk=U.userid AND M.uid_fk='" . $uid . "' ORDER BY M.msg_id DESC LIMIT 1 ";
            $result2  = $GLOBALS['xoopsDB']->queryF($newquery);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result2))) {
                $data[] = $row;
            }
            $count = $GLOBALS['xoopsDB']->getRowsNum($result2);
            $retVal = false;
            if (0 < $count) {
                $data = []; // init data array
                while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result2))) {
                    $data[] = $row;
                }
                if (!empty($data)) {
                    $retVal = $data;
                }
            }
            return $retVal;
        }
    }

    /**
     * Insert comment into the dB
     *
     * @param int          $uid
     * @param int          $msg_id
     * @param string|array $comment
     * @return bool|string false on failure
     */
    public function insertComment($uid, $msg_id, $comment)
    {
        $data    = []; // init the data array
        $comment = smallworld_sanitize(htmlentities($comment, ENT_QUOTES, 'UTF-8'));
        $time    = time();
        $query   = 'SELECT com_id,comment FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE uid_fk='" . $uid . "' AND msg_id_fk='" . $msg_id . "' ORDER BY com_id DESC LIMIT 1 ";
        $result  = $GLOBALS['xoopsDB']->fetchArray($query);
        if ($comment != $result['comment']) {
            $query    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " (comment, uid_fk,msg_id_fk,created) VALUES ('" . $comment . "', '" . $uid . "','" . $msg_id . "', '" . $time . "')";
            $result   = $GLOBALS['xoopsDB']->queryF($query);
            $newquery = 'SELECT C.com_id, C.uid_fk, C.comment, C.msg_id_fk, C.created, U.username FROM '
                        . $GLOBALS['xoopsDB']->prefix('smallworld_comments')
                        . ' C, '
                        . $GLOBALS['xoopsDB']->prefix('smallworld_user')
                        . " U WHERE C.uid_fk=U.userid AND C.uid_fk='"
                        . $uid
                        . "' AND C.msg_id_fk='"
                        . $msg_id
                        . "' ORDER BY C.com_id DESC LIMIT 1 ";
            $result2  = $GLOBALS['xoopsDB']->queryF($newquery);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result2))) {
                $data[0] = $row;
            }

            return $data[0];
        }

        return false;
    }

    /**
     * Get array of users followers
     *
     * @param int $me
     * @return array
     */
    public function getFollowers($me)
    {
        $data   = [];
        $me     = (int)$me;
        $query  = 'SELECT you FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE me = '" . $me . "'";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        if (0 == $i) {
            $data = [$me];
        } else {
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * Count all votes
     *
     * @param int $type - not used
     * @param int $column name of column in vote dB table
     * @param int $msgid
     * @return int
     */
    public function countVotes($type, $column, $msgid)
    {
        $sum = 0;
        $valCol = in_array($column, ['up', 'down']) ? $column : false;
        if (false !== $valCol) {
            $query  = 'SELECT SUM(' . $column . ') AS sum FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE msg_id = '" . (int)$msgid . "' AND com_id = '0'";
            $result = $GLOBALS['xoopsDB']->queryF($query);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $sum = $row['sum'];
            }
        }

        return (int)$sum;
    }

    /**
     * Count comments votes
     *
     * @param int $type - not used
     * @param int $val - not used
     * @param int $comid
     * @param int $msgid
     * @returns int
     */
    public function countVotesCom($type, $val, $comid, $msgid)
    {
        $sum = 0;
        $query  = 'SELECT SUM(' . $val . ') AS sum FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '" . $comid . "' AND msg_id = '" . $msgid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $sum = $row['sum'];
        }

        return (int)$sum;
    }

    /**
     * Check if user has voted
     *
     * @param int    $userid
     * @param string $type
     * @param int    $comid
     * @param int    $msgid
     * @return bool
     */
    public function hasVoted($userid, $type, $comid, $msgid)
    {
        $userid = (int)$userid;
        $comid  = (int)$comid;
        $msgid  = (int)$msgid;

        if ('msg' === $type) {
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '0' AND msg_id = '" . $msgid . "' AND user_id = '" . $userid . "'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        } else {
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '" . $comid . "' AND msg_id = '" . $msgid . "' AND user_id = '" . $userid . "'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        }

        return $i ? true : false;
    }

    /**
     * Count messages per user
     * @param int $userid
     * @return int
     */
    public function countMsges($userid)
    {
        $sql    = 'SELECT (SELECT COUNT(*) FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE uid_fk = '" . $userid . "') + (SELECT COUNT(*) FROM " . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE uid_fk = '" . $userid . "')";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $sum    = $GLOBALS['xoopsDB']->fetchRow($result);

        return $sum[0];
    }

    /**
     * Show permalink updates
     *
     * @param int $updid
     * @param int $uid
     * @param int $ownerID
     * @return array
     */
    public function updatesPermalink($updid, $uid, $ownerID)
    {
        $query  = 'SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U  WHERE M.uid_fk=U.userid AND M.uid_fk='" . $ownerID . "'";
        $query  .= " AND M.msg_id = '" . $updid . "'";
        $query  .= ' ORDER BY M.created DESC LIMIT 1';
        $result = $GLOBALS['xoopsDB']->queryF($query);
        //$count  = $GLOBALS['xoopsDB']->getRowsNum($result);
        $data = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Updates share link in dB
     *
     * @param int $updid
     * @param int $ownerID
     * @return array
     */
    public function updatesSharelink($updid, $ownerID)
    {
        $GLOBALS['xoopsLogger']->activated = false;
        //error_reporting(E_ALL);
        $query  = 'SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U WHERE M.uid_fk=U.userid AND M.uid_fk='" . $ownerID . "' AND M.priv = 0";
        $query  .= " AND M.msg_id = '" . (int)$updid . "'";
        $query  .= ' ORDER BY created DESC LIMIT 1';
        $result = $GLOBALS['xoopsDB']->queryF($query);
        //$count  = $GLOBALS['xoopsDB']->getRowsNum($result);
        $data = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get sharing HTML link
     * @param int $id
     * @param int $priv
     * @return string
     */
    public function getSharing($id, $priv)
    {
        $text = '';
        if (1 !== $priv) {
            $text = " | <span class='smallworld_share' id='smallworld_share'>";
            $text .= "<a class='share' id='share-page" . (int)$id . "' href='javascript:void(0);'>" . _SMALLWORLD_SHARELINK . '</a></span>';
        }

        return $text;
    }

    /**
     * Get content for sharing - HTML div
     *
     * @param int    $id
     * @param int    $priv
     * @param string $permalink
     * @param string $desc
     * @param string $username
     * @return string
     */
    public function getSharingDiv($id, $priv, $permalink, $desc, $username)
    {
        $text = '';
        if (1 != $priv) {
            $text = "<div style='display: none;' class='smallworld_bookmarks' id='share-page' name='share-page" . (int)$id . "'>"
                  . "<span name='share-page" . (int)$id . "' rel1='" . $desc . "' rel2= '" . $username . "' rel=" . $permalink . " id='basicBookmark' title='" . _SMALLWORLD_SHAREBOX_TITLE . "'>"
                  . '</span></div>';
        }

        return $text;
    }

    /**
     * Parse update and comments array to template for public updates
     *
     * @param array $updatesarray
     * @param int   $id
     * @return void
     */
    public function parsePubArray($updatesarray, $id)
    {
        /**
         * @var \XoopsModules\Smallworld\Helper $helper
         * @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler
         */
        $helper            = Helper::getInstance();
        $swUserHandler     = $helper->getHandler('SwUser');
        $check             = new User();
        $swDB              = new SwDatabase();
        $profile           = $swUserHandler->checkIfProfile($id);
        $myavatar          = $swUserHandler->gravatar($id);
        $myavatarlink      = $swUserHandler->getAvatarLink($id, $myavatar);
        $myavatar_size     = smallworld_getImageSize(80, 100, $myavatarlink);
        $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 35);

        $GLOBALS['xoopsTpl']->assign([
            'myavatar'          => $myavatar,
            'myavatarlink'      => $myavatarlink,
            'myavatar_highwide' => $myavatar_highwide
        ]);

        foreach ($updatesarray as $data) {
            // Is update's user a friend ?
            $frU = $check->friendcheck($id, $data['uid_fk']);
            $USW = ['posts' => 0, 'comments' => 0];

            if ($helper->isUserAdmin() || $data['uid_fk'] == $id) {
                $USW    = ['posts' => 1, 'comments' => 1];
                $frU[0] = 2;
            } else {
                $USW = json_decode($swDB->getSettings($data['uid_fk']), true);
            }

            $wm['msg_id']          = $data['msg_id'];
            $wm['orimessage']      = (1 == $USW['posts'] || $profile >= Constants::PROFILE_HAS_BOTH) ? str_replace(["\r", "\n"], '', smallworld_stripWordsKeepUrl($data['message'])) : '';
            $wm['message']         = (1 == $USW['posts'] || $profile >= Constants::PROFILE_HAS_BOTH) ? smallworld_tolink(htmlspecialchars_decode($data['message']), $data['uid_fk']) : _SMALLWORLD_MESSAGE_PRIVSETPOSTS;
            $wm['message']         = smallworld_cleanup($wm['message']);
            $wm['created']         = smallworld_time_stamp($data['created']);
            $wm['username']        = $data['username'];
            $wm['uid_fk']          = $data['uid_fk'];
            $wm['priv']            = $data['priv'];
            $wm['avatar']          = $swUserHandler->gravatar($data['uid_fk']);
            $wm['avatar_link']     = $swUserHandler->getAvatarLink($data['uid_fk'], $wm['avatar']);
            $wm['avatar_size']     = smallworld_getImageSize(80, 100, $wm['avatar_link']);
            $wm['avatar_highwide'] = smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);
            $wm['vote_up']         = $this->countVotes('msg', 'up', $data['msg_id']);
            $wm['vote_down']       = $this->countVotes('msg', 'down', $data['msg_id']);
            $wm['sharelinkurl']    = $helper->url("smallworldshare.php?ownerid={$data['uid_fk']}");
            $wm['sharelinkurl']    .= '&updid=' . $data['msg_id'] . '';
            $wm['usernameTitle']   = $wm['username'] . _SMALLWORLD_UPDATEONSITEMETA . $GLOBALS['xoopsConfig']['sitename'];
            if (1 == $USW['posts'] || $profile >= Constants::PROFILE_HAS_BOTH) {
                $wm['sharelink'] = $this->getSharing($wm['msg_id'], $wm['priv']);
            } else {
                $wm['sharelink'] = $this->getSharing($wm['msg_id'], 1);
            }

            if (1 == $USW['posts'] || $profile >= Constants::PROFILE_HAS_BOTH) {
                $wm['sharediv'] = $this->getSharingDiv($wm['msg_id'], $wm['priv'], $wm['sharelinkurl'], $wm['orimessage'], $wm['usernameTitle']);
            } else {
                $wm['sharediv'] = $this->getSharingDiv($wm['msg_id'], 1, $wm['sharelinkurl'], $wm['orimessage'], $wm['usernameTitle']);
            }
            $wm['linkimage']     = $helper->url('assets/images/link.png');
            $wm['permalink']     = $helper->url("permalink.php?ownerid={$data['uid_fk']}&updid={$data['msg_id']}");
            $wm['commentsarray'] = $this->Comments($data['msg_id']);

            if (2 == $frU[0] || 1 == $USW['posts']) {
                $GLOBALS['xoopsTpl']->append('walldata', $wm);
            }

            foreach ($wm['commentsarray'] as $cdata) {
                // Is commentuser a friend ?
                $frC = $check->friendcheck($id, $cdata['uid_fk']);
                $USC = ['posts' => 0, 'comments' => 0];

                if ($helper->isUserAdmin() || $cdata['uid_fk'] == $id) {
                    $USC    = ['posts' => 1, 'comments' => 1];
                    $frC[0] = Constants::PROFILE_HAS_BOTH;
                } else {
                    $USC = json_decode($swDB->getSettings($cdata['uid_fk']), true);
                }

                $wc['msg_id_fk']       = $cdata['msg_id_fk'];
                $wc['com_id']          = $cdata['com_id'];
                $wc['comment']         = (1 == $USC['comments'] || $profile >= Constants::PROFILE_HAS_BOTH) ? smallworld_tolink(htmlspecialchars_decode($cdata['comment']), $cdata['uid_fk']) : _SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
                $wc['comment']         = smallworld_cleanup($wc['comment']);
                $wc['time']            = smallworld_time_stamp($cdata['created']);
                $wc['username']        = $cdata['username'];
                $wc['uid']             = $cdata['uid_fk'];
                $wc['myavatar']        = $myavatar;
                $wc['myavatar_link']   = $myavatarlink;
                $wc['avatar_size']     = smallworld_getImageSize(80, 100, $wc['myavatar_link']);
                $wc['avatar_highwide'] = smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);
                $wc['cface']           = $swUserHandler->gravatar($cdata['uid_fk']);
                $wc['avatar_link']     = $swUserHandler->getAvatarLink($cdata['uid_fk'], $wc['cface']);
                $wc['vote_up']         = $this->countVotesCom('com', 'up', $cdata['msg_id_fk'], $cdata['com_id']);
                $wc['vote_down']       = $this->countVotesCom('com', 'down', $cdata['msg_id_fk'], $cdata['com_id']);

                if (Constants::PROFILE_HAS_BOTH == $frC[0] || 1 == $USC['comments']) {
                    $GLOBALS['xoopsTpl']->append('comm', $wc);
                }
            }
        }
    }
}
