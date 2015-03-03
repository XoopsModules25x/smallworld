<?php
/**
* You may not change or alter any portion of this comment or credits
* of supporting developers from this source code or any supporting source code
* which is considered copyrighted (c) material of the original comment or credit authors.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* @copyright:            The XOOPS Project http://sourceforge.net/projects/xoops/
* @license:                http://www.fsf.org/copyleft/gpl.html GNU public license
* @module:                Smallworld
* @Author:                Michael Albertsen (http://culex.dk) <culex@culex.dk>
* @copyright:            2011 Culex
* @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/class/wall.php $
* @Last committed:        $Revision: 12114 $
* @Last changed by:        $Author: djculex $
* @Last changed date:    $Date: 2013-10-01 13:11:18 -0400 (Tue, 01 Oct 2013) $
* @ID:                    $Id: wall.php 12114 2013-10-01 17:11:18Z djculex $
**/

// Moderrated and fitted from the tutorial by Srinivas Tamada http://9lessons.info

class Wall_Updates
{
    private function getAdminModerators()
    {
        global $xoopsDB, $xoopsUser;
        $sql = "SELECT userid
                FROM ".$xoopsDB->prefix('smallworld_user')." su
                left JOIN ".$xoopsDB->prefix('groups_users_link')." xu ON su.userid = xu.uid
                WHERE xu.uid in (1)";
        $result = $xoopsDB->queryF($sql);
        while ($row = $xoopsDB->fetchArray($result)) {
            $data[]=$row;
        }

        return $data;
    }
    
    public function Updates($last,$uid, $followers) {
        global $xoopsUser, $xoopsDB, $moduleConfig;
        $hm = smallworld_GetModuleOption('msgtoshow');
        $set = smallworld_checkPrivateOrPublic ();
        $followers = (is_array($followers)) ? $followers : array($followers);
        $followers = array_unique(Smallworld_array_flatten($followers,0));

        $i=0;
        if ($last == 0) {
            $query = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM ".$xoopsDB->prefix('smallworld_messages')
                . " M, ".$xoopsDB->prefix('smallworld_user')." U  WHERE M.uid_fk=U.userid and M.uid_fk='".$uid."'";
        } elseif ($last > 0) {
            $query = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM "
                . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
                . " U  WHERE M.uid_fk=U.userid and M.uid_fk='".$uid."' and M.msg_id < '".$last."'";
        } elseif ($last == 'a') {
            $query = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM "
                . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
                . " U  WHERE M.uid_fk=U.userid and M.uid_fk='".$uid."'";
        }
               
        if (is_array($followers)){
            foreach ($followers as $k => $v) {
                if ($last>0) {
                    $query .= " OR M.uid_fk=U.userid and M.uid_fk= '".$v."' and M.msg_id < '".$last."'";
                } elseif ($last == 0) {
                    $query .= " OR M.uid_fk=U.userid and M.uid_fk= '".$v."'";
                } elseif ($last == 'a') {
                    $query .= " OR M.uid_fk=U.userid and M.uid_fk= '".$v."'";
                }
                $i++;
            }
        }
        if (!is_array($followers)) {
            $followers = $uid;
            if ($last > 0) {
                    $query .= " OR M.uid_fk=U.userid and M.uid_fk= '".$followers."' and M.msg_id < '".$last."'";
            } elseif ($last == 0) {
                $query .= " OR M.uid_fk=U.userid and M.uid_fk= '".$followers."'";
            } elseif ($last == 'a') {
                $query .= " OR M.uid_fk=U.userid and M.uid_fk= '".$followers."'";
            }
        }
        
        if ($last>0) {
            $query .= " order by created DESC LIMIT ".$hm;
        } elseif ($last == 'a') {
                $query .= " order by M.msg_id DESC LIMIT ".$hm;
        } else {
            $query .= " order by created DESC LIMIT ".$hm;
        }
        $result=$xoopsDB->queryF($query);
        $count = $xoopsDB->getRowsNum($result);
        if ($count == 0) {
            return false;
        } else {
            while ($row = $xoopsDB->fetchArray($result)) {
                $data[]=$row;
            }
            if(!empty($data)) {
                return $data;
            }
        }
   }

