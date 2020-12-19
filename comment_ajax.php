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
require_once XOOPS_ROOT_PATH . '/class/template.php';

$GLOBALS['xoopsLogger']->activated = false;
//error_reporting(E_ALL);

$page    = 'index';
/** @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler */
$swUserHandler = $helper->getHandler('SwUser');
$check   = new Smallworld\User();
$id      = $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser']->getVar('uid') : Constants::DEFAULT_UID;
//$profile = $GLOBALS['xoopsUserl'] ? $check->checkIfProfile($id) : Constants::PROFILE_NONE;
$profile = $swUserHandler->checkIfProfile($id);

if ($profile >= Constants::PROFILE_HAS_BOTH) {
    $Xuser    = new \XoopsUser($id);
    $username = $Xuser->getVar('uname');
    $wall     = new Smallworld\WallUpdates();
    $tpl      = new \XoopsTpl();
    $mail     = new Smallworld\Mail();
    $swDB     = new Smallworld\SwDatabase();

    if (isset($_POST['comment'])) {
        $tpl->assign('isadminuser', $helper->isUserAdmin() ? 'YES' : 'NO');

        $followers    = smallworld_array_flatten($wall->getFollowers($id), 0);
        $myavatar     = $swUserHandler->gravatar($id);
        $myavatarlink = $swUserHandler->getAvatarLink($id, $myavatar);

        // Get posted items
        $comment = Request::getString('comment', '', 'POST');
        $msg_id  = Request::getInt('msg_id', Constants::DEFAULT_MESSAGEID, 'POST');
        $data    = $wall->insertComment($id, $msg_id, $comment);
        if ($data) {
            // Is comments' user a friend ?
            $frC = $check->friendcheck($id, $data['uid_fk']);
            $USC = ['posts' => 0, 'comments' => 0];

            if ($helper->isUserAdmin() || $data['uid_fk'] == $id) {
                    $USC['posts']    = 1;
                    $USC['comments'] = 1;
                    $frC[0]          = Constants::PROFILE_HAS_BOTH;
            } else {
                $USC = json_decode($swDB->getSettings($data['uid_fk']), true);
            }

            $wc['msg_id_fk']       = $data['msg_id_fk'];
            $wc['com_id']          = $data['com_id'];
            $wc['comment']         = (1 == $USC['comments'] || Constants::PROFILE_HAS_BOTH == $frC[0]) ? smallworld_tolink(htmlspecialchars_decode($data['comment']), $data['uid_fk']) : _SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
            $wc['comment']         = smallworld_cleanup($wc['comment']);
            $wc['time']            = smallworld_time_stamp($data['created']);
            $wc['username']        = $data['username'];
            $wc['uid']             = $data['uid_fk'];
            $wc['myavatar']        = $myavatar;
            $wc['myavatar_link']   = $myavatarlink;
            $wc['cface']           = $swUserHandler->gravatar($data['uid_fk']);
            $wc['avatar_link']     = $swUserHandler->getAvatarLink($data['uid_fk'], $wc['cface']);
            $wc['avatar_size']     = smallworld_getImageSize(80, 100, $wc['myavatar_link']);
            $wc['avatar_highwide'] = smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);
            $wc['compl_msg_lnk']   = "<a href='" . $helper->url('permalink.php?ownerid=' . smallworld_getOwnerFromComment($data['msg_id_fk']));
            $wc['compl_msg_lnk']   .= '&updid=' . $data['msg_id_fk'] . '#' . $data['com_id'] . "'>" . _SMALLWORLD_COMP_MSG_LNK_DESC . '</a>';
            $wc['vote_up']         = $wall->countVotesCom('com', 'up', $data['msg_id_fk'], $data['com_id']);
            $wc['vote_down']       = $wall->countVotesCom('com', 'down', $data['msg_id_fk'], $data['com_id']);

            //Send mail if tagged
            $permalink = $helper->url('permalink.php?ownerid=' . $data['uid_fk'] . '&updid=' . $data['msg_id_fk']);
            smallworld_getTagUsers($wc['comment'], $wc['uid'], $permalink);

            $tpl->append('comments', $wc);

            $tpl->assign([
                'myusername'   => $username,
                'pagename'     => $page,
                'myavatar'     => $myavatar,
                'myavatarlink' => $myavatarlink
            ]);
            $tpl->display($helper->path('templates/getlastcom.tpl'));

            // send mail to user owning update + participans in the thread that a comment has been posted
            $parts  = $mail->getPartsFromComment($data['msg_id_fk']);
            foreach ($parts as $v) {
                $owner = smallworld_getOwnerFromComment($data['msg_id_fk']);
                // Get owner of posts settings in order to send mail or not!
                $owner_privset = json_decode($swDB->getSettings($v), true);
                if (0 !== $helper->getConfig('smallworldusemailnotis')) {
                    if (1 == $owner_privset['notify']) {
                        $mail->sendMails($data['uid_fk'], $v, 'commentToWM', null, $wc);
                    }
                }
            }
        }
    }
}
