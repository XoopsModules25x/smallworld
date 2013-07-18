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
* @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/include/functions.php $
* @Last committed:        $Revision: 11719 $
* @Last changed by:        $Author: djculex $
* @Last changed date:    $Date: 2013-06-19 16:37:48 +0200 (on, 19 jun 2013) $
* @ID:                    $Id: functions.php 11719 2013-06-19 14:37:48Z djculex $
**/


/*
Get array of timestamps based on the timetype configured in preferences
*/
function SmallworldGetTimestampsToForm() 
{
    $timearray = array();
    $start = 1900;
    $end = date('Y');
        for ($i = $start; $i <= $end; $i++) {
            $key = $i;
            $timearray[$key] = $i;
        }
        ksort($timearray);
    return $timearray;
}

// Clean vars or arrays
// If array check for magicQuotes. 
// Pass string to Smallworld_cleanup_string 
function Smallworld_cleanup($text)
{
    if (is_array($text)) {
        foreach ($text as $key => $value) {
            $text[$key] = Smallworld_cleanup_string($value);
        }
    } else {
        $text = Smallworld_cleanup_string($text);
    }
    return $text;
}

// Sanitize string
function Smallworld_cleanup_string($text)
{
    $myts = MyTextSanitizer::getInstance();   
    $text = $myts->displayTarea($text, $html = 1, $smiley = 1, $xcode = 1, $image = 1, $br = 1);
    return $text;
}

// clean Array for mysql insertion
// or send string to Smallworld_sanitize_string
function Smallworld_sanitize($text)
{
    if (is_array($text)) {
        foreach ($text as $key => $value) {
            $text[$key] = Smallworld_sanitize_string($value);
        }
    } else {
        $text = Smallworld_sanitize_string($text);
    }
    return $text;
}

function Smallworld_sanitize_string($value)
{
    $myts = MyTextSanitizer::getInstance();
    if(get_magic_quotes_gpc()){
        $value = $myts->stripSlashesGPC($value);
    } else {
        $value = mysql_real_escape_string($value);
    }
    return $value; 
}

function Smallworld_DateOfArray($array)
{
    $data = array();
    foreach ($array as $k => $v) {
        $data[$k] = strtotime($v);
    }
    return $data;
}

function Smallworld_YearOfArray($array)
{
    $data = array();
    foreach ($array as $k => $v) {
        $data[$k] = $v;
    }
    return $data;
}

function Smallworld_CreateIndexFiles($folderUrl)
{
    $myts =& MyTextSanitizer::getInstance();
    file_put_contents($folderUrl . 'index.html', "<script>history.go(-1);</script>");
}

function smallworld_ImplodeArray($glue = ", ", $pieces)
{
    return implode($glue, $pieces);
} 

// recursively reduces deep arrays to single-dimensional arrays
// $preserve_keys: (0=>never, 1=>strings, 2=>always)
function Smallworld_array_flatten($array, $preserve_keys = 1, &$newArray = Array())
{
    foreach ($array as $key => $child) {
        if (is_array($child)) {
            $newArray =& Smallworld_array_flatten($child, $preserve_keys, $newArray);
        } elseif ($preserve_keys + is_string($key) > 1) {
            $newArray[$key] = $child;
        } else {
            $newArray[] = $child;
        }
    }
    return $newArray;
}

/*
 * Calculate years from date format (YYYY-MM-DD)
 * @param date $birth
 * @returns integer
 */
function Smallworld_Birthday($birth)
{
    list($year, $month, $day) = explode("-", $birth);
    $yearDiff  = date("Y") - $year;
    $monthDiff = date("m") - $month;
    $dayDiff   = date("d") - $day;
    if ($monthDiff == 0){
        if($dayDiff < 0) {
          $yearDiff--;
        }
    }
    if ($monthDiff < 0){
        $yearDiff--;
    }
    return $yearDiff;
}

function smallworld_isset_or($check)
{
    global $xoopsDB,$xoopsUser;
    $query = "SELECT * FROM ".$xoopsDB->prefix('smallworld_user')." WHERE username = '".$check."'";
    $result=$xoopsDB->queryF($query);
    while ($row=$xoopsDB->fetchArray($result)) {
        if ($row['userid'] == '') {
            return false;
        } else {
                return $row['userid'];
        }
    }
} 

//Srinivas Tamada http://9lessons.info
//Loading Comments link with load_updates.php 
function smallworld_time_stamp($session_time)
{ 
    global $xoopsConfig;          
    $time_difference = time() - $session_time; 
    $seconds = $time_difference; 
    $minutes = round($time_difference / 60);
    $hours = round($time_difference / 3600); 
    $days = round($time_difference / 86400); 
    $weeks = round($time_difference / 604800); 
    $months = round($time_difference / 2419200); 
    $years = round($time_difference / 29030400); 
    if($seconds <= 60) {
        $t = "$seconds"._SMALLWORLD_SECONDSAGO; 
    } else if($minutes <= 60) {
        if($minutes == 1) {
            $t = _SMALLWORLD_ONEMINUTEAGO; 
        } else {
            $t = "$minutes"._SMALLWORLD_MINUTESAGO;
        }
    } else if($hours <= 24) {
            if($hours == 1) {
                $t = _SMALLWORLD_ONEHOURAGO;
            } else {
                $t = "$hours"._SMALLWORLD_HOURSAGO;
            }
    } else if($days <= 7 ) {
        if($days == 1){
            $t = _SMALLWORLD_ONEDAYAGO;
        } else {
            $t = "$days"._SMALLWORLD_DAYSAGO;
        }
    } else if($weeks <=4) {
        if($weeks==1) {
            $t = _SMALLWORLD_ONEWEEKAGO;
        } else {
            $t = "$weeks"._SMALLWORLD_WEEKSAGO;
        }
    } else if($months <= 12) {
        if($months == 1) {
            $t = _SMALLWORLD_ONEMONTHAGO;
        } else {
            $t = "$months"._SMALLWORLD_MONTHSAGO;
        }  
    } else {
        if($years == 1) {
            $t =  _SMALLWORLD_ONEYEARAGO;
        } else {
            $t = "$years"._SMALLWORLD_YEARSAGO;
        }
    }
    return $t;
}

