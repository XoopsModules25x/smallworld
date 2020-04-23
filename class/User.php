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

//include_once $GLOBALS['xoops']->path('include/common.php');

use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

/**
 * Class User
 *
 */
class User
{
    /**
     * Check if user has profile
     *
     * Returns profile type:
     *  0= no XOOPS or SW profile,
     *  1= XOOPS user but no SW profile,
     *  2= has both
     *
     * @deprecated
     * @param int $userId  XOOPS user id
     * @return int
     */
    public function checkIfProfile($userId)
    {
        $depMsg = get_class() . __FUNCTION__ . " is deprecated, use SwUserHandler::" . __FUNCTION__ . " instead";
        if (isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
        } else {
            trigger_error($depMsg, E_USER_WARNING);
        }
        $userId = (int)$userId;
        $type   = Constants::PROFILE_NONE; // init profile type
        if (Constants::DEFAULT_UID < $userId) {
            // XOOPS user id - now check to see if it's a real user
            $xUser = new \XoopsUser($userId);
            if ($xUser instanceof \XoopsUser) {
                // valid XOOPS user, see if there's a SW profile for them
                $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');
                $userCount     = $swUserHandler->getCount(new \Criteria('userid', $userId));
                //$sql       = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . $userId . "'";
                //$result    = $GLOBALS['xoopsDB']->queryF($sql);
                //$userCount = $GLOBALS['xoopsDB']->getRowsNum($result);
                // If \XoopsUser but no smallworld profile set to XOOPS only, otherwise they have a SW profile too
                $type = (0 == $userCount) ? Constants::PROFILE_XOOPS_ONLY : Constants::PROFILE_HAS_BOTH;
            }
        }
        return $type;
    }

    /**
     * Create user
     *
     * @param int $userId
     * @return mixed
     */
    public function createUser($userId)
    {
        $xUser  = new \XoopsUser((int)$userId);
        $retVal = false;
        if ($xUser instanceof \XoopsUser) { // make sure this is a real XOOPS user
            $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');
            $swUser = $swUserHandler->create();
            $swUser->setVar('userid', (int)$userId);
            $retVal = $swUserHandler->insert($swUser);

            //$sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' (userid) VALUES (' . (int)$userId . ')';
            //$retVal = $GLOBALS['xoopsDB']->queryF($sql);
        }
        return $retVal;
    }

    /**
     * Check is user is smallworld user
     *
     * @todo - finish this method - it currently just assigns vars to template
     *
     * @return void
     */
    public function chkUser()
    {
        $greeting = '<br>';
        $greeting .= _SMALLWORLD_NOTYETUSER_GREETING . ' ' . $GLOBALS['xoopsUser']->uname() . '.<br><br>';
        $greeting .= _SMALLWORLD_NOTYETUSER_BOXTEXT;

        $GLOBALS['xoopsTpl']->assign('notyetusercontent', $greeting);
        $GLOBALS['xoopsTpl']->assign('check', 0);
    }

    /**
     * Check is user is friend
     *
     * @param int $user
     * @param int $userId
     * @return array
     */
    public function friendcheck($user, $userId)
    {
        $respons = [0 => '']; // init response
        if ($user == $userId) {
            $respons[0] = 2;
            return $respons;
        }
        $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . (int)$user . "' AND you = '" . (int)$userId . "' LIMIT 1";
        $result = $GLOBALS['xoopsDB']->query($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        if (0 == $i) {
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE you = '" . (int)$user . "' AND me = '" . (int)$userId . "' LIMIT 1";
            $result = $GLOBALS['xoopsDB']->query($sql);
            $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        }
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            if (0 == $i || '' == $i) {
                $respons[0] = 0;
            } elseif (1 == $i) {
                if (1 == $row['status']) {
                    $respons[0] = 1;
                } elseif (2 == $row['status']) {
                    $respons[0] = 2;
                }
            }
        }

        return $respons;
    }

    /**
     * Get name from userid
     *
     * @deprecated - moved to SwUserHandler::getName() method
     * @param int $userId
     * @return string
     */
    public function getName($userId)
    {
        $depMsg = get_class() . __FUNCTION__ . " is deprecated, use SwUserHandler::" . __FUNCTION__ . " instead";
        if (isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
        } else {
            trigger_error($depMsg, E_USER_WARNING);
        }
        $name = '';
        $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');
        $criteria = new \Criteria('userid', (int)$userId);
        $criteria->setLimit(1);
        $swUserArray = $swUserHandler->getAll($criteria, ['username'], false);
        if (0 < count($swUserArray)) {
            $swUser = array_pop($swUserArray);
            $name = $swUser['username'];
        }
        /*
        $sql    = 'SELECT username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . (int)$userId . "' LIMIT 1";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $name = $row['username'];
        }
        */
        return $name;
    }