    /**
     * @Get comments based on msg id
     * @param  int   $msg_id
     * @return array
     */
    public function Comments($msg_id)
    {
        global $xoopsUser, $xoopsDB;
        $query = "SELECT C.msg_id_fk, C.com_id, C.uid_fk, C.comment, C.created, U.username FROM "
            . $xoopsDB->prefix('smallworld_comments')." C, ".$xoopsDB->prefix('smallworld_user')
            . " U WHERE C.uid_fk=U.userid and C.msg_id_fk='".$msg_id."' order by C.com_id asc ";
        $result = $xoopsDB->queryF($query);
        $i = $xoopsDB->getRowsNum($result);
        while ($row = $xoopsDB->fetchArray($result)) {
            $data[]=$row;
        }
        if(!empty($data)) {
            return $data;
        }
    }
    
    /**
     * @Get user image based on uid
     * @param  int    $uid
     * @return string
     */
    public function Gravatar($uid)
    {
        global $xoopsUser, $xoopsDB;
        $image='';
        $sql = "SELECT userimage FROM ".$xoopsDB->prefix('smallworld_user')." WHERE userid = '".$uid."'";
        $result = $xoopsDB->queryF($sql);
        while ($r = $xoopsDB->fetchArray($result)) {
            $image = $r['userimage'];
        }
        
        $image = ($image == '' || $image == 'blank.gif') ? smallworld_getAvatarLink($uid, $image) : $image;
        
        $type = Array(
                    1 => 'jpg',
                    2 => 'jpeg',
                    3 => 'png',
                    4 => 'gif'
                );
        
        $ext = explode(".",$image);
        
        if (@!in_array(strtolower ($ext[1]), $type) || $image == '') {
            $avatar = '';
        } else {
            $avatar = $image;
        }

        return $avatar;
    }
    
    /**
     * @Insert update
     * @param  int    $uid
     * @param  string $update
     * @param  int    $priv
     * @return array
     */
    public function Insert_Update($uid, $update,$priv)
    {
        global $xoopsUser, $xoopsDB;
        $update=Smallworld_sanitize(htmlentities($update, ENT_QUOTES,"UTF-8"));
        $time=time();
        if (!isset($priv)) {
            $priv = 0;
        }
        $query = "SELECT msg_id,message FROM ".$xoopsDB->prefix('smallworld_messages')
            . " WHERE uid_fk='".$uid."' order by msg_id desc limit 1";
        $result = $xoopsDB->queryF($query);
        if ($update!=$result['message']) {
            $query = "INSERT INTO ".$xoopsDB->prefix('smallworld_messages')
                . " (message, uid_fk, priv, created) VALUES ('".$update."', '".$uid."', '".$priv."', '".$time."')";
            $result = $xoopsDB->queryF($query);
            $newquery = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM "
                . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
                . " U where M.uid_fk=U.userid and M.uid_fk='".$uid."' order by M.msg_id desc limit 1 ";
            $result2 = $xoopsDB->queryF($newquery);
            while ($row = $xoopsDB->fetchArray($result2)) {
                $data[]=$row;
            }
            $count = $xoopsDB->getRowsNum($result2);
            if ($count < 1) {
                return false;
            } else {
                while ($row = $xoopsDB->fetchArray($result2)) {
                    $data[]=$row;
                }
                if(!empty($data)) {
                    return $data;
                }
            }
        }
    }
    
    /**
     * @Insert comment
     * @param  int    $uid
     * @param  int    $msgid
     * @param  string $comment
     * @return string / void
     */
    public function Insert_Comment($uid,$msg_id,$comment)
    {
        global $xoopsUser, $xoopsDB;
        $comment=Smallworld_sanitize(htmlentities($comment, ENT_QUOTES,"UTF-8"));
           $time=time();
        $query = "SELECT com_id,comment FROM ".$xoopsDB->prefix('smallworld_comments')
            . " WHERE uid_fk='".$uid."' and msg_id_fk='".$msg_id."' order by com_id desc limit 1 ";
        $result = $xoopsDB->fetchArray($query);
        if ($comment!=$result['comment']) {
            $query ="INSERT INTO ".$xoopsDB->prefix('smallworld_comments')
                . " (comment, uid_fk,msg_id_fk,created) VALUES ('".$comment."', '".$uid."','".$msg_id."', '".$time."')";
            $result = $xoopsDB->queryF($query);
            $newquery = "SELECT C.com_id, C.uid_fk, C.comment, C.msg_id_fk, C.created, U.username FROM "
                . $xoopsDB->prefix('smallworld_comments')." C, ".$xoopsDB->prefix('smallworld_user')
                . " U where C.uid_fk=U.userid and C.uid_fk='".$uid."' and C.msg_id_fk='".$msg_id."' order by C.com_id desc limit 1 ";
            $result2 = $xoopsDB->queryF($newquery);
               while ($row = $xoopsDB->fetchArray($result2)) {
                $data[0]=$row;
            }

             return $data[0];
        } else {
            return false;
        }
    }