// Return only url/link
// If url is image link return <img>
function smallworld_tolink($text, $uid)
{
    global $xoopsUser;
    $ext = substr($text,-4,4);
    $ext2 = substr($text,-5,5);
    $xoopsUser = new XoopsUser($uid);
    $usr = new $xoopsUser($uid);
    
    $userID = $xoopsUser->getVar('uid');
	$user = new XoopsUser($userID);
	$username = $user->getVar('uname');
    $gallery = XOOPS_URL."/modules/smallworld/galleryshow.php?username=".$usr->getVar('uname');
    
    if (in_array($ext,array('.jpg','.bmp','.gif','.png')) || in_array($ext,array('.JPG','.BMP','.GIF','.PNG')) || in_array($ext2,array('.jpeg'))){
        if (strpos($text, 'UPLIMAGE') !== false ) {
            $text = str_replace('UPLIMAGE', '', $text); 
            $text = preg_replace('/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                        '<span class="smallworldUplImgTxt"><br/><img class="smallworldAttImg" src="\\1"><br><br><a id="smallworldUplImgLnk" href="'
                        . $gallery . '" target="_SELF">'
                        . $usr->getVar('uname') . _SMALLWORLD_UPLOADEDSOMEIMAGES
                        . '</a><br></span>', $text
                    );
            $text = preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                    '<a href="\\1">lala</a>', $text);
            $text = preg_replace('/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                        '\\1<span class="smallworldUplImgTxt"><br/><img class="smallworldAttImg" src="//\\2"><br><br><a id="smallworldUplImgLnk" href="' 
                        . $gallery . '" target="_SELF">'
                        . $username . _SMALLWORLD_UPLOADEDSOMEIMAGES
                        . '</a><br></span>', $text
                    );
             $text = html_entity_decode($text,ENT_QUOTES,"UTF-8");
        } else {
            $text = preg_replace('/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                    '<img class="smallworldAttImg" src="\\1"><a class="smallworldAttImgTxt" href="\\1">'._SMALLWORLD_CLICKIMAGETHUMB.' </a><br>', $text);
            $text = preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                    '<img class="smallworldAttImg" src="\\1"><a class="smallworldAttImgTxt" href="\\1">'._SMALLWORLD_CLICKIMAGETHUMB.' </a><br>', $text);
            $text = preg_replace('/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
            '\\1<img class="smallworldAttImg" src="//\\2"><a class="smallworldAttImgTxt" href="//\\2">'._SMALLWORLD_CLICKIMAGETHUMB.'</a><br>', $text);
             $text = html_entity_decode($text,ENT_QUOTES,"UTF-8");
        }
    } else {
        $text = html_entity_decode($text,ENT_QUOTES,"UTF-8");
        $text = " " . $text;
        $text = str_replace('UPLIMAGE', '', $text); 
    }
    return linkify_twitter_status($text);
}

function Smallworld_stripWordsKeepUrl($text)
{
    preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                 '<div class=".embed"><a href="\\1">\\1</a></div>', $text);
    return $text;
}

function Smallworld_sociallinks($num, $name)
{
        if ($num == 0)     {
            $image    =    '<img title="Msn" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/msn.png"/>'; 
            $link     =    '<a title="Msn" id="Smallworld_socialnetwork" target="_blank" href="http://members.msn.com/'.$name.'">';        
        }
        if ($num == 1)    {
            $image    =    '<img title="facebook" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/facebook.png"/>'; 
            $link     =    '<a title="facebook" id="Smallworld_socialnetwork" target="_blank" href="http://www.facebook.com/'.$name.'">';
        }
        if ($num == 2)    {
            $image    =    '<img title="GooglePlus" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/googleplus.png"/>';
            $link     =    '<a title="GooglePlus" id="Smallworld_socialnetwork" target="_blank" href="https://plus.google.com/'.$name.'">';    
        }
        if ($num == 3)    {
            $image    =    '<img title="Icq" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/icq.png"/>';
            $link     =    '<a title="icq" id="Smallworld_socialnetwork" target="_blank" href="http://www.icq.com/people/'.$name.'/">';    
        }            
        if ($num == 4)    {
            $image    =    '<img title="Skype" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/skype.png"/>';
            $link     =    '<a title="Skype" id="Smallworld_socialnetwork" target="_blank" href="skype:'.$name.'?userinfo">';
        }
        if ($num == 5)    {
            $image    =    '<img title="Twitter" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/twitter.png"/>';
            $link     =    '<a title="Twitter" id="Smallworld_socialnetwork" target="_blank" href="http://twitter.com/#!/'.$name.'">';    
        }    
        if ($num == 6)    {
            $image    =    '<img title="MySpace" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/myspace.png"/>';
            $link     =    '<a title="MySpace" id="Smallworld_socialnetwork" target="_blank" href="http://www.myspace.com/'.$name.'">';
        }
        if ($num == 7)    {
            $image    =    '<img title="Xoops" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/xoops.png"/>'; 
            $link     =    '<a title="Xoops" id="Smallworld_socialnetwork" target="_blank" href="http://xoops.org/modules/profile/userinfo.php?uid='.$name.'">';    
        }
        if ($num == 8)    {
            $image    =    '<img title="Yahoo Messenger" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/yahoo.png"/>'; 
            $link     =    '<a title="Yahoo Messenger" id="Smallworld_socialnetwork" target="_blank" href="ymsgr:sendim?'.$name.'">';    
        }
        if ($num == 9)    {
            $image    =    '<img title="Youtube" id="Smallworld_socialnetworkimg" src="'.XOOPS_URL.'/modules/smallworld/images/socialnetworkicons/youtube.png"/>'; 
            $link     =    '<a title="Youtube" id="Smallworld_socialnetwork" target="_blank" href="http://www.youtube.com/user/'.$name.'">';    
        }        
    return  $image.$link;
}