    /**
     * Check if user is follower
     *
     * @param int $userId
     * @param int $friendId
     * @return array
     */
    public function following_or($userId, $friendId)
    {
        $respons = [0 => 0]; // init the array
        if ($userId != $friendId) {
            $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE me = '" . (int)$userId . "' AND you = '" . (int)$friendId . "'";
            $result = $GLOBALS['xoopsDB']->query($sql);
            $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                if (1 == $i) {
                    $respons[0] = (Constants::FRIEND_STATUS_PENDING == $row['status']) ? Constants::FRIEND_STATUS_PENDING : Constants::FRIEND_STATUS_APPROVED;
                }
            }
        }

        return $respons;
    }

    /**
     * Get requests
     *
     * @param int $userId
     * @return array
     */
    public function getRequests($userId)
    {
        $msg      = [];
        $sql      = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE you = '" . (int)$userId . "' AND status = '1'";
        $result   = $GLOBALS['xoopsDB']->queryF($sql);
        //$i        = $GLOBALS['xoopsDB']->getRowsNum($result);
        //$swDB     = new \XoopsModules\Smallworld\SwDatabase();
        //$wall     = new \XoopsModules\Smallworld\WallUpdates();
        /**
         * @var \XoopsModules\Smallworld\Helper $helper
         * @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler
         */
        $helper        = \XoopsModules\Smallworld\Helper::getInstance();
        $swUserHandler = $helper->getHandler('SwUser');
        $start         = 0;
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result)) && $start <= count($row)) {
            $msg[$start]['friendname']  = $swUserHandler->getName($row['me']);
            $msg[$start]['img']         = $swUserHandler->gravatar($row['me']);
            $msg[$start]['friendimage'] = "<img src='" . XOOPS_UPLOAD_URL . '/' . $msg[$start]['img'] . "' height='40px'>";
            $msg[$start]['frienddate']  = date('d-m-Y', $row['date']);
            $msg[$start]['accept']      = '<a class="smallworldrequestlink" id = "smallworldfriendrequest_' . $msg[$start]['friendname'] . '" href = "javascript:Smallworld_AcceptDenyFriend(1,' . $row['me'] . ',' . $row['you'] . ',' . $start . ');">' . _SMALLWORLD_ACCEPT . '</a>';
            $msg[$start]['deny']        = '<a class="smallworldrequestlink" id = "smallworldfriendrequest_' . $msg[$start]['friendname'] . '" href = "javascript:Smallworld_AcceptDenyFriend(-1,' . $row['me'] . ',' . $row['you'] . ',' . $start . ');">' . _SMALLWORLD_DENY . '</a>';
            $msg[$start]['later']       = '<a class="smallworldrequestlink" id = "smallworldfriendrequest_' . $msg[$start]['friendname'] . '" href = "javascript:Smallworld_AcceptDenyFriend(0,' . $row['me'] . ',' . $row['you'] . ',' . $start . ');">' . _SMALLWORLD_LATER . '</a>';
            $msg[$start]['cnt']         = $start;
            ++$start;
        }

        return $msg;
    }

    /**
     * Get partner
     *
     * @deprecated - replaced with SwUserHandler::spouseExists() method
     * @param string $name
     * @return int
     */
    public function spousexist($name)
    {
        $depMsg = get_class() . __FUNCTION__ . " is deprecated, use SwUserHandler::" . __FUNCTION__ . " instead";
        if (isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
        } else {
            trigger_error($depMsg, E_USER_WARNING);
        }
        $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');
        $exists = $swUserHandler->getCount(new \Criteria('username', $name));
        return $exists ? 1 : 0;
        /*
        $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE username = '" . $name . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);

        return $i;
        */
    }

    /**
     * Get all users
     *
     * @deprecated - functionality moved to SwUserHandler::allUsers()
     * @return array
     */
    public function allUsers()
    {
        $depMsg = get_class() . __FUNCTION__ . " is deprecated, use SwUserHandler::" . __FUNCTION__ . " instead";
        if (isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
        } else {
            trigger_error($depMsg, E_USER_WARNING);
        }
        $retVal = [];
        $sql    = 'SELECT userid FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' ORDER BY userid';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        if (0 !== $i) {
            $data = [];
            while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $data[] = $r;
            }
            $retVal = smallworld_array_flatten($data, 0);
        }

        return $retVal;
        //redirect_header(XOOPS_URL . "/modules/smallworld/register.php");
    }
}
