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

require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';
global $xoopsUser, $xoopsModule, $xoopsLogger, $xoopsTpl;
$xoopsLogger->activated = false;
//error_reporting(E_ALL);
$page    = 'index';
$check   = new smallworld\SmallWorldUser;
$id      = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$profile = $xoopsUser ? $check->CheckIfProfile($id) : 0;

if ($profile >= 2) {
    $Xuser    = new XoopsUser($id);
    $username = $Xuser->getVar('uname');
    $Wall     = new smallworld\WallUpdates();
    $tpl      = new XoopsTpl();
    $mail     = new smallworld\SmallWorldMail;
    $dBase    = new smallworld\SmallWorldDB;

    if (isset($_POST['comment'])) {
        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $tpl->assign('isadminuser', 'YES');
        }

        $followers = Smallworld_array_flatten($Wall->getFollowers($id), 0);

        $myavatar     = $Wall->Gravatar($id);
        $myavatarlink = smallworld_getAvatarLink($id, $myavatar);

        // Get posted items
        $comment = $_POST['comment'];
        $msg_id  = $_POST['msg_id'];

        $data = $Wall->Insert_Comment($id, $msg_id, $comment);
        if ($data) {

            // Is comments's user a friend ?
            $frC = $check->friendcheck($id, $data['uid_fk']);

            $USC             = [];
            $USC['posts']    = 0;
            $USC['comments'] = 0;
            //$USC['notify'] = 0;

            if ($xoopsUser) {
                if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) || $data['uid_fk'] == $id) {
                    $USC['posts']    = 1;
                    $USC['comments'] = 1;
                    $frC[0]          = 2;
                } else {
                    $USC = json_decode($dBase->GetSettings($data['uid_fk']), true);
                }
            }

            if (!$xoopsUser) {
                $USC = json_decode($dBase->GetSettings($data['uid_fk']), true);
            }

            $wc['msg_id_fk']       = $data['msg_id_fk'];
            $wc['com_id']          = $data['com_id'];
            $wc['comment']         = (1 == $USC['comments'] || 2 == $frC[0]) ? smallworld_tolink(htmlspecialchars_decode($data['comment']), $data['uid_fk']) : _SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
            $wc['comment']         = Smallworld_cleanup($wc['comment']);
            $wc['time']            = smallworld_time_stamp($data['created']);
            $wc['username']        = $data['username'];
            $wc['uid']             = $data['uid_fk'];
            $wc['myavatar']        = $myavatar;
            $wc['myavatar_link']   = $myavatarlink;
            $wc['cface']           = $Wall->Gravatar($data['uid_fk']);
            $wc['avatar_link']     = smallworld_getAvatarLink($data['uid_fk'], $wc['cface']);
            $wc['avatar_size']     = smallworld_getImageSize(80, 100, $wc['myavatar_link']);
            $wc['avatar_highwide'] = smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);
            $wc['compl_msg_lnk']   = "<a href='" . XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . Smallworld_getOwnerFromComment($data['msg_id_fk']);
            $wc['compl_msg_lnk']   .= '&updid=' . $data['msg_id_fk'] . '#' . $data['com_id'] . "'>" . _SMALLWORLD_COMP_MSG_LNK_DESC . '</a>';
            $wc['vote_up']         = $Wall->countVotesCom('com', 'up', $data['msg_id_fk'], $data['com_id']);
            $wc['vote_down']       = $Wall->countVotesCom('com', 'down', $data['msg_id_fk'], $data['com_id']);

            //Send mail if tagged
            $permalink = XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . $data['uid_fk'] . '&updid=' . $data['msg_id_fk'];
            smallworld_getTagUsers($wc['comment'], $wc['uid'], $permalink);

            $tpl->append('comments', $wc);

            $tpl->assign('myusername', $username);
            $tpl->assign('pagename', $page);
            $tpl->assign('myavatar', $myavatar);
            $tpl->assign('myavatarlink', $myavatarlink);
            $tpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/getlastcom.tpl');

            // send mail to user owning update + participans in the thread that a comment has been posted
            $parts  = $mail->getPartsFromComment($data['msg_id_fk']);
            $emails = '';
            foreach ($parts as $k => $v) {
                $owner = Smallworld_getOwnerFromComment($data['msg_id_fk']);
                // Get owner of posts settings in order to send mail or not!
                $owner_privset = json_decode($dBase->GetSettings($v), true);
                if (0 != smallworld_GetModuleOption('smallworldusemailnotis', $repmodule = 'smallworld')) {
                    if (1 == $owner_privset['notify']) {
                        $mail->sendMails($data['uid_fk'], $v, 'commentToWM', $link = null, $wc);
                    }
                }
            }
        }
    }
}