function smallworld_GetModuleOption($option, $repmodule='smallworld')
{
    global $xoopsModuleConfig, $xoopsModule;
    static $tbloptions = Array();
    if(is_array($tbloptions) && array_key_exists($option,$tbloptions)) {
        return $tbloptions[$option];
    }
    $retval = false;
    if (isset($xoopsModuleConfig) 
        && (is_object($xoopsModule) 
        && $xoopsModule->getVar('dirname') == $repmodule 
        && $xoopsModule->getVar('isactive'))
    )
    {
        if(isset($xoopsModuleConfig[$option])) {
            $retval= $xoopsModuleConfig[$option];
        }
    } else {
        $module_handler =& xoops_gethandler('module');
        $module =& $module_handler->getByDirname($repmodule);
        $config_handler =& xoops_gethandler('config');
        if ($module) {
            $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
            if(isset($moduleConfig[$option])) {
                $retval= $moduleConfig[$option];
            }
        }
    }
    $tbloptions[$option]=$retval;
    return $retval;
}

/**
 * Check image extension and users gender. If image is legal image extension return avatar, 
    else return default gender based image
 * @param int $userid
 * @param string $image
 * @returns string
 */
function smallworld_getAvatarLink($userid, $image)
{
    global $xoopsUser, $xoopsDB;  
    $ext = pathinfo(strtolower($image), PATHINFO_EXTENSION);
    $sql = "SELECT gender FROM ".$xoopsDB->prefix('smallworld_user')." WHERE userid = '".intval($userid)."'";
    $result = $xoopsDB->queryf($sql);
    $counter = $xoopsDB->getRowsNum($result);
    if ($counter == 0) {
        $gender = '';
    } else {
        while ($row = $xoopsDB->fetchArray($result)) { 
            $gender = $row['gender'];
        }
    }
    
    $link = XOOPS_UPLOAD_URL."/".$image;
    if (!in_array($ext,array('jpg','bmp','gif','png','jpeg')) || $image == '' || $image == "blank.gif"){
        if ($ext == '' || $gender == '1') {
            $link = XOOPS_URL."/modules/smallworld/images/ano_woman.png";
        }
    
        if ($ext == '' AND $gender == '1') {
            $link = XOOPS_URL."/modules/smallworld/images/ano_woman.png";
        }
        
        if ($ext == '' AND $gender == '2') {
            $link = XOOPS_URL."/modules/smallworld/images/ano_man.png";
        }
        if ($ext == '' AND $gender == '1') {
            $link = XOOPS_URL."/modules/smallworld/images/ano_woman.png";
        }
        if ($ext == '' AND $gender == '2') {
            $link = XOOPS_URL."/modules/smallworld/images/ano_man.png";
        }
        
        if ($ext == '' AND $gender == '') {
            $link = XOOPS_URL."/modules/smallworld/images/genderless.png";
        }
        
        if ($ext == '' AND $gender == '') {
            $link = XOOPS_URL."/modules/smallworld/images/genderless.png";
        }
    } 
    return $link;
}

function smallworld_checkForXim()
{
    $filename = XOOPS_ROOT_PATH."/modules/xim/chat.php";
    if (file_exists($filename)) {
       return true;
    } else {
       return false;
    } 
}

/**
 * Get version number of xim if exists
 * @return int $version
 */
function smallworld_XIMversion () {
    global $xoopsDB;
    $sql = "SELECT version FROM ".$xoopsDB->prefix('modules')." WHERE dirname = 'xim'";
    $result = $xoopsDB->queryF($sql);
    if ($result) {
        while ($r = $xoopsDB->fetchArray($result)) { 
            $version = $r['version'];
        }
    } else {
        $version = 0;
    }
    return $version;
}

/*
* Input: Message Id,
* Return owner of thread (original poster)
* Return Integer
*/
function Smallworld_getOwnerFromComment($msg_id_fk)
{
    global $xoopsDB;
    $sql = "Select uid_fk from ".$xoopsDB->prefix('smallworld_messages')." where msg_id = '".$msg_id_fk."'";
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) { 
        $owner = $r['uid_fk'];
    }
    return $owner;
}

// Get username from userID
function Smallworld_getName($userID)
{
    global $xoopsUser, $xoopsDB;
    $sql = "SELECT username FROM ".$xoopsDB->prefix('smallworld_user')." WHERE userid = '".intval($userID)."'";
    $result = $xoopsDB->queryf($sql);
    while ($row = $xoopsDB->fetchArray($result)) { 
        $name = $row['username'];
    }
    return $name;
}

