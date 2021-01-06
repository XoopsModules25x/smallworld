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
require_once XOOPS_ROOT_PATH . '/class/mail/xoopsmultimailer.php';

/**
 * Class Mail
 */
class Mail
{
    /** Function to send mails to users based on certain events
     *
     * $fromUserId = uid, $toUserId = uid
     * $event : 'register' = New user registration,
     * 	'complatint' = Complaint agains a wall message,
     *  'newavatar' = User has opload new avatar, 'commentToWM' = New comment to your update
     * Register, complaint, newavatar is sent only to site admin, commentToWM to owner user of wall update
     * Link is optional, defaul null. Could be a link to Userprofile, or singlepage wall update.
     * Itemtext is text from comments or complaints to be sent by mail..
     * Result: send mail, return true or false
     *
     * @param int         $fromUserId
     * @param int         $toUserId
     * @param string      $event
     * @param null|string $link
     * @param array       $data
     * @throws \phpmailerException
     * @return bool  true on success, false on failure
     */
    public function sendMails($fromUserId, $toUserId, $event, $link, array $data)
    {
        $date    = date('m-d-Y H:i:s', time());
        $mail    = new \XoopsMultiMailer();
        $wall    = new WallUpdates();
        $tpl     = new \XoopsTpl();
        $message = '';
        /**
         * \XoopsModules\Smallworld\Helper $helper
         * \XoopsModules\Smallworld\SwUserHandler $swUserHandler
         */
        $helper        = Helper::getInstance();
        $swUserHandler = $helper->getHandler('SwUser');

        // From and To user ids
        $fromXuser       = new \XoopsUser($fromUserId);
        $fromAvatar      = $swUserHandler->gravatar($fromUserId);
        $fromAvatarlink  = "<img class='left' src='" . $swUserHandler->getAvatarLink($fromUserId, $fromAvatar) . "' height='90px' width='90px'>";
        $toXuser         = new \XoopsUser($toUserId);
        $toAvatar        = $swUserHandler->gravatar($toUserId);
        $toAvatarlink    = "<img class='left' src='" . $swUserHandler->getAvatarLink($toUserId, $toAvatar) . "' height='90px' width='90px'>";
        // Sender's XOOPS username
        $sendName        = $fromXuser->getVar('uname');
        $sendNameUrl     = "<a href='" . $helper->url("userprofile.php?username={$sendName}") . "'>{$sendName}</a>";
        // Receiver's XOOPS username and email
        $receiveName     = $toXuser->getVar('uname');
        $receiveNameUrl  = "<a href='" . $helper->url("userprofile.php?username={$receiveName}") . "'>{$receiveName}</a>";

        // Checking content of 'event' to send right message
        switch ($event) {
            case ('register'):
                $subject = _SMALLWORLD_MAIL_REGISTERSUBJECT . $GLOBALS['xoopsConfig']['sitename'];

                $registername  = $sendName;
                $toAvatarlink = "<img class='left' src='" . $swUserHandler->getAvatarLink($fromUserId, $toAvatar) . "' height='90px' width='90px'>";

                $tpl = new \XoopsTpl();
                $tpl->assign([
                    'registername' => $registername,
                    'sitename'     => $GLOBALS['xoopsConfig']['sitename'],
                    'registerurl'  => $sendNameUrl,
                    'registerlink' => $toAvatarlink
                ]);

                $lnk        = $helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/mailTpl/mail_register.tpl');
                $message    = $tpl->fetch($lnk);
                $mail->Body = $message;
                $toMail     = $GLOBALS['xoopsConfig']['adminmail'];
                break;
            // Send email to admin if red/yellow card has been pressed indicating a "bad" thread has been found.
            case ('complaint'):
                $subject = _SMALLWORLD_MAIL_COMPLAINT . $GLOBALS['xoopsConfig']['sitename'];

                $senders_id  = $fromUserId;
                $sendersName = stripslashes($data['byuser']);
                $againstUser = stripslashes($data['a_user']);
                $time        = date('d-m-Y H:i:s', $data['time']);
                $link        = stripslashes($data['link']);

                $tpl = new \XoopsTpl();
                $tpl->assign('sendername', $sendersName);
                $tpl->assign('against', $againstUser);
                $tpl->assign('time', $time);
                $tpl->assign('link', $link);
                $tpl->assign('sitename', $GLOBALS['xoopsConfig']['sitename']);

                $lnk        = $helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/mailTpl/mail_complaint.tpl');
                $message    = $tpl->fetch($lnk);
                $mail->Body = $message;
                $toMail     = $GLOBALS['xoopsConfig']['adminmail'];
                break;
            case ('commentToWM'):
                $subject = _SMALLWORLD_MAIL_NEWCOMMENT . $GLOBALS['xoopsConfig']['sitename'];

                $ownermessage = stripslashes($this->getOwnerUpdateFromMsgID($data['msg_id_fk']));
                if (preg_match('/UPLIMAGE/', $ownermessage)) {
                    $ownmsg       = str_replace('UPLIMAGE ', '', $ownermessage);
                    $ownermessage = "<img width='300px' src='" . $ownmsg . "' style='margin: 5px 0px;' >";
                }

                $owner           = smallworld_getOwnerFromComment($data['msg_id_fk']);
                $ownerXuser      = new \XoopsUser($owner);
                $ownerAvatar     = $swUserHandler->gravatar($owner);
                $ownerAvatarlink = "<img class='left' src='" . $swUserHandler->getAvatarLink($owner, $ownerAvatar) . "' height='90px' width='90px'>";
                $ownerXname      = $ownerXuser->uname();
                $ownerXnameUrl   = "<a href='" . $helper->url("userprofile.php?username='{$ownerXname}") . "'>{$ownerXname}</a>";
                $replylink       = "<a href='" . $helper->url("permalink.php?ownerid={$owner}&updid={$data['msg_id_fk']}") . "'>" . _SMALLWORLD_SEEANDREPLYHERE . '</a>';

                $tpl = new \XoopsTpl();
                $tpl->assign([
                    'receivename'     => $receiveName,
                    'ownername'       => $ownerXname,
                    'ownernameurl'    => $ownerXnameUrl,
                    'sendname'        => $sendName,
                    'sendnameurl'     => $sendNameUrl,
                    'sitename'        => $GLOBALS['xoopsConfig']['sitename'],
                    'ownermessage'    => $ownermessage,
                    'from_avatarlink' => $fromAvatarlink,
                    'to_avatarlink'   => $ownerAvatarlink,
                    'itemtext'        => stripslashes($data['comment']),
                    'itemtextdate'    => $date,
                    'replylink'       => $replylink
                ]);
                $lnk        = $helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/mailTpl/mail_newcomment.tpl');
                $message    = $tpl->fetch($lnk);
                $mail->Body = $message;
                $toMail     = $toXuser->getVar('email');
                break;
            case ('friendshipfollow'):
                $subject = _SMALLWORLD_MAIL_NEWFRIENDFOLLOWER . $GLOBALS['xoopsConfig']['sitename'];
                $link    = "<a href='" . $helper->url('index.php') . "'>" . _SMALLWORLD_GOTOSMALLWORLDHERE . "</a>";

                $tpl = new \XoopsTpl();
                $tpl->assign([
                    'toUser'   => $receiveName,
                    'date'     => $date,
                    'link'     => $link,
                    'sitename' => $GLOBALS['xoopsConfig']['sitename']
                ]);

                $lnk        = $helper->url('language/' . $GLOBALS['xoopsConfig']['language'] . '/mailTpl/mail_attencionneeded.tpl');
                $message    = $tpl->fetch($lnk);
                $mail->Body = $message;
                $toMail     = $toXuser->getVar('email');
                break;
            case ('tag'):
                $subject = _SMALLWORLD_MAIL_FRIENDTAGGEDYOU . $GLOBALS['xoopsConfig']['sitename'];
                $tpl     = new \XoopsTpl();
                $tpl->assign([
                    'toUser'   => $receiveName,
                    'fromUser' => $sendName,
                    'date'     => $date,
                    'link'     => $link,
                    'sitename'  => $GLOBALS['xoopsConfig']['sitename']
                ]);

                $lnk        = $helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/mailTpl/mail_tag.tpl');
                $message    = $tpl->fetch($lnk);
                $mail->Body = $message;
                $toMail     = $toXuser->getVar('email');
                break;
        }

        $mail->isMail();
        $mail->isHTML(true);
        $mail->addAddress($toMail);
        $mail->Subject = $subject;

        $retVal = true;
        if (!$mail->send()) {
            //@todo figure out what to do if failure, if anything
            $retVal = false;
        }
        return $retVal;
    }

    /*
     From msg_id_fk get userids in the thread and return unique array
    */

    /**
     * @param $msg_id_fk
     * @return array
     */
    public function getPartsFromComment($msg_id_fk)
    {
        $parts  = [];
        $sql    = 'SELECT uid_fk FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE msg_id_fk = '" . $msg_id_fk . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $parts[] = $r['uid_fk'];
        }

        return array_unique($parts);
    }

    /**
     * @param $msgid
     * @return mixed
     */
    public function getOwnerUpdateFromMsgID($msgid)
    {
        $sql    = 'SELECT message FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE msg_id = '" . $msgid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $message = $r['message'];
        }

        return $message;
    }
}
