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
 * @package      \XoopsModules\SmallWorld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 */

use XoopsModules\Smallworld\Constants;

/**
 * Public Wall Updates Class
 *
 * SmallWorld - Moderated and fitted from the tutorial by Srinivas Tamada http://9lessons.info
 * Provides wall update methods
 */
class PublicWallUpdates
{
    /**
     * Get an array of Admin Moderators
     *
     * @deprecated - not used
     * @return array
     */
    private function getAdminModerators()
    {
        $sql    = 'SELECT userid
                FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' su
                LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('groups_users_link') . ' xu ON su.userid = xu.uid
                WHERE xu.uid IN (1)';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $data = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get array of users being inspected
     *
     * @return int|string
     */
    public function inspected()
    {
        $sql    = 'SELECT userid FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . ' WHERE (inspect_start+inspect_stop) > ' . time() . '';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $data   = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }
        $sub = !empty($data) ? implode(',', smallworld_array_flatten(array_unique($data), 0)) : 0;

        return $sub;
    }

    /**
     * Get array of updates
     *
     * @param string|int $last
     * @param array $moderators
     * @return array|bool
     */
    public function Updates($last, $moderators)
    {
        /** @var \XoopsModules\Smallworld\Helper $helper */
        $helper     = Helper::getInstance();
        $moderators = is_array($moderators) ? $moderators : [$moderators];
        $hm         = $helper->getConfig('msgtoshow');
        //$set        = smallworld_checkPrivateOrPublic();
        $mods       = implode(',', smallworld_array_flatten(array_unique($moderators), 0));
        $inspected  = $this->inspected();
        $perm       = $helper->getConfig('smallworldshowPoPubPage');
        $data       = []; // init data array
        if ('' == $mods && 0 == $inspected) { // nothing here
            return $data;
        }
        if (0 == $last) {
            $query = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_messages')
                     . ' M, '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_user')
                     . ' U WHERE M.uid_fk=U.userid AND M.uid_fk IN ('
                     . $mods
                     . ') AND M.uid_fk NOT IN ('
                     . $inspected
                     . ") AND M.priv = '0'";
        } elseif ($last > 0) {
            $query = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_messages')
                     . ' M, '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_user')
                     . ' U  WHERE M.uid_fk=U.userid AND M.uid_fk IN ('
                     . $mods
                     . ') AND M.uid_fk NOT IN ('
                     . $inspected
                     . ") AND M.priv = '0' AND M.msg_id < '"
                     . $last
                     . "' ORDER BY created DESC LIMIT {$hm}";
        } elseif ('a' == $last) {
            $query = 'SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_messages')
                     . ' M, '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_user')
                     . ' U  WHERE M.uid_fk=U.userid AND M.uid_fk IN ('
                     . $mods
                     . ') AND M.uid_fk NOT IN ('
                     . $inspected
                     . ") AND M.priv = '0'"
                     . " ORDER BY by M.msg_id DESC LIMIT {$hm}";
        }

        if ($last <= 0 || ('a' == $last)) {
            $query .= " ORDER BY created DESC LIMIT {$hm}";
        }

        $result = $GLOBALS['xoopsDB']->queryF($query);
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
        $data = [];  // initialize return array
        $inspected = $this->inspected();
        $query     = 'SELECT C.msg_id_fk, C.com_id, C.uid_fk, C.comment, C.created, U.username FROM '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_comments')
                     . ' C, '
                     . $GLOBALS['xoopsDB']->prefix('smallworld_user')
                     . " U WHERE C.uid_fk=U.userid AND C.msg_id_fk='"
                     . (int)$msg_id
                     . "' AND C.uid_fk NOT IN ("
                     . $inspected
                     . ') ORDER BY C.com_id ASC ';
        $result    = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get user image based on uid
     *
     * @param int $uid
     * @return string
     */
    public function Gravatar($uid)
    {
        $image  = $avatar = '';
        $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');
        $criteria = new \Criteria('userid', (int)$uid);
        $criteria->setLimit(1);
        $swUser = $swUserHandler->getAll($criteria, ['userimage'], false);
        if (0 < count($swUser)) {
            $image = $swUser['userimage'];
        }
        /*
        $sql    = 'SELECT userimage FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . (int)$uid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $image = $r['userimage'];
        }
        */
        if ('blank.gif' === $image) {
            $image = smallworld_getAvatarLink($uid, $image);
        }

        //$image = ($image == '' || $image == 'blank.gif') ? smallworld_getAvatarLink($uid, $image) : $image;

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
     * Count all votes
     *
     * @param int $type - not used
     * @param int $val -  not used
     * @param int $msgid
     * @return int
     */
    public function countVotes($type, $val, $msgid)
    {
        $sum    = 0;
        $query  = 'SELECT SUM(' . $val . ') AS sum FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE msg_id = '" . (int)$msgid . "' AND com_id = '0'";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $sum = $row['sum'];
        }

        return (int)$sum; // make sure it's an integer - not sure this is really necessary
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
        $sum    = 0;
        $query  = 'SELECT SUM(' . $val . ') AS sum FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '" . (int)$comid . "' AND msg_id = '" . (int)$msgid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $sum = $row['sum'];
        }

        return (int)$sum;
    }