// Check if user has been taken down for inspection by admin
// Userid = user id of user to check
// return array
function Smallworld_isInspected($userid)
{
    global $xoopsDB;
    $data = array();
    $sql = "SELECT inspect_start, inspect_stop FROM ".$xoopsDB->prefix('smallworld_admin')." WHERE userid = '".$userid."' AND (inspect_start+inspect_stop) > ".time()."";
    $result = $xoopsDB->queryF($sql);
    if ($xoopsDB->getRowsNum($result) > 0) {
        while ($row = $xoopsDB->fetchArray($result)) {
            $data['inspect'] = 'yes';
            $data['totaltime'] = ($row['inspect_start'] + $row['inspect_stop'])-time();
        }
    } else {
        $data['inspect'] = 'no';
    }
    return $data;
}

// Auto delete all inspects from DB where time has passed
// inspect_start + inspect_stop - time() is deleted if negative intval
function SmallworldDeleteOldInspects()
{
    global $xoopsDB;
    $sql = "UPDATE ".$xoopsDB->prefix('smallworld_admin')." SET inspect_start = '', inspect_stop = '' WHERE (inspect_start+inspect_stop) <= ".time()."";
    $result = $xoopsDB->queryF($sql);
}

// Function to get sum of users in you following array
// Used to calculate new message flash in jQuery intval fetch
function smallworld_getCountFriendMessagesEtc()
{
    global $xoopsUser, $xoopsDB;
    $user = new xoopsUser;
    $Wall = new Wall_Updates();
    $userid = $xoopsUser->getVar('uid');
    $followers = Smallworld_array_flatten($Wall->getFollowers($userid),0);    
    if (smallworld_GetModuleOption('usersownpostscount', $repmodule='smallworld') == 1) {
        array_push($followers, $userid);
    }
    $ids = join(',',$followers);
        $sql = "SELECT COUNT(*) AS total "
        ." FROM ( "
        ." SELECT com_id , count( * ) as comments FROM ".$xoopsDB->prefix('smallworld_comments')." WHERE uid_fk IN ($ids) Group by com_id "
        ." UNION ALL "
        ." Select msg_id , count( * ) as messages FROM ".$xoopsDB->prefix('smallworld_messages')." WHERE uid_fk IN ($ids) group by msg_id "
        ." ) as d";
        $result = $xoopsDB->queryF($sql);
        while ($r = $xoopsDB->fetchArray($result)) {
            $total = $r['total'];
        }
    return $total;
}

// Function to get sum of users in you following array
// Used to calculate new message flash in jQuery intval fetch
function smallworld_countUsersMessages($id)
{
    global $xoopsUser, $xoopsDB;
    $user = new xoopsUser;
    $Wall = new Wall_Updates();
        $sql = "SELECT COUNT(*) AS total "
        ." FROM ( "
        ." SELECT com_id , count( * ) as comments FROM ".$xoopsDB->prefix('smallworld_comments')." WHERE uid_fk = ".intval($id)." Group by com_id "
        ." UNION ALL "
        ." Select msg_id , count( * ) as messages FROM ".$xoopsDB->prefix('smallworld_messages')." WHERE uid_fk = ".intval($id)."group by msg_id "
        ." ) as d";
        $result = $xoopsDB->queryF($sql);
        while ($r = $xoopsDB->fetchArray($result)) {
            $total = $r['total'];
        }
    return $total;
}

// Get the three newest members to array
function smallworld_Stats_newest()
{
    global $xoopsDB, $xoopsUser;
    $nu = array();
    $sql = "SELECT * FROM ".$xoopsDB->prefix('smallworld_user')." ORDER BY regdate DESC limit 3";
    $result = $xoopsDB->queryF($sql);
        if ($xoopsDB->getRowsNum($result) > 0) {
            $i = 0;
            while ($r = $xoopsDB->fetchArray($result)) {
                $nu[$i]['userid'] = $r['userid'];
                $nu[$i]['username'] = $r['username'];
                $nu[$i]['regdate'] = date('d-m-Y',$r['regdate']);
                $nu[$i]['username_link']  = "<a href = '".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$r['username']."'>";
                $nu[$i]['username_link'] .= $r['username']." (".$r['realname'].") [".$nu[$i]['regdate']."] </a>";
                $nu[$i]['userimage'] = $r['userimage'];
                $nu[$i]['userimage_link'] = smallworld_getAvatarLink ($r['userid'], Smallworld_Gravatar($r['userid']));
                $i++;
            }
        }
    return $nu;
}
//Avatar Image
function Smallworld_Gravatar($uid)
{
    global $xoopsUser, $xoopsDB;
    $image='';
    $sql = "SELECT userimage FROM ".$xoopsDB->prefix('smallworld_user')." WHERE userid = '".$uid."'";
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $image = $r['userimage'];
    }
    if ($image == 'Not specifiyed' || $image == '') {
        $avt = new XoopsUser($uid);
        $avatar = $avt->user_avatar(); 
    } else {
        $avatar = $image;
    }
    return $avatar;
}
    
