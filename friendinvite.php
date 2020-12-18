<?php
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

use Xmf\Request;
use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
$GLOBALS['xoopsLogger']->activated = false;

$friend        = Request::getInt('friend', 0, 'POST');
$check         = new Smallworld\SmallWorldUser();
//$friendProfile = $check->checkIfProfile($friend);
/** @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler */
$swUserHandler = $helper->getHandler('SwUser');
$friendProfile = $swUserHandler->checkIfProfile($friend);

if (Constants::PROFILE_HAS_BOTH > $friendProfile) {
    $helper->redirect('index.php', 3, _SMALLWORLD_NOUSER_ERROR);
}

$swDB       = new Smallworld\SmallWorldDB();
$mail       = new Smallworld\Mail();
$stat       = Request::getInt('stat', 0, 'POST');
$invitation = Request::getInt('invitation', 0, 'POST');
$myUid      = Request::getInt('myUid', 0, 'POST');
$friendName = $swUserHandler->getName($friend);
$yourName   = $swUserHandler->getName($myUid);
$USC        = json_decode($swDB->getSettings($friend), true);

switch ($invitation) {
    case 1:
        $friendshipExists = $check->friendcheck($myUid, $friend);
        if (0 == $friendshipExists[0]) {
            $resultMsg = _SMALLWORLD_JSON_ADDFRIEND . $friendName . _SMALLWORLD_JSON_REQUEST_PENDING;
            if (0 != $helper->getConfig('smallworldusemailnotis')) {
                if (1 == $USC['notify']) {
                    $mail->sendMails($friend, $friend, 'friendshipfollow', $link = null, []);
                }
            }
            $swDB->toogleFriendInvite($friendshipExists, $friend, $myUid);
            echo json_encode(['error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_CANCELFR_TEXT]);
        }

        if ($friendshipExists[0] > 0 and $friendshipExists[0] < 2) {
            $resultMsg = _SMALLWORLD_JSON_CANCEL_ADDFRIEND . $friendName;
            $swDB->toogleFriendInvite($friendshipExists, $friend, $myUid);
            echo json_encode(['error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT]);
        }
        if (Constants::PROFILE_HAS_BOTH == $friendshipExists[0]) {
            $resultMsg = _SMALLWORLD_JSON_DELETE_FRIEND_START . $friendName . _SMALLWORLD_JSON_DELETE_FRIEND_END;
            $swDB->toogleFriendInvite($friendshipExists, $friend, $myUid);
            echo json_encode(['error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT]);
        }
        break;
    case 2:
        // Used for followers
        $following = $check->following_or($myUid, $friend);

        if (0 == $following[0]) {
            $resultMsgFollow = _SMALLWORLD_JSON_FOLLOWINGFRIEND . $friendName . _SMALLWORLD_JSON_FOLLOWINGFRIEND_DESC;
            $swDB->toogleFollow($following[0], $myUid, $friend);
            echo json_encode(['error' => 'no', 'msg' => $resultMsgFollow, 'msgChange' => _SMALLWORLD_JSON_FLNO_TEXT]);
        }
        if (0 < $following[0]) {
            $resultMsgFollow = _SMALLWORLD_JSON_UNFOLLOWINGFRIEND . $friendName . _SMALLWORLD_JSON_UNFOLLOWINGFRIEND_DESC;
            $swDB->toogleFollow($following[0], $myUid, $friend);
            echo json_encode(['error' => 'no', 'msg' => $resultMsgFollow, 'msgChange' => _SMALLWORLD_JSON_FLYES_TEXT]);
        }
        break;
    case 3:
        // Used for accept/deny friendship requests
        if ($stat > 0) {
            // Friendship is accepted (update status in mysql)
            $swDB->setFriendshitStat(1, $myUid, $friend);
            $acceptMsg = _SMALLWORLD_JSON_DELETE_FRIEND_START . $friendName . _SMALLWORLD_JSON_DELETE_FRIEND_END;
            echo json_encode(['error' => 'no', 'msg' => $acceptMsg, 'msgChange' => _SMALLWORLD_JSON_REMOVEFR_TEXT]);
        }
        if ($stat < 0) {
            // friendship is denied (delete from mysql)
            $swDB->setFriendshitStat(-1, $myUid, $friend);
            $acceptMsg = _SMALLWORLD_JSON_ADDFRIEND . $friendName . _SMALLWORLD_JSON_REQUEST_PENDING;
            echo json_encode(['error' => 'no', 'msg' => $acceptMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT]);
        }
        break;
}
