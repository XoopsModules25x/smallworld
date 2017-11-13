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
$id      = $xoopsUser ? $xoopsUser->getVar('uid') : 0;
$check   = new smallworld\SmallWorldUser;
$profile = $xoopsUser ? $check->CheckIfProfile($id) : 0;

if ($profile >= 2) {
    $Xuser    = new \XoopsUser($id);
    $username = $Xuser->getVar('uname');
    $Wall     = new smallworld\WallUpdates();
    $tpl      = new \XoopsTpl();
    $dBase    = new smallworld\SmallWorldDB;
    $mail     = new smallworld\SmallWorldMail;

    if (isset($_POST['update'])) {
        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $tpl->assign('isadminuser', 'YES');
        }

        $priv      = (int)$_POST['priv'];
        $followers = Smallworld_array_flatten($Wall->getFollowers($id), 0);

        $myavatar          = $Wall->Gravatar($id);
        $myavatarlink      = smallworld_getAvatarLink($id, $myavatar);
        $myavatar_size     = smallworld_getImageSize(80, 100, $myavatarlink);
        $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 35);

        $update  = $_POST['update'];
        $insdata = $Wall->Insert_Update($id, $update, $priv);
        if ($insdata) {
            //$updatesarray=$Wall->Updates('a', $id, $followers,$page);
            foreach ($insdata as $data) {
                $USW             = [];
                $USW['posts']    = 0;
                $USW['comments'] = 0;

                if ($xoopsUser) {
                    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) || $data['uid_fk'] == $id) {
                        $USW['posts']    = 1;
                        $USW['comments'] = 1;
                    } else {
                        $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                    }
                }

                if (!$xoopsUser) {
                    $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                }

                $wm['msg_id']          = $data['msg_id'];
                $wm['orimessage']      = (1 == $USW['posts'] || $profile >= 2) ? str_replace(["\r", "\n"], '', Smallworld_stripWordsKeepUrl($data['message'])) : '';
                $wm['message']         = (1 == $USW['posts'] || $profile >= 2) ? smallworld_tolink(htmlspecialchars_decode($data['message']), $data['uid_fk']) : _SMALLWORLD_MESSAGE_PRIVSETPOSTS;
                $wm['message']         = Smallworld_cleanup($wm['message']);
                $wm['created']         = smallworld_time_stamp($data['created']);
                $wm['username']        = $data['username'];
                $wm['uid_fk']          = $data['uid_fk'];
                $wm['priv']            = $data['priv'];
                $wm['avatar']          = $Wall->Gravatar($data['uid_fk']);
                $wm['avatar_link']     = smallworld_getAvatarLink($data['uid_fk'], $wm['avatar']);
                $wm['avatar_size']     = smallworld_getImageSize(80, 100, $wm['avatar_link']);
                $wm['avatar_highwide'] = smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);
                $wm['compl_msg_lnk']   = "<a href='" . XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . $data['uid_fk'];
                $wm['compl_msg_lnk']   .= '&updid=' . $data['msg_id'] . "'>" . _SMALLWORLD_COMP_MSG_LNK_DESC . '</a>';
                $wm['vote_up']         = $Wall->countVotes('msg', 'up', $data['msg_id']);
                $wm['vote_down']       = $Wall->countVotes('msg', 'down', $data['msg_id']);
                $wm['compl_msg_lnk']   = "<a href='" . XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . $data['uid_fk'];
                $wm['compl_msg_lnk']   .= '&updid=' . $data['msg_id'] . "'>" . _SMALLWORLD_COMP_MSG_LNK_DESC . '</a>';
                $wm['sharelinkurl']    = XOOPS_URL . '/modules/smallworld/smallworldshare.php?ownerid=' . $data['uid_fk'];
                $wm['sharelinkurl']    .= '&updid=' . $data['msg_id'] . '';
                $wm['usernameTitle']   = $wm['username'] . _SMALLWORLD_UPDATEONSITEMETA . $xoopsConfig['sitename'];
                $wm['linkimage']       = XOOPS_URL . '/modules/smallworld/assets/images/link.png';
                $wm['permalink']       = XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . $data['uid_fk'] . '&updid=' . $data['msg_id'];
                if (1 == $USW['posts'] || $profile >= 2) {
                    $wm['sharelink'] = $Wall->GetSharing($wm['msg_id'], $wm['priv']);
                } else {
                    $wm['sharelink'] = $Wall->GetSharing($wm['msg_id'], 1);
                }

                if (1 == $USW['posts'] || $profile >= 2) {
                    $wm['sharediv'] = $Wall->GetSharingDiv($wm['msg_id'], $wm['priv'], $wm['sharelinkurl'], $wm['orimessage'], $wm['usernameTitle']);
                } else {
                    $wm['sharediv'] = $Wall->GetSharingDiv($wm['msg_id'], 1, $wm['sharelinkurl'], $wm['orimessage'], $wm['usernameTitle']);
                }
                $wm['commentsarray'] = $Wall->Comments($data['msg_id']);

                //Send mail if tagged
                smallworld_getTagUsers($wm['message'], $wm['uid_fk'], $wm['permalink']);

                $tpl->append('walldata', $wm);

                if (!empty($wm['commentsarray'])) {
                    foreach ($wm['commentsarray'] as $cdata) {
                        $USC             = [];
                        $USC['posts']    = 0;
                        $USC['comments'] = 0;

                        if ($xoopsUser) {
                            if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) && $cdata['uid_fk'] == $id) {
                                $USC['posts']    = 1;
                                $USC['comments'] = 1;
                            } else {
                                $USC = json_decode($dBase->GetSettings($cdata['uid_fk']), true);
                            }
                        }

                        if (!$xoopsUser) {
                            $USC = json_decode($dBase->GetSettings($cdata['uid_fk']), true);
                        }

                        $wc['msg_id_fk']       = $cdata['msg_id_fk'];
                        $wc['com_id']          = $cdata['com_id'];
                        $wc['comment']         = (1 == $USC['comments'] || $profile >= 2) ? smallworld_tolink(htmlspecialchars_decode($cdata['comment']), $cdata['uid_fk']) : _SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
                        $wc['comment']         = Smallworld_cleanup($wc['comment']);
                        $wc['time']            = smallworld_time_stamp($cdata['created']);
                        $wc['username']        = $cdata['username'];
                        $wc['uid']             = $cdata['uid_fk'];
                        $wc['myavatar']        = $myavatar;
                        $wc['myavatar_link']   = $myavatarlink;
                        $wc['avatar_size']     = smallworld_getImageSize(80, 100, $wc['myavatar_link']);
                        $wc['avatar_highwide'] = smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);
                        $wc['cface']           = $Wall->Gravatar($cdata['uid_fk']);
                        $wc['avatar_link']     = smallworld_getAvatarLink($cdata['uid_fk'], $wc['cface']);
                        $wc['compl_msg_lnk']   = "<a href='" . XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . Smallworld_getOwnerFromComment($cdata['msg_id_fk']);
                        $wc['compl_msg_lnk']   .= '&updid=' . $cdata['msg_id_fk'] . '#' . $cdata['com_id'] . "'>" . _SMALLWORLD_COMP_MSG_LNK_DESC . '</a>';
                        $wc['vote_up']         = $Wall->countVotesCom('com', 'up', $cdata['msg_id_fk'], $cdata['com_id']);
                        $wc['vote_down']       = $Wall->countVotesCom('com', 'down', $cdata['msg_id_fk'], $cdata['com_id']);
                        $tpl->append('comm', $wc);
                    }
                }
            }
        }
        $tpl->assign('myusername', $username);
        $tpl->assign('pagename', $page);
        $tpl->assign('myavatar', $myavatar);
        $tpl->assign('myavatarlink', $myavatarlink);
        $tpl->assign('myavatar_highwide', $myavatar_highwide);
        $tpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/getlastmsg.html');
    }
} else {
    header('HTTP/1.1 403 Forbidden');
}