// find user with most posted messages
function Smallworld_mostactiveusers_allround()
{
    global $xoopsDB,$xoopsUser;
    $sql = "SELECT uid_fk, COUNT( * ) as cnt ";
    $sql .= "FROM ( ";
    $sql .= "SELECT uid_fk ";
    $sql .= "FROM ".$xoopsDB->prefix('smallworld_messages')." ";
    $sql .= "UNION ALL SELECT uid_fk ";
    $sql .= "FROM ".$xoopsDB->prefix('smallworld_comments')." ";
    $sql .= ") AS u ";
    $sql .= "GROUP BY uid_fk ";
    $sql .= "ORDER BY count( * ) DESC limit 3";
    $result = $xoopsDB->queryF($sql);
    $counter = $xoopsDB->getRowsNum($result);
    if ($counter < 1) {
    } else {    
        $msg = array();
        $counter = 1;
        while ($row = $xoopsDB->fetchArray($result)) {
            $msg[$counter]["counter"] = $counter;
            $msg[$counter]["img"] = smallworld_getAvatarLink ($row['uid_fk'], Smallworld_Gravatar($row['uid_fk']));
            $msg[$counter]["msgs"] = _SMALLWORLD_TOTALPOSTS." : ".$row["cnt"];
            $msg[$counter]["cnt"] = $row["cnt"];
            $msg[$counter]["username"] = $xoopsUser->getUnameFromId($row["uid_fk"]);
            $msg[$counter]["username_link"]  = "<a href = '".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$msg[$counter]["username"]."'>";
            $msg[$counter]["username_link"] .= $msg[$counter]["username"]." (".$msg[$counter]["msgs"].")</a>";
            $counter++;
        }
     }
    return $msg;
}

// Find worst rated users overall
function Smallworld_worstratedusers()
{
    global $xoopsUser, $xoopsDB;
    $array = array();
    $counter = 1;
    $sql  = "SELECT owner, (";
    $sql .= "sum( up ) - sum( down )";
    $sql .= ") AS total";
    $sql .= " FROM ".$xoopsDB->prefix('smallworld_vote')."";
    $sql .= " GROUP BY owner ORDER by total ASC LIMIT 5";
    $result = $xoopsDB->queryF($sql);
    while ($row = $xoopsDB->fetchArray($result)) {
        $array[$counter]['counter'] = $counter;
        $array[$counter]['img'] = smallworld_getAvatarLink ($row["owner"], Smallworld_Gravatar($row["owner"]));
        $array[$counter]['user'] = $xoopsUser->getUnameFromId($row["owner"]);
        $array[$counter]['rating'] = $row["total"];
        $array[$counter]['user_link']  = "<a href = '".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$array[$counter]['user']."'>";
        $array[$counter]['user_link'] .= $array[$counter]['user']." (".$array[$counter]['rating'].")</a>";
        $counter++;
    }
    return $array;
}

// Find best rated users overall
function Smallworld_topratedusers()
{
    global $xoopsUser, $xoopsDB;
    $array = array();
    $counter = 1;
    $sql  = "SELECT owner, (";
    $sql .= "sum( up ) - sum( down )";
    $sql .= ") AS total";
    $sql .= " FROM ".$xoopsDB->prefix('smallworld_vote')."";
    $sql .= " GROUP BY owner ORDER by total DESC LIMIT 5";
    $result = $xoopsDB->queryF($sql);
    while ($row = $xoopsDB->fetchArray($result)) {
        $array[$counter]['counter'] = $counter;
        $array[$counter]['img'] = smallworld_getAvatarLink ($row["owner"], Smallworld_Gravatar($row["owner"]));
        $array[$counter]['user'] = $xoopsUser->getUnameFromId($row["owner"]);
        $array[$counter]['rating'] = $row["total"];
        $array[$counter]['user_link']  = "<a href = '".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$array[$counter]['user']."'>";
        $array[$counter]['user_link'] .= $array[$counter]['user']." (".$array[$counter]['rating'].")</a>";
        $counter++;
    }
    return $array;
}

function smallworld_nextBirthdays()
{
    global $xoopsDB,$xoopsUser;
    $now = date('d-m');
    $res = array();
    $sql = 'SELECT userid, username, userimage, realname, birthday, CURDATE(),'
        . ' DATE_FORMAT(birthday, "%d / %m") AS daymon , '
        . ' (YEAR(CURDATE())-YEAR(birthday))'
        . ' - (RIGHT(CURDATE(),5)<RIGHT(birthday,5))'
        . ' AS age_now'
        . ' FROM xoops_smallworld_user WHERE right(birthday,5) = right(CURDATE(),5)'
        . ' ORDER BY MONTH( birthday ) , DAY( birthday ) '
        . ' LIMIT 10 ';        
    $result = $xoopsDB->queryF($sql);
    $counter = $xoopsDB->getRowsNum($result);
    $i = 0;
    while ($r = $xoopsDB->fetchArray($result)) {
        $res[$i]['amount']             = $counter;
        $res[$i]['userid']             = $r['userid'];
        $res[$i]['userimage']         = smallworld_getAvatarLink ($r['userid'], Smallworld_Gravatar($r["userid"]));
        $res[$i]['birthday']         = $r['daymon'];
        $res[$i]['agenow']             = $r['age_now'];
        $res[$i]['username']         = $xoopsUser->getUnameFromId($r['userid']);
        $res[$i]['username_link']     = "<a href = '".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$res[$i]['username']."'>";
        $res[$i]['username_link']  .= $res[$i]['username']." (".$r['daymon'].") ".$r['age_now']." "._SMALLWORLD_BDAY_YEARS;
        $res[$i]['username_link']  .="</a>";
        $i++;
    }
        return $res;
}

/*
 * Return date format (YYYY-MM-DD) for MySql
 * @param date $stringDate
 * $returns date
*/
function Smallworld_euroToUsDate($stringDate)
{
    if ($stringDate != 0 || $stringDate != '') { 
        $theData = explode("-", trim($stringDate));
        $ret = $theData[2] . "-" . $theData[1] . "-" . $theData[0];
        return $ret;
    } else {
        return "1900-01-01";
    }
}

