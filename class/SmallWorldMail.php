<?php namespace Xoopsmodules\smallworld;
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

require_once XOOPS_ROOT_PATH . '/class/mail/xoopsmultimailer.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';

/**
 * Class SmallWorldMail
 */
class SmallWorldMail
{
    /* Function to send mails to users based on certain events
         * $fromUserID = uid, $toUserID = uid
         * $event : 'register' = New user registration,
         * 	'complatint' = Complaint agains a wall message,
         *  'newavatar' = User has opload new avatar, 'commentToWM' = New comment to your update
         * Register, complaint, newavatar is sent only to site admin, commentToWM to owner user of wall update
         * Link is optional, defaul null. Could be a link to Userprofile, or singlepage wall update.
         * Itemtext is text from comments or complaints to be sent by mail..
         * Result: send mail, return true or false
         */

    /**
     * @param             $fromUserID
     * @param             $toUserID
     * @param             $event
     * @param null|string $link
     * @param array       $data
     * @throws \phpmailerException
     */
    public function sendMails($fromUserID, $toUserID, $event, $link = null, array $data)
    {
        global $xoopsConfig, $xoopsUser;
        $date    = date('m-d-Y H:i:s', time());
        $mail    = new \XoopsMultiMailer;
        $wall    = new WallUpdates;
        $tpl     = new \XoopsTpl();
        $message = '';

        // From and To user ids
        $FromUser        = new \XoopsUser($fromUserID);
        $from_avatar     = $wall->Gravatar($fromUserID);
        $from_avatarlink = "<img class='left' src='" . smallworld_getAvatarLink($fromUserID, $from_avatar) . "' height='90px' width='90px'>";

        $ToUser        = new \XoopsUser($toUserID);
        $To_avatar     = $wall->Gravatar($toUserID);
        $To_avatarlink = "<img class='left' src='" . smallworld_getAvatarLink($toUserID, $To_avatar) . "' height='90px' width='90px'>";
        // Senders username
        $SendName    = $FromUser->getVar('uname');
        $SendNameUrl = "<a href='" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $SendName . "'>" . $SendName . '</a>';

        // Recievers username and email
        $RecieveName    = $ToUser->getVar('uname');
        $RecieveNameUrl = "<a href='" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $RecieveName . "'>" . $RecieveName . '</a>';

        // Checking content of 'event' to send right message
        if ('register' === $event) {
            $subject = _SMALLWORLD_MAIL_REGISTERSUBJECT . $xoopsConfig['sitename'];

            $registername  = $SendName;
            $To_avatarlink = "<img class='left' src='" . smallworld_getAvatarLink($fromUserID, $To_avatar) . "' height='90px' width='90px'>";

            $tpl = new \XoopsTpl();
            $tpl->assign('registername', $registername);
            $tpl->assign('sitename', $xoopsConfig['sitename']);
            $tpl->assign('registerurl', $SendNameUrl);
            $tpl->assign('registerlink', $To_avatarlink);

            $lnk        = XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/mailTpl/mail_register.tpl';
            $message    = $tpl->fetch($lnk);
            $mail->Body = $message;
            $toMail     = $xoopsConfig['adminmail'];

            // Send email to admin if red/yellow card has been pressed indicating a "bad" thread has been found.
        } elseif ('complaint' === $event) {
            $subject = _SMALLWORLD_MAIL_COMPLAINT . $xoopsConfig['sitename'];

            $senders_id   = $fromUserID;
            $senders_name = stripslashes($data['byuser']);
            $against_user = stripslashes($data['a_user']);
            $time         = date('d-m-Y H:i:s', $data['time']);
            $link         = stripslashes($data['link']);

            $tpl = new \XoopsTpl();
            $tpl->assign('sendername', $senders_name);
            $tpl->assign('against', $against_user);
            $tpl->assign('time', $time);
            $tpl->assign('link', $link);
            $tpl->assign('sitename', $xoopsConfig['sitename']);

            $lnk        = XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/mailTpl/mail_complaint.tpl';
            $message    = $tpl->fetch($lnk);
            $mail->Body = $message;
            $toMail     = $xoopsConfig['adminmail'];
        } elseif ('commentToWM' === $event) {
            $subject = _SMALLWORLD_MAIL_NEWCOMMENT . $xoopsConfig['sitename'];

            $ownermessage = stripslashes($this->getOwnerUpdateFromMsgID($data['msg_id_fk']));
            if (preg_match('/UPLIMAGE/', $ownermessage)) {
                $ownmsg       = str_replace('UPLIMAGE ', '', $ownermessage);
                $ownermessage = "<img width='300px' src='" . $ownmsg . "' style='margin: 5px 0px;' >";
            }

            $owner            = Smallworld_getOwnerFromComment($data['msg_id_fk']);
            $OwnerUser        = new \XoopsUser($owner);
            $Owner_avatar     = $wall->Gravatar($owner);
            $Owner_avatarlink = "<img class='left' src='" . smallworld_getAvatarLink($owner, $Owner_avatar) . "' height='90px' width='90px'>";
            $OwnerName        = $OwnerUser->getVar('uname');
            $OwnerNameUrl     = "<a href='" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $OwnerName . "'>" . $OwnerName . '</a>';

            $replylink = "<a href='" . XOOPS_URL . '/modules/smallworld/permalink.php?ownerid=' . $owner . '&updid=' . $data['msg_id_fk'] . "'>";
            $replylink .= _SMALLWORLD_SEEANDREPLYHERE . '</a>';

            $tpl = new \XoopsTpl();
            $tpl->assign('recievename', $RecieveName);
            $tpl->assign('ownername', $OwnerName);
            $tpl->assign('ownernameurl', $OwnerNameUrl);
            $tpl->assign('sendname', $SendName);
            $tpl->assign('sendnameurl', $SendNameUrl);
            $tpl->assign('sitename', $xoopsConfig['sitename']);
            $tpl->assign('ownermessage', $ownermessage);
            $tpl->assign('from_avatarlink', $from_avatarlink);
            $tpl->assign('to_avatarlink', $Owner_avatarlink);
            $tpl->assign('itemtext', stripslashes($data['comment']));
            $tpl->assign('itemtextdate', $date);
            $tpl->assign('replylink', $replylink);
            $lnk        = XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/mailTpl/mail_newcomment.tpl';
            $message    = $tpl->fetch($lnk);
            $mail->Body = $message;

            $toMail = $ToUser->getVar('email');
        } elseif ('friendshipfollow' === $event) {
            $subject = _SMALLWORLD_MAIL_NEWFRIENDFOLLOWER . $xoopsConfig['sitename'];
            $link    = "<a href='" . XOOPS_URL . "/modules/smallworld/index.php'>";
            $link    .= _SMALLWORLD_GOTOSMALLWORLDHERE . '</a>';

            $tpl = new \XoopsTpl();
            $tpl->assign('toUser', $RecieveName);
            $tpl->assign('date', $date);
            $tpl->assign('link', $link);
            $tpl->assign('sitename', $xoopsConfig['sitename']);

            $lnk        = XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/mailTpl/mail_attencionneeded.tpl';
            $message    = $tpl->fetch($lnk);
            $mail->Body = $message;
            $toMail     = $ToUser->getVar('email');
        } elseif ('tag' === $event) {
            $subject = _SMALLWORLD_MAIL_FRIENDTAGGEDYOU . $xoopsConfig['sitename'];
            $tpl     = new \XoopsTpl();
            $tpl->assign('toUser', $RecieveName);
            $tpl->assign('fromUser', $SendName);
            $tpl->assign('date', $date);
            $tpl->assign('link', $link);
            $tpl->assign('sitename', $xoopsConfig['sitename']);

            $lnk        = XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/mailTpl/mail_tag.tpl';
            $message    = $tpl->fetch($lnk);
            $mail->Body = $message;
            $toMail     = $ToUser->getVar('email');
        }

        $mail->isMail();
        $mail->isHTML(true);
        $mail->addAddress($toMail);
        $mail->Subject = $subject;

        if (!$mail->send()) {
        } else {
        }
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
        global $xoopsDB;
        $parts  = [];
        $sql    = 'SELECT uid_fk FROM ' . $xoopsDB->prefix('smallworld_comments') . " WHERE msg_id_fk = '" . $msg_id_fk . "'";
        $result = $xoopsDB->queryF($sql);
        while ($r = $xoopsDB->fetchArray($result)) {
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
        global $xoopsDB;
        $sql    = 'SELECT message FROM ' . $xoopsDB->prefix('smallworld_messages') . " WHERE msg_id = '" . $msgid . "'";
        $result = $xoopsDB->queryF($sql);
        while ($r = $xoopsDB->fetchArray($result)) {
            $message = $r['message'];
        }
        return $message;
    }
}
