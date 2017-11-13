<?php
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

use Xmf\Request;
use Xoopsmodules\smallworld;
require_once __DIR__ . '/header.php';

global $xoopsUser;
require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;

if ($xoopsUser) {
    $check  = new smallworld\SmallWorldUser;
    $db     = new smallworld\SmallWorldDB;
    $mail   = new SmallWorldMail;
    $friend = $_POST['friend'];
    if (isset($_POST['stat'])) {
        $stat = $_POST['stat'];
    }
    $friendProfile = $check->CheckIfProfile($friend);
    $invitation    = $_POST['invitation'];
    $myUid         = $_POST['myUid'];
    $friendName    = $check->getName($friend);
    $yourName      = $check->getName($myUid);
    $USC           = json_decode($db->GetSettings($friend), true);

    if ('1' == $invitation) {
        if ($friendProfile >= 2) {
            $friendshipExists = $check->friendcheck($myUid, $friend);
            if (0 == $friendshipExists[0]) {
                $resultMsg = _SMALLWORLD_JSON_ADDFRIEND . $friendName . _SMALLWORLD_JSON_REQUEST_PENDING;
                if (0 != smallworld_GetModuleOption('smallworldusemailnotis', $repmodule = 'smallworld')) {
                    if (1 == $USC['notify']) {
                        $mail->sendMails($friend, $friend, 'friendshipfollow', $link = null, []);
                    }
                }
                $db->toogleFriendInvite($friendshipExists, $friend, $myUid);
                echo json_encode(['error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_CANCELFR_TEXT]);
            }

            if ($friendshipExists[0] > 0 and $friendshipExists[0] < 2) {
                $resultMsg = _SMALLWORLD_JSON_CANCEL_ADDFRIEND . $friendName;
                $db->toogleFriendInvite($friendshipExists, $friend, $myUid);
                echo json_encode(['error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT]);
            }
            if (2 == $friendshipExists[0]) {
                $resultMsg = _SMALLWORLD_JSON_DELETE_FRIEND_START . $friendName . _SMALLWORLD_JSON_DELETE_FRIEND_END;
                $db->toogleFriendInvite($friendshipExists, $friend, $myUid);
                echo json_encode(['error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT]);
            }
        } else {
            echo 'friend does not exist';
        }
    }
    if ('2' == $invitation) {
        if ($friendProfile >= 2) {
            // Used for followers
            $following = $check->following_or($myUid, $friend);

            if (0 == $following[0]) {
                $resultMsgFollow = _SMALLWORLD_JSON_FOLLOWINGFRIEND . $friendName . _SMALLWORLD_JSON_FOLLOWINGFRIEND_DESC;
                $db->toogleFollow($following[0], $myUid, $friend);
                echo json_encode(['error' => 'no', 'msg' => $resultMsgFollow, 'msgChange' => _SMALLWORLD_JSON_FLNO_TEXT]);
            }
            if ($following[0] > 0) {
                $resultMsgFollow = _SMALLWORLD_JSON_UNFOLLOWINGFRIEND . $friendName . _SMALLWORLD_JSON_UNFOLLOWINGFRIEND_DESC;
                $db->toogleFollow($following[0], $myUid, $friend);
                echo json_encode(['error' => 'no', 'msg' => $resultMsgFollow, 'msgChange' => _SMALLWORLD_JSON_FLYES_TEXT]);
            }
        }
    }
    if ('3' == $invitation) {
        if ($friendProfile >= 2) {
            // Used for accept/deny friendship requests
            if ($stat > 0) {
                // Friendship is accepted (update status in mysql)
                $db->SetFriendshitStat(1, $myUid, $friend);
                $acceptMsg = _SMALLWORLD_JSON_DELETE_FRIEND_START . $friendName . _SMALLWORLD_JSON_DELETE_FRIEND_END;
                echo json_encode(['error' => 'no', 'msg' => $acceptMsg, 'msgChange' => _SMALLWORLD_JSON_REMOVEFR_TEXT]);
            }
            if ($stat < 0) {
                // friendship is denied (delete from mysql)
                $db->SetFriendshitStat(-1, $myUid, $friend);
                $acceptMsg = _SMALLWORLD_JSON_ADDFRIEND . $friendName . _SMALLWORLD_JSON_REQUEST_PENDING;
                echo json_encode(['error' => 'no', 'msg' => $acceptMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT]);
            }
        }
    }
}