/*
 * Return date format (DD-MM-YYYY) for display
 * @param date $stringDate
 * $returns date
*/
function Smallworld_UsToEuroDate($stringDate)
{
    if ($stringDate != 0 || $stringDate != '') { 
        $theData = explode("-", trim($stringDate));
        $ret = $theData[2] . "-" . $theData[1] . "-" . $theData[0];
        return $ret;
    } else {
        return "01-01-1900";
    }
}

function smallworld_sp ()
{
    $sp = array();
    $sp[0]['spimage'] = "<img id = 'smallworld_img_sp' src = '"
        . XOOPS_URL . "/modules/smallworld/images/sp.png' height='30px' width='30px' />";
    return $sp;
}

//Check privacy settings in permapage
function smallworldCheckPriv ($id)
{
    global $xoopsDB;
    $public = "SELECT priv FROM ".$xoopsDB->prefix('smallworld_messages')." WHERE msg_id = ".$id."";
    $result = $xoopsDB->queryF($public);
    while ($row = $xoopsDB->fetchArray($result)){
        $priv = $row['priv'];
    }
    return $priv;
}

// Function to calculate remaining seconds until user's birthday
// Input $d : date('Y-m-d') format
// return seconds until date at midnight, or
// return 0 if date ('Y-m-d') is equal to today
function smallworldNextBDaySecs($d)
{
    $olddate =  substr($d, 4);
    $exactdate = date('Y') . "" . $olddate;
    $newdate = date('Y') . "" . $olddate . " 00:00:00";
    $nextyear = date('Y') + 1 . "" . $olddate . " 00:00:00";
    if ($exactdate != date('Y-m-d')) {
        if($newdate > date("Y-m-d H:i:s")) {
            $start_ts = strtotime($newdate);
            $end_ts = strtotime(date("Y-m-d H:i:s"));
            $diff = $end_ts - $start_ts;
            $n = round($diff);
            $return = substr($n, 1);
            return $return;
        } else {
            $start_ts = strtotime($nextyear);
            $end_ts = strtotime(date("Y-m-d H:i:s"));
            $diff = $end_ts - $start_ts;
            $n = round($diff);
            $return = substr($n, 1);
            return $return;
        }
    } else {
        return 0;
    }
}

//Function to get value from xoopsConfig array
function smallworldGetValfromArray($key, $array)
{
    $ar = smallworld_GetModuleOption($array, $repmodule='smallworld');
    $ret = 0;
    if (in_array($key, $ar,true)) {
        $ret = 1;
        return $ret;
    } else {
        return 0;
    }
     
}

//Function to resize images proportionally
// Using imagesize($imageurl) returns $img[0], $img[1]
// Target = new max height or width in px
function smallworld_imageResize($width, $height, $target)
{
    //takes the larger size of the width and height and applies the
    //formula accordingly...this is so this script will work
    //dynamically with any size image
    if ($width > $height) {
        $percentage = ($target / $width);
    } else {
        $percentage = ($target / $height);
    }
    //gets the new value and applies the percentage, then rounds the value
    $width = round($width * $percentage);
    $height = round($height * $percentage);
    //returns the new sizes in html image tag format...this is so you
    //can plug this function inside an image tag and just get the
    
    return "width=\"$width\" height=\"$height\"";
}

/**
 * Fetch image width and height
 * will attempt to use the getimagesiz method first, then curl
 * @param int $w
 * @param int $h
 * @param url $url
 * @returns array
 */
function smallworld_getImageSize($w, $h, $url) 
{
    $bn = basename($url);
    if ($bn != 'blank.gif' 
        || $bn != 'blank.png'
        || $bn != 'blank.jpg'
        || $bn != 'image_missing.png'
    )
    {
        if (ini_get('allow_url_fopen')) {
            $imagesize = getimagesize($url);
        } else {
            $imagesize['0'] = $w;
            $imagesize['1'] = $h;
        }
        
        if (!ini_get('allow_url_fopen')) {
            if (function_exists('curl_init')) {
                 $ch = curl_init();
                 curl_setopt($ch, CURLOPT_URL, $url); 
                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                 $result = curl_exec($ch); 
                 $img = ImageCreateFromString($result);
                 $imagesize['0'] = imagesx($img);
                 $imagesize['1'] = imagesy($img);
            } else {
                $imagesize['0'] = $w;
                $imagesize['1'] = $h;
            }
        }
        return $imagesize;
    } else {
        return array(0 => '0', 1 => '0');
    }
}