    /**
     * @Get array of users followers
     * @param  int   $me
     * @return array
     */
    public function getFollowers ($me)
    {
        global $xoopsDB, $xoopsUser;
        $query = "SELECT you FROM ".$xoopsDB->prefix('smallworld_followers')." WHERE me = '".$me."'";
        $result=$xoopsDB->queryF($query);
        $i = $xoopsDB->getRowsNum($result);
        while ($row = $xoopsDB->fetchArray($result)) {
            $data[]=$row;
        }
        if ($i == 0) {
            $data = array($me);
        }
        if(!empty($data)) {
            return $data;
        }
    }
    
    /**
     * @count all votes
     * @param  int $type
     * @param  int $val
     * @param  int $msgid
     * @return int
     */
    public function countVotes ($type, $val, $msgid)
    {
        global $xoopsUser, $xoopsDB;
        $query = "Select SUM(".$val.") as sum from ".$xoopsDB->prefix('smallworld_vote')." where msg_id = '".$msgid."' and com_id = '0'";
        $result=$xoopsDB->queryF($query);
        while ($row = $xoopsDB->fetchArray($result)) {
            $sum = $row['sum'];
        }
        if ($sum==''){
            $sum = '0';
        }

        return $sum;
    }
    
    /**
     * @Count comments votes
     * @param int $type
     * @param int $val
     * @param int $comid
     * @param int $msgid
     * @returns int
     */
    public function countVotesCom ($type, $val, $comid, $msgid)
    {
        global $xoopsUser, $xoopsDB;
        $query = "Select SUM(".$val.") as sum from ".$xoopsDB->prefix('smallworld_vote')
            . " where com_id = '".$comid."' AND msg_id = '".$msgid."'";
        $result=$xoopsDB->queryF($query);
        while ($row = $xoopsDB->fetchArray($result)) {
            $sum = $row['sum'];
        }
        if ($sum==''){
            $sum = '0';
        }

        return $sum;
    }
    
    /**
     * @Check if user is friend
     * @param  int    $userid
     * @param  string $type
     * @param  int    $comid
     * @param  int    $msgid
     * @return int
     */
    public function HasVoted ($userid, $type, $comid, $msgid)
    {
        global $xoopsUser, $xoopsDB;
        if ($type == 'msg') {
            $sql = "Select * from ".$xoopsDB->prefix('smallworld_vote')
                . " where com_id = '0' and msg_id = '".$msgid."' and user_id = '".$userid."'";
            $result = $xoopsDB->queryF($sql);
            $i = $xoopsDB->getRowsNum($result);
        } else {
            $sql = "Select * from ".$xoopsDB->prefix('smallworld_vote')
                . " where com_id = '".$comid."' and msg_id = '".$msgid."' and user_id = '".$userid."'";
            $result = $xoopsDB->queryF($sql);
            $i = $xoopsDB->getRowsNum($result);
        }

        return $i;
    }
    
    /**
     * @count messages per user
     * @param  int $userid
     * @return int
     */
    public function CountMsges ($userid)
    {
        global $xoopsDB;
        $sql = "SELECT (SELECT COUNT(*) FROM ".$xoopsDB->prefix('smallworld_comments')
            . " WHERE uid_fk = '".$userid."') + (SELECT COUNT(*) FROM "
            . $xoopsDB->prefix('smallworld_messages')." WHERE uid_fk = '".$userid."')";
        $result = $xoopsDB->queryF($sql);
        $sum = $xoopsDB->fetchRow($result);

        return $sum[0];
    }
    
    /**
     * @Show permaling updates
     * @param  int   $updid
     * @param  int   $uid
     * @param  int   $ownerID
     * @return array
     */
    public function UpdatesPermalink($updid,$uid, $ownerID)
    {
        global $xoopsUser, $xoopsDB, $moduleConfig;
        $query = "SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM "
            . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
            . " U  WHERE M.uid_fk=U.userid and M.uid_fk='".$ownerID."'";
        $query .= " AND M.msg_id = '".$updid."'";
        $query .= " order by M.created DESC LIMIT 1";
        $result=$xoopsDB->queryF($query);
        $count = $xoopsDB->getRowsNum($result);
        if ($count < 1) {
            return false;
        } else {
            while ($row = $xoopsDB->fetchArray($result)) {
                $data[]=$row;
            }
            if(!empty($data)) {
                return $data;
            }
        }
    }