    /**
     * @Check is user is friend
     * @param int    $userid
     * @param string $type
     * @param int    $comid
     * @param int    $msgid
     * @return int
     */
    public function hasVoted($userid, $type, $comid, $msgid)
    {
        if ('msg' === $type) {
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '0' AND msg_id = '" . (int)$msgid . "' AND user_id = '" . (int)$userid . "'";
        } else {
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '" . (int)$comid . "' AND msg_id = '" . (int)$msgid . "' AND user_id = '" . (int)$userid . "'";
        }
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);

        return $i;
    }

    /**
     * @count messages per user
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
     * Show permaling updates
     *
     * @todo ensure "something" is always returned
     * @param int $updid
     * @param int $uid
     * @param int $ownerID
     * @return array|bool
     */
    public function updatesPermalink($updid, $uid, $ownerID)
    {
        $query  = 'SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U  WHERE M.uid_fk=U.userid AND M.uid_fk='" . $ownerID . "'";
        $query  .= " AND M.msg_id = '" . $updid . "'";
        $query  .= ' order by M.created DESC LIMIT 1';
        $result = $GLOBALS['xoopsDB']->queryF($query);
        $count  = $GLOBALS['xoopsDB']->getRowsNum($result);
        if ($count < 1) {
            return false;
        }
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    /**
     * Get share link
     *
     * @todo ensure "something" is always returned
     * @param int $updid
     * @param int $ownerID
     * @return array|bool
     */
    public function updatesSharelink($updid, $ownerID)
    {
        $query  = 'SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' M, ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " U WHERE M.uid_fk=U.userid AND M.uid_fk='" . $ownerID . "' AND M.priv = 0";
        $query  .= " AND M.msg_id = '" . $updid . "'";
        $query  .= ' order by created DESC LIMIT 1';
        $result = $GLOBALS['xoopsDB']->queryF($query);
        $count  = $GLOBALS['xoopsDB']->getRowsNum($result);
        if ($count < 1) {
            return false;
        }
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data[] = $row;
        }
        if (!empty($data)) {
            return $data;
        }
    }

    /**
     * @Get sharing link
     * @param int $id
     * @param int $priv
     * @return string
     */
    public function getSharing($id, $priv)
    {
        $text = '';
        if (1 !== $priv) {
            $text = " | <span class='smallworld_share' id='smallworld_share'>";
            $text .= "<a class='share' id='share-page" . $id . "' href='javascript:void(0);'>" . _SMALLWORLD_SHARELINK . '</a></span>';
        }

        return $text;
    }

    /**
     * @Get content for sharing div
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
            $text = "<div style='display: none;' class='smallworld_bookmarks' id='share-page' name='share-page" . $id . "'>";
            $text .= "<span name='share-page" . $id . "' rel1='" . $desc . "' rel2= '" . $username . "' rel=" . $permalink . " id='basicBookmark' title='" . _SMALLWORLD_SHAREBOX_TITLE . "'>";
            $text .= '</span></div>';
        }

        return $text;
    }

    /**
     * Parse update and comments array to template for public updates
     *
     * @param array $updatesarray
     * @param int   $id
     * #return void
     */
    public function parsePubArray($updatesarray, $id)
    {
        /**
         * @var \XoopsModules\Smallworld\Helper $helper
         * @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler
         */
        $helper = Helper::getInstance();
        $wm                = [];
        $check             = new User();
        $swDB              = new SwDatabase();
        $profile           = $helper->getHandler('SwUser')->checkIfProfile($id);
        $myavatar          = $this->Gravatar($id);
        $myavatarlink      = smallworld_getAvatarLink($id, $myavatar);
        $myavatar_size     = smallworld_getImageSize(80, 100, $myavatarlink);
        $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 100);
        $user_img          = "<img src='" . smallworld_getAvatarLink($id, $myavatar) . "' id='smallworld_user_img' " . $myavatar_highwide . '>';

        $GLOBALS['xoopsTpl']->assign([
            'myavatar'          => $myavatar,
            'myavatarlink'      => $myavatarlink,
            'myavatar_highwide' => $myavatar_highwide,
            'avatar'            => $user_img
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
            $wm['avatar']          = $this->Gravatar($data['uid_fk']);
            $wm['avatar_link']     = smallworld_getAvatarLink($data['uid_fk'], $wm['avatar']);
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
                // Is comment user a friend ?
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
                $wc['myavatar']        = $this->Gravatar($id);
                $wc['myavatar_link']   = $myavatarlink;
                $wc['avatar_size']     = smallworld_getImageSize(80, 100, $wc['myavatar_link']);
                $wc['avatar_highwide'] = smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);
                $wc['cface']           = $this->Gravatar($cdata['uid_fk']);
                $wc['avatar_link']     = smallworld_getAvatarLink($cdata['uid_fk'], $wc['cface']);
                $wc['vote_up']         = $this->countVotesCom('com', 'up', $cdata['msg_id_fk'], $cdata['com_id']);
                $wc['vote_down']       = $this->countVotesCom('com', 'down', $cdata['msg_id_fk'], $cdata['com_id']);

                if (Constants::PROFILE_HAS_BOTH == $frC[0] || 1 == $USC['comments']) {
                    $GLOBALS['xoopsTpl']->append('comm', $wc);
                }
            }
        }
    }
}