/**
 * Check weather requests are set or not
 * If not return '' else return false
 * @param string $req
 * @returns string or void
 */
 
 function smallworld_isset($req)
 {
    if(isset($req) || !empty($req)) {
        return $req;
    } else {
        return '';
    }
 }
 
 /**
  * @do db query for images upload last 5 minutes by spec. userid and return random
  * @param int $userid
  * @returns string 
  */ 
  function smallworld_getRndImg($userid)
  {
    global $xoopsDB;
    $sql = "SELECT imgname FROM " . $xoopsDB->prefix('smallworld_images') . " WHERE userid = " . $userid
        . " AND time BETWEEN UNIX_TIMESTAMP( ) - 3000 AND UNIX_TIMESTAMP() ORDER BY rand() limit 1";
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $img = $r['imgname'];
    }
    if (!empty($img)) {
        return $img;
    } else {
        return false;
    }
  }

    /**
     * @Get url of smallworld
     * @returns string
     */ 
    function smallworld_getHostRequest() 
    {
        $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
        $host     = $_SERVER['HTTP_HOST'];
        $script   = $_SERVER['SCRIPT_NAME'];
        $params   = $_SERVER['QUERY_STRING'];
        $currentUrl = $protocol . '://' . $host;
        return $currentUrl;
    }    
    
    /**
     * @Get htmlentities
     * @return translated string to utf-8
     */
    function smallworld_decodeEntities($text) 
    {
        $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
        $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
        $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
        return $text;
    }
    
    /**
     * @Get string and shorten so contains only ? chars
     * @Param $text
     * @Param $chars
     * return string first ? chars
     */
    function smallworld_shortenText($text, $chars) 
    {
        $text = $text." ";
        $text = substr($text,0,$chars);
        $text = substr($text,0,strrpos($text,' '));
        $text = $text."...";
        return $text;
    }
    
    /**
     * @Get languagefile if constants are not defined
     * @param $def
     * @file
     * return file include 
     */
    function smallworld_isDefinedLanguage ($def, $file)
    {
        global $xoopsConfig;
        if (!defined($def)){
            // language files (main.php)
        	if( file_exists(XOOPS_ROOT_PATH.'/modules/smallworld/language/'.$xoopsConfig['language'].'/'.$file ) ) {
        	    include_once XOOPS_ROOT_PATH.'/modules/smallworld/language/'.$xoopsConfig['language'].'/'.$file;
        	}  else {
        	    // fallback english
        	    include_once XOOPS_ROOT_PATH.'/modules/smallworld/language/english/'.'/'.$file;
        	}
        }
    }
    
    /**
     * @Check if smallworld is for private or public access
     * return value for config
     */
    function smallworld_checkPrivateOrPublic ()
    {
        global $xoopsUser;
        $opt = array();
        $set = smallworld_GetModuleOption('smallworldprivorpub', $repmodule='smallworld');
            if ($set != 0) {
                $opt['access'] = 1;
            } else {
                $opt['access'] = 0;
            }
        if ($xoopsUser) {
            $id = $xoopsUser->getVar('uid');
            $user = new XoopsUser($id);
            $check = new SmallWorldUser;
            $profile = $check->checkIfProfile($id);
            $opt['xoopsuser'] = 1;
            if ($profile != 0) {
                $opt['smallworlduser'] = 1;
            } else {
                $opt['smallworlduser'] = 0;
            }
        }  else {
            $opt['xoopsuser'] = 0;
            $opt['smallworlduser'] = 0;
        }
        return $opt;
    }

    /**
     * @return array of groups
     * return array
     *
     */
    function smallworld_xv_getGroupd () {
        $db =& XoopsDatabaseFactory::getDatabaseConnection();
        $myts =& MyTextSanitizer::getInstance();
        $sql = "SELECT userid, username FROM ".$db->prefix('smallworld_user')." ORDER BY userid";
        $result = $db->queryF($sql);
        $num = $db->getRowsNum($result);
        if ($num == 0) {
            $ndata = array(0 => _MI_SMALLWORLD_ALL);
        } else {
            while ($r = $db->fetchArray($result)) {
                $data[$r['userid']] = $r['username'];
            }
            $ndata = array_merge(array(0 => _MI_SMALLWORLD_ALL), $data);
        }
        return $ndata;
    } 
    
    /**
     * Set javascript vars to theme using various values
     * Return void
     */
    function smallworld_SetCoreScript () {
        global $xoopsUser, $xoopsConfig, $xoTheme;

        $module_handler =& xoops_gethandler('module');
        $module = $module_handler->getByDirname('smallworld');
        $config_handler =& xoops_gethandler('config');
        if ($module) {
            $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));						
        }
               
        // IF logged in define xoops / smallworld user id
        $myid = ($xoopsUser) ? $xoopsUser->getVar('uid'):0;
        
        // Check if option is et to allow public reading
        $pub = smallworld_checkPrivateOrPublic();
            $access = $pub['access'];

        // GET various variables from language folder
        if (file_exists(XOOPS_ROOT_PATH.'/modules/smallworld/language/'.$xoopsConfig['language'].'/js/variables.js')) {
            $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/'.$xoopsConfig['language'].'/js/variables.js');
        } else {
            $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/english/js/variables.js');
        }

        // Check if USER is smallworld-registered user
        $chkUser = new SmallWorldUser;
        $ChkProf = ($xoopsUser) ? $chkUser->CheckIfProfile($myid):0;
        
        // Check if there are requests pending
        $count_invit = ($xoopsUser) ? count($chkUser->getRequests($myid)):0;
        
        // Get module config for validation and place in javascript
        $validate = $moduleConfig['validationstrenght'];
        
        // Check to see if smallworld should use username links to point to default xoops or smallworld
        $takeoverlinks = $moduleConfig['takeoveruserlinks'];
        $fieldstoshow = array_flip(smallworld_GetModuleOption('smallworldusethesefields', $repmodule='smallworld'));
        $useverification = smallworld_GetModuleOption('smallworldmandatoryfields', $repmodule='smallworld');
        $smallworldUV = implode(',', $useverification);
        
        // Get users messages count based on users followerArray
        $getUserMsgNum = ($xoopsUser) ? smallworld_getCountFriendMessagesEtc():0;

        // Check if request url is with www or without
        $urltest = smallworld_getHostRequest();
        $xoops_url = XOOPS_URL;
        if (!strstr($urltest, 'www.')) {
            $xoops_url = str_replace( 'www.', '', $xoops_url );
        }
               
        // Set javascript vars but only if not already defined.
        // Check prevents multible loads      
        $script = "var Smallworld_myID;"."\n";
        $script .= "if (typeof Smallworld_myID === 'undefined') {"."\n";
        $script .= "var smallworld_url = '" . $xoops_url . "/modules/smallworld/" . "';\n";
        $script .= "var smallworld_uploaddir = '" . $xoops_url . "/uploads/avatars/" . "';\n";
        $script .= "var xoops_smallworld = jQuery.noConflict();\n";
        $script .= "var Smallworld_myID = " . $myid . ";\n";
        $script .= "var Smallworld_userHasProfile = " . $ChkProf . ";\n";
        $script .= "var smallworldTakeOverLinks = " . $takeoverlinks . ";\n";
        $script .= "var Smallworld_geocomplete = '';\n";
        $script .= "var smallworldVerString = '" . $smallworldUV . "';\n";
        $script .= "var smallworlduseverification = new Array();\n";
        $script .= "smallworlduseverification = smallworldVerString.split(',');\n";
        $script .= "var Smallworld_hasmessages = " . $count_invit . ";\n";
        $script .= "var smallworldvalidationstrenght = " . $validate . ";\n";
        $script .= "var smallworld_getFriendsMsgComCount = " . $getUserMsgNum . ";\n";              
        //$script .= "var $ = jQuery();\n";
        $script .= "}"."\n";
        $xoTheme->addScript('','',$script);
        
        // Include geolocate styling
        $xoTheme->addScript("https://maps.googleapis.com/maps/api/js?sensor=false&language="._LANGCODE);
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ui.geo_autocomplete.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ui.geo_autocomplete_now.js');
        
        smallworld_includeScripts ();
        
    }
    
    /**
     * Include script files based on $page
     * @return void
     */
     
     function smallworld_includeScripts () {
        global $xoopsUser, $xoopsConfig, $xoTheme;
            $page = basename ($_SERVER['PHP_SELF'],".php");
            switch ($page) {
                case 'register':
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.validate.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.validation.functions.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.stepy.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');  
                break;
                
                case 'publicindex':
              		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ajaxupload.3.5.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.avatar_helper.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.bookmark.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');  
                break;
                
                case 'permalink':
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.innerfade.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.bookmark.js');  
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');                    
                break;
                
                case 'index':
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ajaxupload.3.5.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.avatar_helper.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.bookmark.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');                 
                break;
                
                case 'img_upload':
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/bootstrap.min.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/style.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/bootstrap-responsive.min.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/bootstrap-image-gallery.min.css');

                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/vendor/jquery.ui.widget.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/tmpl.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/load-image.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/canvas-to-blob.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/bootstrap.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/bootstrap-image-gallery.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.iframe-transport.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.fileupload.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.fileupload-fp.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.fileupload-ui.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/main.js');    
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                break;
                
                case 'galleryshow':
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/galleriffic-5.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.galleriffic.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.history.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.opacityrollover.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/gallery_mod.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.innerfade.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');	                
                break;
                
                case 'friends':
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/apprise-1.5.full.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/jquery.fileupload-ui.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ajaxupload.3.5.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.avatar_helper.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.innerfade.js');			
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                    //$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/colorbox.css');                 
                break;
                
                case 'editprofile':                    
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.validate.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.validation.functions.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.stepy.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css'); 
                break;
                
                case 'smallworldshare':
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.innerfade.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.bookmark.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                break;   

                case 'userprofile':
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/apprise-1.5.full.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/jquery.fileupload-ui.css');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ajaxupload.3.5.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.avatar_helper.js');	
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.bookmark.js');		
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
                    $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.countdown.js');
                    $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');  
            break;
            }
     }
    
    /**
     * Check if permission is set for userid to post publicly
     * @return array
     */
     
     function smallworld_checkUserPubPostPerm () {
        global $xoopsUser,$xoopsModule;
        $check = new SmallWorldUser;       
        $UserPerPub = smallworld_GetModuleOption('smallworldshowPoPubPage');
        $allUsers = $check->allUsers();      
        if ($UserPerPub[0] != 0) {
            $pub = $UserPerPub;
        } else {
            $pub = $check->allUsers();
        }
        return $pub;
     }
    /**
     * Change @username to urls
     * @param  string $status_text
     * @return string $status_text
     */
    function linkify_twitter_status($status_text) {
        // linkify twitter users
        //$keywords = preg_split("/[\s,]+/", "hypertext language, programming");
        $status_text = preg_replace(
        '/(^|\s)@(\w+)/',
        '\1<a href="'.XOOPS_URL . '/modules/smallworld/userprofile.php?username=\2">' . '@\2</a> ',
        $status_text
        );

        return $status_text;
    }
    
    /**
     * Extract users from @tags
     * @param $txt
     * @return array @users
     **/
    function smallworld_getUidFromName ($name) 
    {
        global $xoopsDB;
        $sql = "Select userid from ".$xoopsDB->prefix('smallworld_user')." where username = '".$name."'";
        $result = $xoopsDB->queryF($sql);
        while ($r = $xoopsDB->fetchArray($result)) { 
            $id = $r['userid'];
        }
        return $id;
    }
     
    /**
     * Extract users from @tags
     * @param $txt
     * @return array @users
     **/
     function smallworld_getTagUsers ($txt, $sender, $permalink='') 
     {
        $dBase = new SmallWorldDB;
        $mail = new smallworld_mail;
        preg_match_all("/@([a-zA-Z0-9]+|\\[[a-zA-Z0-9]+\\])/", $txt, $matches);
        $users = array_unique($matches[1]);
        foreach ($users as $users) {
            $uid = smallworld_getUidFromName ($users);
            $notify = json_decode($dBase->GetSettings($uid), true);
            if (isset ($notify['notify'])) {
                if ($notify['notify'] != 0) {  
                   $mail->sendMails ($sender, $uid, 'tag', $link=$permalink, array($txt)); 
                }
            }

        }
     }
