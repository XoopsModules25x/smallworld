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

//include_once $GLOBALS['xoops']->path('include/common.php');


class SmallWorldUser
{

    /**
     * @Check if user has profile
     * @param int $userID
     * @return int
     */
    public function checkIfProfile($userID)
    {
        global $xoopsUser, $xoopsDB;
        $i      = 0;
        $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE userid = '" . $userID . "'";
        $result = $xoopsDB->queryF($sql);
        $i      = $xoopsDB->getRowsNum($result);
        if ($xoopsUser) {
            // If xoopsuser but no smallworld profile
            if (0 == $i) {
                $i = 1;
            } else {
                // if xoopsuser and has profile
                $i = 2;
            }
        } else {
            // if not xoopsUser ie anonymous user
            $i = 0;
        }
        return $i;
    }

    /**
     * @Create user
     * @param int $userid
     * @return void
     */
    public function createUser($userid)
    {
        global $xoopsUser, $xoopsDB;
        $a      = new $xoopsUser($userid);
        $b      = $a->uname();
        $sql    = 'INSERT INTO ' . $xoopsDB->prefix('smallworld_user') . ' (userid) VALUES (' . (int)$userid . ')';
        $result = $xoopsDB->queryF($sql);
    }

    /**
     * @Check is user is smallworld user
     * @return void
     */
    public function chkUser()
    {
        global $xoopsUser, $xoopsTpl;
        $greeting = '<br>';
        $greeting .= _SMALLWORLD_NOTYETUSER_GREETING . ' ' . $xoopsUser->uname() . '.<br><br>';
        $greeting .= _SMALLWORLD_NOTYETUSER_BOXTEXT;

        $xoopsTpl->assign('notyetusercontent', $greeting);
        $xoopsTpl->assign('check', 0);
    }

    /**
     * @Check is user is friend
     * @param int $user
     * @param int $userID
     * @return array|int
     */
    public function friendcheck($user, $userID)
    {
        global $xoopsUser, $xoopsDB;
        $respons = [];
        if ($user == $userID) {
            $respons[0] = 2;
            return $respons;
        }
        $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE me = '" . (int)$user . "' AND you = '" . (int)$userID . "'";
        $result = $xoopsDB->query($sql);
        $i      = $xoopsDB->getRowsNum($result);
        if (0 == $i) {
            $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE you = '" . (int)$user . "' AND me = '" . (int)$userID . "'";
            $result = $xoopsDB->query($sql);
            $i      = $xoopsDB->getRowsNum($result);
        }
        while ($row = $xoopsDB->fetchArray($result)) {
            if (0 == $i and '' == $i) {
                $respons[0] = 0;
            }

            if (1 == $i and 1 == $row['status']) {
                $respons[0] = 1;
            }
            if (1 == $i and 2 == $row['status']) {
                $respons[0] = 2;
            }
            return $respons;
        }
    }

    /**
     * @Get name from userid
     * @param int $userID
     * @return string
     */
    public function getName($userID)
    {
        global $xoopsUser, $xoopsDB;
        $name = '';
        $sql    = 'SELECT username FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE userid = '" . (int)$userID . "'";
        $result = $xoopsDB->queryF($sql);
        while ($row = $xoopsDB->fetchArray($result)) {
            $name = $row['username'];
        }
        return $name;
    }

    /**
     * @Check if user is follower
     * @param int $userid
     * @param int $friendid
     * @return int
     */
    public function following_or($userid, $friendid)
    {
        global $xoopsDB, $xoopsUser;
        $respons[0] = 0;
        if ($userid != $friendid) {
            $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_followers') . " WHERE me = '" . (int)$userid . "' AND you = '" . (int)$friendid . "'";
            $result = $xoopsDB->query($sql);
            $i      = $xoopsDB->getRowsNum($result);
            while ($row = $xoopsDB->fetchArray($result)) {
                if (0 == $i) {
                    $respons[0] = 0;
                }

                if (1 == $i and 1 == $row['status']) {
                    $respons[0] = 1;
                }
                if (1 == $i and 2 == $row['status']) {
                    $respons[0] = 2;
                }
            }
        } else {
        }
        return $respons;
    }

    /**
     * @Get requests
     * @param int $userid
     * @return array
     */
    public function getRequests($userid)
    {
        global $xoopsDB, $xoopsUser;
        $msg      = [];
        $sql      = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_friends') . " WHERE you = '" . (int)$userid . "' AND status = '1'";
        $result   = $xoopsDB->queryF($sql);
        $i        = $xoopsDB->getRowsNum($result);
        $db       = new SmallWorldDB;
        $Wall     = new WallUpdates();
        $myavatar = $Wall->Gravatar($userid);
        $start    = 0;
        while ($row = $xoopsDB->fetchArray($result) and $start <= count($row)) {
            $msg[$start]['friendname']  = $this->getName($row['me']);
            $msg[$start]['img']         = $Wall->Gravatar($row['me']);
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
     * @Get partner
     * @param string $name
     * @return int
     */
    public function spousexist($name)
    {
        global $xoopsUser, $xoopsDB;
        $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE username = '" . $name . "'";
        $result = $xoopsDB->queryF($sql);
        $i      = $xoopsDB->getRowsNum($result);
        return $i;
    }

    /**
     * Get all users
     * @return array
     */

    public function allUsers()
    {
        global $xoopsDB;
        $sql    = 'SELECT userid FROM ' . $xoopsDB->prefix('smallworld_user') . ' ORDER BY userid';
        $result = $xoopsDB->queryF($sql);
        $i      = $xoopsDB->getRowsNum($result);
        if (0 != $i) {
            while ($r = $xoopsDB->fetchArray($result)) {
                $data[] = $r;
            }
            if (!empty($data)) {
                return Smallworld_array_flatten($data, 0);
            }
        } else {
            //redirect_header(XOOPS_URL . "/modules/smallworld/register.php");
        }
    }
}