    /**
     * @Get share link
     * @param  int   $updid
     * @param  int   $ownerID
     * @return array
     */
    public function UpdatesSharelink($updid,$ownerID)
    {
        global $xoopsUser, $xoopsDB, $moduleConfig, $xoopsLogger;
        $xoopsLogger->activated = false;
        //error_reporting(E_ALL);
        $query = "SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM "
            . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
            . " U WHERE M.uid_fk=U.userid and M.uid_fk='".$ownerID."' and M.priv = 0";
        $query .= " AND M.msg_id = '".$updid."'";
        $query .= " order by created DESC LIMIT 1";
        $result=$xoopsDB->queryF($query);
        $count = $xoopsDB->getRowsNum($result);
        if ($count < 1) {
            return false;
        } else {
            while ($row = $xoopsDB->fetchArray($result)) {
                $data[]=$row;
            }
            if(!empty($data)) {
                return $data;
            }
        }
    }
    
    /**
     * @Get sharing link
     * @param  int    $id
     * @param  int    $priv
     * @return string
     */
    function GetSharing ($id, $priv) {
        if ($priv != 1) {
            $text  = " | <span class='smallworld_share' id='smallworld_share'>";
            $text .= "<a class='share' id='share-page".$id."' href='javascript:void(0);'>"._SMALLWORLD_SHARELINK."</a></span>";
        } else {
            $text = "";
        }

        return $text;
    }

    /**
     * @Get content for sharing div
     * @param  int    $id
     * @param  int    $priv
     * @param  string $permalink
     * @param  string $desc
     * @param  string $username
     * @return string
     */
    function GetSharingDiv ($id, $priv, $permalink,$desc,$username) {
        if ($priv != 1) {
            $text   = "<div style='display: none;' class='smallworld_bookmarks' id='share-page' name='share-page".$id."'>";
            $text  .= "<span name='share-page".$id."' rel1='".$desc."' rel2= '".$username."' rel=".$permalink." id='basicBookmark' title='"._SMALLWORLD_SHAREBOX_TITLE."'>";
            $text  .= "</span></div>";
        } else {
            $text="";
        }

        return $text;
    }
    
    /**
     * @Parse update and comments array to template for public updates
     * @param  array  $updatesarray
     * @param  int    $id
     * @param  string $permalink
     * @return void
    */
    function ParsePubArray ($updatesarray, $id)
    {
        global $xoopsUser, $xoopsTpl, $tpl, $xoopsModule, $xoopsConfig;
        
        $check = new SmallWorldUser;
        $dBase = new SmallWorldDB;
        $profile = ($xoopsUser) ? $check->checkIfProfile($id) : 0;
        $module_handler =& xoops_gethandler('module');
            $module = $module_handler->getByDirname('smallworld');
            $config_handler =& xoops_gethandler('config');
            $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
        
        $myavatar        =    $this->Gravatar($id);
        $myavatarlink    =    smallworld_getAvatarLink($id, $myavatar);
        $myavatar_size  = smallworld_getImageSize(80, 100, $myavatarlink);
        $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 35);
        
        $xoopsTpl->assign('myavatar',$myavatar);
        $xoopsTpl->assign('myavatarlink',$myavatarlink);
        $xoopsTpl->assign('myavatar_highwide',$myavatar_highwide);
        
        if (!empty($updatesarray)) {
            foreach ($updatesarray as $data) {

            // Is update's user a friend ?
            $frU = $check->friendcheck($id,$data['uid_fk']);

            $USW = array();
            $USW['posts'] = 0;
            $USW['comments'] = 0;

            if ($xoopsUser) {
                if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) || $data['uid_fk'] == $id) {
                    $USW['posts'] = 1;
                    $USW['comments'] = 1;
                    $frU[0] = 2;
                } else {
                    $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                }
            }

            if (!$xoopsUser) {
                $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
            }

