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
* @Repository path:        $HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/class/wall.php $
* @Last committed:        $Revision: 9502 $
* @Last changed by:        $Author: djculex $
* @Last changed date:    $Date: 2012-05-13 18:15:22 +0200 (sø, 13 maj 2012) $
* @ID:                    $Id: wall.php 9502 2012-05-13 16:15:22Z djculex $
**/



// Moderrated and fitted from the tutorial by Srinivas Tamada http://9lessons.info

class Public_Wall_Updates 
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
        
    }
    
    /**
     * Get arry of users being inspected
     *
     *
     */
     
    function inspected () {
        global $xoopsDB;
        $sql = "SELECT userid FROM ".$xoopsDB->prefix('smallworld_admin'). " WHERE (inspect_start+inspect_stop) > ".time()."";
        $result = $xoopsDB->queryF($sql);
        $data = array();
        while ($row = $xoopsDB->fetchArray($result)) {
            $data[]=$row;
        }
        if (!empty($data)) {
            $sub = implode(",",Smallworld_array_flatten(array_unique($data),0));
        } else {
            $sub = 0;
        }
        return $sub;
    }
    /**
     * @Get array of updates
     * @param int $last
     * @param int $uid
     * @param array $followes
     * @return array
     */      
    public function Updates($last,$moderators) {
        global $xoopsUser, $xoopsDB, $moduleConfig, $xoopsLogger;
        $hm = smallworld_GetModuleOption('msgtoshow');
        $set = smallworld_checkPrivateOrPublic ();
        $mods = implode(",",Smallworld_array_flatten(array_unique($moderators),0));
        $inspected = $this->inspected();
        $perm = smallworld_GetModuleOption('smallworldshowPoPubPage');
        $i=0;   
        
        if ($last == 0) {
            $query = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM ".$xoopsDB->prefix('smallworld_messages')
                . " M, ".$xoopsDB->prefix('smallworld_user')." U WHERE M.uid_fk=U.userid and M.uid_fk in (".$mods.") and M.uid_fk NOT IN (".$inspected.") and M.priv = '0'";
        } elseif ($last > 0) {
            $query = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM "
                . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
                . " U  WHERE M.uid_fk=U.userid and M.uid_fk in (".$mods.") and M.uid_fk NOT IN (".$inspected.") and M.priv = '0' and M.msg_id < '".$last."'";
        } elseif ($last == 'a') {
            $query = "SELECT M.msg_id, M.uid_fk, M.priv, M.message, M.created, U.username FROM "
                . $xoopsDB->prefix('smallworld_messages')." M, ".$xoopsDB->prefix('smallworld_user')
                . " U  WHERE M.uid_fk=U.userid and M.uid_fk in (".$mods.") and M.uid_fk NOT IN (".$inspected.") and M.priv = '0'";            
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
     * @param int $msg_id
     * @return array
     */  
    public function Comments($msg_id)
    {
        global $xoopsUser, $xoopsDB;
        $inspected = $this-> inspected();
        $query = "SELECT C.msg_id_fk, C.com_id, C.uid_fk, C.comment, C.created, U.username FROM "
            . $xoopsDB->prefix('smallworld_comments')." C, ".$xoopsDB->prefix('smallworld_user')
            . " U WHERE C.uid_fk=U.userid and C.msg_id_fk='".$msg_id."' and C.uid_fk NOT IN (".$inspected.") order by C.com_id asc ";
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
     * @param int $uid
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
        
        $type = Array(
                    1 => 'jpg', 
                    2 => 'jpeg', 
                    3 => 'png', 
                    4 => 'gif'
                );
        
        $ext = explode(".",$image);
        
        if (@!in_array(strtolower ($ext[1]), $type) || $image == '') {
            $avt = new XoopsUser($uid);
            $avatar = $avt->user_avatar(); 
        } else {
            $avatar = $image;
        }
        return $avatar;
    }
    
    /**
     * @count all votes
     * @param int $type
     * @param int $val
     * @param int $msgid
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
     * @Check is user is friend
     * @param int $userid
     * @param string $type
     * @param int $comid
     * @param int $msgid
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
     * @param int $userid
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
     * @param int $updid
     * @param int $uid
     * @param int $ownerID
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
     * @param int $updid
     * @param int $ownerID
     * @return array
     */  
    public function UpdatesSharelink($updid,$ownerID) 
    {
        global $xoopsUser, $xoopsDB, $moduleConfig;
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
     * @param int $id
     * @param int $priv
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
     * @param int $id
     * @param int $priv
     * @param string $permalink
     * @param string $desc
     * @param string $username
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
    
}
?>