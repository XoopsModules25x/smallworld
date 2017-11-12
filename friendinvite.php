<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  :            {@link https://xoops.org 2001-2017 XOOPS Project}
 * @license    :                {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * @module     :                Smallworld
 * @Author     :                Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @copyright  :            2011 Culex
 * @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/friendinvite.php $
 * @Last       committed:        $Revision: 11992 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2013-08-31 20:06:22 +0200 (lø, 31 aug 2013) $
 * @ID         :                    $Id: friendinvite.php 11992 2013-08-31 18:06:22Z djculex $
 **/
global $xoopsUser;
include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH . "/modules/smallworld/include/functions.php");
global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;

if ($xoopsUser) {
    $check  = new SmallWorldUser;
    $db     = new SmallWorldDB;
    $mail   = new smallworld_mail;
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

    if ($invitation == '1') {
        if ($friendProfile >= 2) {

            $friendshipExists = $check->friendcheck($myUid, $friend);
            if ($friendshipExists[0] == 0) {
                $resultMsg = _SMALLWORLD_JSON_ADDFRIEND . $friendName . _SMALLWORLD_JSON_REQUEST_PENDING;
                if (smallworld_GetModuleOption('smallworldusemailnotis', $repmodule = 'smallworld') != 0) {
                    if ($USC['notify'] == 1) {
                        $mail->sendMails($friend, $friend, 'friendshipfollow', $link = null, array());
                    }
                }
                $db->toogleFriendInvite($friendshipExists, $friend, $myUid);
                echo json_encode(array('error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_CANCELFR_TEXT));
            }

            if ($friendshipExists[0] > 0 AND $friendshipExists[0] < 2) {
                $resultMsg = _SMALLWORLD_JSON_CANCEL_ADDFRIEND . $friendName;
                $db->toogleFriendInvite($friendshipExists, $friend, $myUid);
                echo json_encode(array('error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT));
            }
            if ($friendshipExists[0] == 2) {
                $resultMsg = _SMALLWORLD_JSON_DELETE_FRIEND_START . $friendName . _SMALLWORLD_JSON_DELETE_FRIEND_END;
                $db->toogleFriendInvite($friendshipExists, $friend, $myUid);
                echo json_encode(array('error' => 'no', 'msg' => $resultMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT));
            }
        } else {
            echo "friend does not exist";
        }
    }
    if ($invitation == '2') {
        if ($friendProfile >= 2) {
            // Used for followers
            $following = $check->following_or($myUid, $friend);

            if ($following[0] == 0) {
                $resultMsgFollow = _SMALLWORLD_JSON_FOLLOWINGFRIEND . $friendName . _SMALLWORLD_JSON_FOLLOWINGFRIEND_DESC;
                $db->toogleFollow($following[0], $myUid, $friend);
                echo json_encode(array('error' => 'no', 'msg' => $resultMsgFollow, 'msgChange' => _SMALLWORLD_JSON_FLNO_TEXT));
            }
            if ($following[0] > 0) {
                $resultMsgFollow = _SMALLWORLD_JSON_UNFOLLOWINGFRIEND . $friendName . _SMALLWORLD_JSON_UNFOLLOWINGFRIEND_DESC;
                $db->toogleFollow($following[0], $myUid, $friend);
                echo json_encode(array('error' => 'no', 'msg' => $resultMsgFollow, 'msgChange' => _SMALLWORLD_JSON_FLYES_TEXT));
            }

        }
    }
    if ($invitation == '3') {
        if ($friendProfile >= 2) {
            // Used for accept/deny friendship requests
            if ($stat > 0) {
                // Friendship is accepted (update status in mysql)
                $db->SetFriendshitStat(1, $myUid, $friend);
                $acceptMsg = _SMALLWORLD_JSON_DELETE_FRIEND_START . $friendName . _SMALLWORLD_JSON_DELETE_FRIEND_END;
                echo json_encode(array('error' => 'no', 'msg' => $acceptMsg, 'msgChange' => _SMALLWORLD_JSON_REMOVEFR_TEXT));
            }
            if ($stat < 0) {
                // friendship is denied (delete from mysql)
                $db->SetFriendshitStat(-1, $myUid, $friend);
                $acceptMsg = _SMALLWORLD_JSON_ADDFRIEND . $friendName . _SMALLWORLD_JSON_REQUEST_PENDING;
                echo json_encode(array('error' => 'no', 'msg' => $acceptMsg, 'msgChange' => _SMALLWORLD_JSON_ADDFR_TEXT));
            }
        }
    }
}
?>