                $wm['msg_id']            =    $data['msg_id'];
                $wm['orimessage']        =    ($USW['posts'] == 1 || $profile >= 2) ?
                    str_replace(array("\r", "\n"), '',Smallworld_stripWordsKeepUrl($data['message'])):'';
                $wm['message']            =    ($USW['posts'] == 1 || $profile >= 2) ?
                    smallworld_tolink(htmlspecialchars_decode($data['message']), $data['uid_fk']):_SMALLWORLD_MESSAGE_PRIVSETPOSTS;
                $wm['message']          =   Smallworld_cleanup($wm['message']);
                $wm['created']            =    smallworld_time_stamp($data['created']);
                $wm['username']            =    $data['username'];
                $wm['uid_fk']            =    $data['uid_fk'];
                $wm['priv']                =    $data['priv'];
                $wm['avatar']            =    $this->Gravatar($data['uid_fk']);
                $wm['avatar_link']        =    smallworld_getAvatarLink ($data['uid_fk'], $wm['avatar']);
                $wm['avatar_size']      =   smallworld_getImageSize(80, 100, $wm['avatar_link']);
                $wm['avatar_highwide']  =   smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);
                $wm['vote_up']            =    $this->countVotes ('msg', 'up', $data['msg_id']);
                $wm['vote_down']        =    $this->countVotes ('msg', 'down', $data['msg_id']);
                $wm['sharelinkurl']        =    XOOPS_URL."/modules/smallworld/smallworldshare.php?ownerid=".$data['uid_fk'];
                $wm['sharelinkurl']       .=    "&updid=".$data['msg_id']."";
                $wm['usernameTitle']    =    $wm['username']._SMALLWORLD_UPDATEONSITEMETA.$xoopsConfig['sitename'];
                if ($USW['posts'] == 1 || $profile >= 2) {
                    $wm['sharelink'] = $this->GetSharing ($wm['msg_id'],$wm['priv']);
                } else {
                    $wm['sharelink'] = $this->GetSharing ($wm['msg_id'],1);
                }

                if ($USW['posts'] == 1 || $profile >= 2) {
                    $wm['sharediv']    = $this->GetSharingDiv ($wm['msg_id'],$wm['priv'], $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                } else {
                    $wm['sharediv']    = $this->GetSharingDiv ($wm['msg_id'],1, $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                }
                $wm['linkimage']        =   XOOPS_URL."/modules/smallworld/images/link.png";
                $wm['permalink']        =   XOOPS_URL."/modules/smallworld/permalink.php?ownerid=".$data['uid_fk']."&updid=".$data['msg_id'];
                $wm['commentsarray']    =    $this->Comments($data['msg_id']);

                if ($frU[0] == 2 || $USW['posts'] == 1) {
                    $xoopsTpl->append('walldata', $wm);
                }

                if (!empty($wm['commentsarray'])){
                    foreach($wm['commentsarray'] as $cdata) {
                        // Is commentuser a friend ?
                        $frC = $check->friendcheck($id,$cdata['uid_fk']);

                        $USC = array();
                        $USC['posts'] = 0;
                        $USC['comments'] = 0;

                        if ($xoopsUser) {
                            if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) || $cdata['uid_fk'] == $id) {
                                $USC['posts'] = 1;
                                $USC['comments'] = 1;
                                $frC[0] = 2;
                            } else {
                                $USC = json_decode($dBase->GetSettings($cdata['uid_fk']), true);
                            }
                        }

                        if (!$xoopsUser) {
                            $USC = json_decode($dBase->GetSettings($cdata['uid_fk']), true);
                        }

                        $wc['msg_id_fk']        =        $cdata['msg_id_fk'];
                        $wc['com_id']            =        $cdata['com_id'];
                        $wc['comment']            =        ($USC['comments']  == 1 || $profile >= 2) ?
                            smallworld_tolink(htmlspecialchars_decode($cdata['comment']),$cdata['uid_fk']):
                            _SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
                        $wc['comment']            =        Smallworld_cleanup($wc['comment']);
                        $wc['time']                =        smallworld_time_stamp($cdata['created']);
                        $wc['username']            =        $cdata['username'];
                        $wc['uid']                =        $cdata['uid_fk'];
                        $wc['myavatar']            =        $this->Gravatar($id);
                        $wc['myavatar_link']    =        $myavatarlink;
                        $wc['avatar_size']      =       smallworld_getImageSize(80, 100, $wc['myavatar_link']);
                        $wc['avatar_highwide']  =       smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);
                        $wc['cface']            =        $this->Gravatar($cdata['uid_fk']);
                        $wc['avatar_link']        =        smallworld_getAvatarLink ($cdata['uid_fk'], $wc['cface']);
                        $wc['vote_up']            =        $this->countVotesCom ('com', 'up', $cdata['msg_id_fk'],$cdata['com_id']);
                        $wc['vote_down']        =        $this->countVotesCom ('com', 'down', $cdata['msg_id_fk'],$cdata['com_id']);
                        
                        if ($frC[0] == 2 || $USC['comments'] == 1) {
                            $xoopsTpl->append('comm', $wc);
                        }
                    }
                }
            }
        }
    }
}
