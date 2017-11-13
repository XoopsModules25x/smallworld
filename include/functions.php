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
//require_once __DIR__ . '/common.php';

/*
Get array of timestamps based on the timetype configured in preferences
*/
/**
 * @return array
 */
function SmallworldGetTimestampsToForm()
{
    $timearray = [];
    $start     = 1900;
    $end       = date('Y');
    for ($i = $start; $i <= $end; ++$i) {
        $key             = $i;
        $timearray[$key] = $i;
    }
    ksort($timearray);
    return $timearray;
}

// Clean vars or arrays
// If array check for magicQuotes.
// Pass string to Smallworld_cleanup_string
/**
 * @param $text
 * @return array|mixed
 */
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
/**
 * @param $text
 * @return mixed
 */
function Smallworld_cleanup_string($text)
{
    $myts = MyTextSanitizer::getInstance();
    $text = $myts->displayTarea($text, $html = 1, $smiley = 1, $xcode = 1, $image = 1, $br = 1);
    return $text;
}

// clean Array for mysql insertion
// or send string to Smallworld_sanitize_string
/**
 * @param $text
 * @return array|mixed
 */
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

/**
 * @param $value
 * @return mixed
 */
function Smallworld_sanitize_string($value)
{
    $myts = MyTextSanitizer::getInstance();
    if (get_magic_quotes_gpc()) {
        $value = $myts->stripSlashesGPC($value);
    } else {
        $value = $GLOBALS['xoopsDB']->escape($value);
    }
    return $value;
}

/**
 * @param $array
 * @return array
 */
function Smallworld_DateOfArray($array)
{
    $data = [];
    foreach ($array as $k => $v) {
        $data[$k] = strtotime($v);
    }
    return $data;
}

/**
 * @param $array
 * @return array
 */
function Smallworld_YearOfArray($array)
{
    $data = [];
    foreach ($array as $k => $v) {
        $data[$k] = $v;
    }
    return $data;
}

/**
 * @param $folderUrl
 */
function Smallworld_CreateIndexFiles($folderUrl)
{
    $myts = MyTextSanitizer::getInstance();
    file_put_contents($folderUrl . 'index.html', '<script>history.go(-1);</script>');
}

/**
 * @param string $glue
 * @param        $pieces
 * @return string
 */
function smallworld_ImplodeArray($glue = ', ', $pieces)
{
    return implode($glue, $pieces);
}

// recursively reduces deep arrays to single-dimensional arrays
// $preserve_keys: (0=>never, 1=>strings, 2=>always)
/**
 * @param       $array
 * @param int   $preserve_keys
 * @param array $newArray
 * @return array
 */
function Smallworld_array_flatten($array, $preserve_keys = 1, &$newArray = [])
{
    foreach ($array as $key => $child) {
        if (is_array($child)) {
            $newArray = Smallworld_array_flatten($child, $preserve_keys, $newArray);
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
/**
 * @param $birth
 * @return false|string
 */
function Smallworld_Birthday($birth)
{
    list($year, $month, $day) = explode('-', $birth);
    $yearDiff  = date('Y') - $year;
    $monthDiff = date('m') - $month;
    $dayDiff   = date('d') - $day;
    if (0 == $monthDiff) {
        if ($dayDiff < 0) {
            $yearDiff--;
        }
    }
    if ($monthDiff < 0) {
        $yearDiff--;
    }
    return $yearDiff;
}

/**
 * @param $check
 * @return bool
 */
function smallworld_isset_or($check)
{
    global $xoopsDB, $xoopsUser;
    $query  = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE username = '" . $check . "'";
    $result = $xoopsDB->queryF($query);
    while ($row = $xoopsDB->fetchArray($result)) {
        if ('' == $row['userid']) {
            return false;
        } else {
            return $row['userid'];
        }
    }
}

//Srinivas Tamada http://9lessons.info
//Loading Comments link with load_updates.php
/**
 * @param $session_time
 * @return string
 */
function smallworld_time_stamp($session_time)
{
    global $xoopsConfig;
    $time_difference = time() - $session_time;
    $seconds         = $time_difference;
    $minutes         = round($time_difference / 60);
    $hours           = round($time_difference / 3600);
    $days            = round($time_difference / 86400);
    $weeks           = round($time_difference / 604800);
    $months          = round($time_difference / 2419200);
    $years           = round($time_difference / 29030400);
    if ($seconds <= 60) {
        $t = "$seconds" . _SMALLWORLD_SECONDSAGO;
    } elseif ($minutes <= 60) {
        if (1 == $minutes) {
            $t = _SMALLWORLD_ONEMINUTEAGO;
        } else {
            $t = "$minutes" . _SMALLWORLD_MINUTESAGO;
        }
    } elseif ($hours <= 24) {
        if (1 == $hours) {
            $t = _SMALLWORLD_ONEHOURAGO;
        } else {
            $t = "$hours" . _SMALLWORLD_HOURSAGO;
        }
    } elseif ($days <= 7) {
        if (1 == $days) {
            $t = _SMALLWORLD_ONEDAYAGO;
        } else {
            $t = "$days" . _SMALLWORLD_DAYSAGO;
        }
    } elseif ($weeks <= 4) {
        if (1 == $weeks) {
            $t = _SMALLWORLD_ONEWEEKAGO;
        } else {
            $t = "$weeks" . _SMALLWORLD_WEEKSAGO;
        }
    } elseif ($months <= 12) {
        if (1 == $months) {
            $t = _SMALLWORLD_ONEMONTHAGO;
        } else {
            $t = "$months" . _SMALLWORLD_MONTHSAGO;
        }
    } else {
        if (1 == $years) {
            $t = _SMALLWORLD_ONEYEARAGO;
        } else {
            $t = "$years" . _SMALLWORLD_YEARSAGO;
        }
    }
    return $t;
}

// Return only url/link
// If url is image link return <img>
/**
 * @param $text
 * @param $uid
 * @return string
 */
function smallworld_tolink($text, $uid)
{
    global $xoopsUser;
    $ext       = substr($text, -4, 4);
    $ext2      = substr($text, -5, 5);
    $xoopsUser = new \XoopsUser($uid);
    $usr       = new $xoopsUser($uid);

    $userID   = $xoopsUser->getVar('uid');
    $user     = new \XoopsUser($userID);
    $username = $user->getVar('uname');
    $gallery  = XOOPS_URL . '/modules/smallworld/galleryshow.php?username=' . $usr->getVar('uname');

    if (in_array($ext, ['.jpg', '.bmp', '.gif', '.png']) || in_array($ext, ['.JPG', '.BMP', '.GIF', '.PNG']) || in_array($ext2, ['.jpeg'])) {
        if (false !== strpos($text, 'UPLIMAGE')) {
            $text = str_replace('UPLIMAGE', '', $text);
            $text = preg_replace(
                '/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                                 '<span class="smallworldUplImgTxt"><br><img class="smallworldAttImg" src="\\1"><br><br><a id="smallworldUplImgLnk" href="' . $gallery . '" target="_SELF">' . $usr->getVar('uname') . _SMALLWORLD_UPLOADEDSOMEIMAGES . '</a><br></span>',
                $text
            );
            $text = preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<a href="\\1">lala</a>', $text);
            $text = preg_replace(
                '/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                                 '\\1<span class="smallworldUplImgTxt"><br><img class="smallworldAttImg" src="//\\2"><br><br><a id="smallworldUplImgLnk" href="' . $gallery . '" target="_SELF">' . $username . _SMALLWORLD_UPLOADEDSOMEIMAGES . '</a><br></span>',
                $text
            );
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        } else {
            $text = preg_replace('/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<img class="smallworldAttImg" src="\\1"><a class="smallworldAttImgTxt" href="\\1">' . _SMALLWORLD_CLICKIMAGETHUMB . ' </a><br>', $text);
            $text = preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<img class="smallworldAttImg" src="\\1"><a class="smallworldAttImgTxt" href="\\1">' . _SMALLWORLD_CLICKIMAGETHUMB . ' </a><br>', $text);
            $text = preg_replace('/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '\\1<img class="smallworldAttImg" src="//\\2"><a class="smallworldAttImgTxt" href="//\\2">' . _SMALLWORLD_CLICKIMAGETHUMB . '</a><br>', $text);
            $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        }
    } else {
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = ' ' . $text;
        $text = str_replace('UPLIMAGE', '', $text);
    }
    return linkify_twitter_status($text);
}

/**
 * @param $text
 * @return mixed
 */
function Smallworld_stripWordsKeepUrl($text)
{
    preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<div class=".embed"><a href="\\1">\\1</a></div>', $text);
    return $text;
}

/**
 * @param $num
 * @param $name
 * @return string
 */
function Smallworld_sociallinks($num, $name)
{
    if (0 == $num) {
        $image = '<img title="Msn" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/msn.png">';
        $link  = '<a title="Msn" id="Smallworld_socialnetwork" target="_blank" href="http://members.msn.com/' . $name . '">';
    }
    if (1 == $num) {
        $image = '<img title="facebook" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/facebook.png">';
        $link  = '<a title="facebook" id="Smallworld_socialnetwork" target="_blank" href="http://www.facebook.com/' . $name . '">';
    }
    if (2 == $num) {
        $image = '<img title="GooglePlus" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/googleplus.png">';
        $link  = '<a title="GooglePlus" id="Smallworld_socialnetwork" target="_blank" href="https://plus.google.com/' . $name . '">';
    }
    if (3 == $num) {
        $image = '<img title="Icq" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/icq.png">';
        $link  = '<a title="icq" id="Smallworld_socialnetwork" target="_blank" href="http://www.icq.com/people/' . $name . '/">';
    }
    if (4 == $num) {
        $image = '<img title="Skype" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/skype.png">';
        $link  = '<a title="Skype" id="Smallworld_socialnetwork" target="_blank" href="skype:' . $name . '?userinfo">';
    }
    if (5 == $num) {
        $image = '<img title="Twitter" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/twitter.png">';
        $link  = '<a title="Twitter" id="Smallworld_socialnetwork" target="_blank" href="http://twitter.com/#!/' . $name . '">';
    }
    if (6 == $num) {
        $image = '<img title="MySpace" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/myspace.png">';
        $link  = '<a title="MySpace" id="Smallworld_socialnetwork" target="_blank" href="http://www.myspace.com/' . $name . '">';
    }
    if (7 == $num) {
        $image = '<img title="Xoops" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/xoops.png">';
        $link  = '<a title="Xoops" id="Smallworld_socialnetwork" target="_blank" href="https://xoops.org/modules/profile/userinfo.php?uid=' . $name . '">';
    }
    if (8 == $num) {
        $image = '<img title="Yahoo Messenger" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/yahoo.png">';
        $link  = '<a title="Yahoo Messenger" id="Smallworld_socialnetwork" target="_blank" href="ymsgr:sendim?' . $name . '">';
    }
    if (9 == $num) {
        $image = '<img title="Youtube" id="Smallworld_socialnetworkimg" src="' . XOOPS_URL . '/modules/smallworld/assets/images/socialnetworkicons/youtube.png">';
        $link  = '<a title="Youtube" id="Smallworld_socialnetwork" target="_blank" href="http://www.youtube.com/user/' . $name . '">';
    }
    return $image . $link;
}

/**
 * @param        $option
 * @param string $repmodule
 * @return bool|mixed
 */
function smallworld_GetModuleOption($option, $repmodule = 'smallworld')
{
    global $xoopsModuleConfig, $xoopsModule;
    static $tbloptions = [];
    if (is_array($tbloptions) && array_key_exists($option, $tbloptions)) {
        return $tbloptions[$option];
    }
    $retval = false;
    if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
        if (isset($xoopsModuleConfig[$option])) {
            $retval = $xoopsModuleConfig[$option];
        }
    } else {
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname($repmodule);
        $configHandler = xoops_getHandler('config');
        if ($module) {
            $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
            if (isset($moduleConfig[$option])) {
                $retval = $moduleConfig[$option];
            }
        }
    }
    $tbloptions[$option] = $retval;
    return $retval;
}

/**
 * Check image extension and users gender. If image is legal image extension return avatar,
 * else return default gender based image
 * @param int    $userid
 * @param string $image
 * @returns string
 */
function smallworld_getAvatarLink($userid, $image)
{
    global $xoopsUser, $xoopsDB;
    $ext     = pathinfo(strtolower($image), PATHINFO_EXTENSION);
    $sql     = 'SELECT gender FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE userid = '" . (int)$userid . "'";
    $result  = $xoopsDB->queryF($sql);
    $counter = $xoopsDB->getRowsNum($result);
    if (0 == $counter) {
        $gender = '';
    } else {
        while ($row = $xoopsDB->fetchArray($result)) {
            $gender = $row['gender'];
        }
    }

    //$image = ($image == 'blank.gif') ? '' : $image;

    if (preg_match('/avatars/i', $image)) {
        $link = XOOPS_UPLOAD_URL . '/' . $image;
    } else {
        $link = $image;
    }

    if (in_array($ext, ['jpg', 'bmp', 'gif', 'png', 'jpeg']) || '' == $image || 'blank.gif' == $image) {
        if ('1' == $gender) {
            $link = XOOPS_URL . '/modules/smallworld/assets/images/ano_woman.png';
        }

        if ('2' == $gender) {
            $link = XOOPS_URL . '/modules/smallworld/assets/images/ano_man.png';
        }

        if ('' == $gender) {
            $link = XOOPS_URL . '/modules/smallworld/assets/images/genderless.png';
        }
    }
    //echo $link."<br>";
    return $link;
}

/**
 * @return bool
 */
function smallworld_checkForXim()
{
    $filename = XOOPS_ROOT_PATH . '/modules/xim/chat.php';
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
function smallworld_XIMversion()
{
    global $xoopsDB;
    $sql    = 'SELECT version FROM ' . $xoopsDB->prefix('modules') . " WHERE dirname = 'xim'";
    $result = $xoopsDB->queryF($sql);
    if ($xoopsDB->getRowsNum($result) > 0) {
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
/**
 * @param $msg_id_fk
 * @return mixed
 */
function Smallworld_getOwnerFromComment($msg_id_fk)
{
    global $xoopsDB;
    $sql    = 'SELECT uid_fk FROM ' . $xoopsDB->prefix('smallworld_messages') . " WHERE msg_id = '" . $msg_id_fk . "'";
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $owner = $r['uid_fk'];
    }
    return $owner;
}

// Get username from userID
/**
 * @param $userID
 * @return mixed
 */
function Smallworld_getName($userID)
{
    global $xoopsUser, $xoopsDB;
    $sql    = 'SELECT username FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE userid = '" . (int)$userID . "'";
    $result = $xoopsDB->queryF($sql);
    while ($row = $xoopsDB->fetchArray($result)) {
        $name = $row['username'];
    }
    return $name;
}

// Check if user has been taken down for inspection by admin
// Userid = user id of user to check
// return array
/**
 * @param $userid
 * @return array
 */
function Smallworld_isInspected($userid)
{
    global $xoopsDB;
    $data   = [];
    $sql    = 'SELECT inspect_start, inspect_stop FROM ' . $xoopsDB->prefix('smallworld_admin') . " WHERE userid = '" . $userid . "' AND (inspect_start+inspect_stop) > " . time() . '';
    $result = $xoopsDB->queryF($sql);
    if ($xoopsDB->getRowsNum($result) > 0) {
        while ($row = $xoopsDB->fetchArray($result)) {
            $data['inspect']   = 'yes';
            $data['totaltime'] = ($row['inspect_start'] + $row['inspect_stop']) - time();
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
    $sql    = 'UPDATE ' . $xoopsDB->prefix('smallworld_admin') . " SET inspect_start = '', inspect_stop = '' WHERE (inspect_start+inspect_stop) <= " . time() . '';
    $result = $xoopsDB->queryF($sql);
}

// Function to get sum of users in you following array
// Used to calculate new message flash in jQuery intval fetch
/**
 * @return mixed
 */
function smallworld_getCountFriendMessagesEtc()
{
    global $xoopsUser, $xoopsDB;
    $user      = new \XoopsUser;
    $Wall      = new smallworld\WallUpdates();
    $userid    = $xoopsUser->getVar('uid');
    $followers = Smallworld_array_flatten($Wall->getFollowers($userid), 0);
    if (1 == smallworld_GetModuleOption('usersownpostscount', $repmodule = 'smallworld')) {
        array_push($followers, $userid);
    }
    $ids    = implode(',', $followers);
    $sql    = 'SELECT COUNT(*) AS total '
              . ' FROM ( '
              . ' SELECT com_id , count( * ) as comments FROM '
              . $xoopsDB->prefix('smallworld_comments')
              . " WHERE uid_fk IN ($ids) Group by com_id "
              . ' UNION ALL '
              . ' Select msg_id , count( * ) as messages FROM '
              . $xoopsDB->prefix('smallworld_messages')
              . " WHERE uid_fk IN ($ids) group by msg_id "
              . ' ) as d';
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $total = $r['total'];
    }
    return $total;
}

// Function to get sum of users in you following array
// Used to calculate new message flash in jQuery intval fetch
/**
 * @param $id
 * @return mixed
 */
function smallworld_countUsersMessages($id)
{
    global $xoopsUser, $xoopsDB;
    $user   = new XoopsUser;
    $Wall   = new smallworld\WallUpdates();
    $sql    = 'SELECT COUNT(*) AS total '
              . ' FROM ( '
              . ' SELECT com_id , count( * ) AS comments FROM '
              . $xoopsDB->prefix('smallworld_comments')
              . ' WHERE uid_fk = '
              . (int)$id . ' GROUP BY com_id '
              . ' UNION ALL '
              . ' SELECT msg_id , count( * ) AS messages FROM '
              . $xoopsDB->prefix('smallworld_messages')
              . ' WHERE uid_fk = '
              . (int)$id . 'group BY msg_id '
              . ' ) AS d';
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $total = $r['total'];
    }
    return $total;
}

// Get the three newest members to array
/**
 * @return array
 */
function smallworld_Stats_newest()
{
    global $xoopsDB, $xoopsUser;
    $nu     = [];
    $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . ' ORDER BY regdate DESC LIMIT 3';
    $result = $xoopsDB->queryF($sql);
    if ($xoopsDB->getRowsNum($result) > 0) {
        $i = 0;
        while ($r = $xoopsDB->fetchArray($result)) {
            $nu[$i]['userid']         = $r['userid'];
            $nu[$i]['username']       = $r['username'];
            $nu[$i]['regdate']        = date('d-m-Y', $r['regdate']);
            $nu[$i]['username_link']  = "<a href = '" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $r['username'] . "'>";
            $nu[$i]['username_link']  .= $r['username'] . ' (' . $r['realname'] . ') [' . $nu[$i]['regdate'] . '] </a>';
            $nu[$i]['userimage']      = $r['userimage'];
            $nu[$i]['userimage_link'] = smallworld_getAvatarLink($r['userid'], Smallworld_Gravatar($r['userid']));
            ++$i;
        }
    }
    return $nu;
}

//Avatar Image
/**
 * @param $uid
 * @return string
 */
function Smallworld_Gravatar($uid)
{
    global $xoopsUser, $xoopsDB;
    $image  = '';
    $sql    = 'SELECT userimage FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE userid = '" . $uid . "'";
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $image = $r['userimage'];
    }

    $image = ('' == $image || 'blank.gif' === $image) ? smallworld_getAvatarLink($uid, $image) : $image;

    $type = [
        1 => 'jpg',
        2 => 'jpeg',
        3 => 'png',
        4 => 'gif'
    ];

    $ext = explode('.', $image);

    if (@!in_array(strtolower($ext[1]), $type) || '' == $image) {
        $avatar = '';
    } else {
        $avatar = $image;
    }
    return $avatar;
}

// find user with most posted messages
/**
 * @return array
 */
function Smallworld_mostactiveusers_allround()
{
    global $xoopsDB, $xoopsUser;
    $msg     = [];
    $sql     = 'SELECT uid_fk, COUNT( * ) as cnt ';
    $sql     .= 'FROM ( ';
    $sql     .= 'SELECT uid_fk ';
    $sql     .= 'FROM ' . $xoopsDB->prefix('smallworld_messages') . ' ';
    $sql     .= 'UNION ALL SELECT uid_fk ';
    $sql     .= 'FROM ' . $xoopsDB->prefix('smallworld_comments') . ' ';
    $sql     .= ') AS u ';
    $sql     .= 'GROUP BY uid_fk ';
    $sql     .= 'ORDER BY count( * ) DESC limit 3';
    $result  = $xoopsDB->queryF($sql);
    $counter = $xoopsDB->getRowsNum($result);
    if ($counter < 1) {
    } else {
        $counter = 1;
        while ($row = $xoopsDB->fetchArray($result)) {
            $msg[$counter]['counter']       = $counter;
            $msg[$counter]['img']           = smallworld_getAvatarLink($row['uid_fk'], Smallworld_Gravatar($row['uid_fk']));
            $msg[$counter]['msgs']          = _SMALLWORLD_TOTALPOSTS . ' : ' . $row['cnt'];
            $msg[$counter]['cnt']           = $row['cnt'];
            $msg[$counter]['username']      = $xoopsUser->getUnameFromId($row['uid_fk']);
            $msg[$counter]['username_link'] = "<a href = '" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $msg[$counter]['username'] . "'>";
            $msg[$counter]['username_link'] .= $msg[$counter]['username'] . ' (' . $msg[$counter]['msgs'] . ')</a>';
            ++$counter;
        }
    }
    return $msg;
}

// Find worst rated users overall
/**
 * @return array
 */
function Smallworld_worstratedusers()
{
    global $xoopsUser, $xoopsDB;
    $array   = [];
    $counter = 1;
    $sql     = 'SELECT owner, (';
    $sql     .= 'sum( up ) - sum( down )';
    $sql     .= ') AS total';
    $sql     .= ' FROM ' . $xoopsDB->prefix('smallworld_vote') . '';
    $sql     .= ' GROUP BY owner ORDER by total ASC LIMIT 5';
    $result  = $xoopsDB->queryF($sql);
    while ($row = $xoopsDB->fetchArray($result)) {
        $array[$counter]['counter']   = $counter;
        $array[$counter]['img']       = smallworld_getAvatarLink($row['owner'], Smallworld_Gravatar($row['owner']));
        $array[$counter]['user']      = $xoopsUser->getUnameFromId($row['owner']);
        $array[$counter]['rating']    = $row['total'];
        $array[$counter]['user_link'] = "<a href = '" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $array[$counter]['user'] . "'>";
        $array[$counter]['user_link'] .= $array[$counter]['user'] . ' (' . $array[$counter]['rating'] . ')</a>';
        ++$counter;
    }
    return $array;
}

// Find best rated users overall
/**
 * @return array
 */
function Smallworld_topratedusers()
{
    global $xoopsUser, $xoopsDB;
    $array   = [];
    $counter = 1;
    $sql     = 'SELECT owner, (';
    $sql     .= 'sum( up ) - sum( down )';
    $sql     .= ') AS total';
    $sql     .= ' FROM ' . $xoopsDB->prefix('smallworld_vote') . '';
    $sql     .= ' GROUP BY owner ORDER by total DESC LIMIT 5';
    $result  = $xoopsDB->queryF($sql);
    while ($row = $xoopsDB->fetchArray($result)) {
        $array[$counter]['counter']   = $counter;
        $array[$counter]['img']       = smallworld_getAvatarLink($row['owner'], Smallworld_Gravatar($row['owner']));
        $array[$counter]['user']      = $xoopsUser->getUnameFromId($row['owner']);
        $array[$counter]['rating']    = $row['total'];
        $array[$counter]['user_link'] = "<a href = '" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $array[$counter]['user'] . "'>";
        $array[$counter]['user_link'] .= $array[$counter]['user'] . ' (' . $array[$counter]['rating'] . ')</a>';
        ++$counter;
    }
    return $array;
}

/**
 * @return array
 */
function smallworld_nextBirthdays()
{
    global $xoopsDB, $xoopsUser;
    $now     = date('d-m');
    $res     = [];
    $sql     = 'SELECT userid, username, userimage, realname, birthday, CURDATE(),'
               . ' DATE_FORMAT(birthday, "%d / %m") AS daymon , '
               . ' (YEAR(CURDATE())-YEAR(birthday))'
               . ' - (RIGHT(CURDATE(),5)<RIGHT(birthday,5))'
               . ' AS age_now'
               . ' FROM '
               . $xoopsDB->prefix('smallworld_user')
               . ' WHERE right(birthday,5) = right(CURDATE(),5)'
               . ' ORDER BY MONTH( birthday ) , DAY( birthday ) '
               . ' LIMIT 10 ';
    $result  = $xoopsDB->queryF($sql);
    $counter = $xoopsDB->getRowsNum($result);
    $i       = 0;
    while ($r = $xoopsDB->fetchArray($result)) {
        $res[$i]['amount']        = $counter;
        $res[$i]['userid']        = $r['userid'];
        $res[$i]['userimage']     = smallworld_getAvatarLink($r['userid'], Smallworld_Gravatar($r['userid']));
        $res[$i]['birthday']      = $r['daymon'];
        $res[$i]['agenow']        = $r['age_now'];
        $res[$i]['username']      = $xoopsUser->getUnameFromId($r['userid']);
        $res[$i]['username_link'] = "<a href = '" . XOOPS_URL . '/modules/smallworld/userprofile.php?username=' . $res[$i]['username'] . "'>";
        $res[$i]['username_link'] .= $res[$i]['username'] . ' (' . $r['daymon'] . ') ' . $r['age_now'] . ' ' . _SMALLWORLD_BDAY_YEARS;
        $res[$i]['username_link'] .= '</a>';
        ++$i;
    }
    return $res;
}

/*
 * Return date format (YYYY-MM-DD) for MySql
 * @param date $stringDate
 * $returns date
*/
/**
 * @param $stringDate
 * @return string
 */
function Smallworld_euroToUsDate($stringDate)
{
    if (0 != $stringDate || '' != $stringDate) {
        $theData = explode('-', trim($stringDate));
        $ret     = $theData[2] . '-' . $theData[1] . '-' . $theData[0];
        return $ret;
    } else {
        return '1900-01-01';
    }
}

/*
 * Return date format (DD-MM-YYYY) for display
 * @param date $stringDate
 * $returns date
*/
/**
 * @param $stringDate
 * @return string
 */
function Smallworld_UsToEuroDate($stringDate)
{
    if (0 != $stringDate || '' != $stringDate) {
        $theData = explode('-', trim($stringDate));
        $ret     = $theData[2] . '-' . $theData[1] . '-' . $theData[0];
        return $ret;
    } else {
        return '01-01-1900';
    }
}

/**
 * @return array
 */
function smallworld_sp()
{
    $sp               = [];
    $sp[0]['spimage'] = "<img id = 'smallworld_img_sp' src = '" . XOOPS_URL . "/modules/smallworld/assets/images/sp.png' height='30px' width='30px' >";
    return $sp;
}

//Check privacy settings in permapage
/**
 * @param $id
 * @return mixed
 */
function smallworldCheckPriv($id)
{
    global $xoopsDB;
    $public = 'SELECT priv FROM ' . $xoopsDB->prefix('smallworld_messages') . ' WHERE msg_id = ' . $id . '';
    $result = $xoopsDB->queryF($public);
    while ($row = $xoopsDB->fetchArray($result)) {
        $priv = $row['priv'];
    }
    return $priv;
}

// Function to calculate remaining seconds until user's birthday
// Input $d : date('Y-m-d') format
// return seconds until date at midnight, or
// return 0 if date ('Y-m-d') is equal to today
/**
 * @param $d
 * @return bool|int|string
 */
function smallworldNextBDaySecs($d)
{
    $olddate   = substr($d, 4);
    $exactdate = date('Y') . '' . $olddate;
    $newdate   = date('Y') . '' . $olddate . ' 00:00:00';
    $nextyear  = date('Y') + 1 . '' . $olddate . ' 00:00:00';
    if ($exactdate != date('Y-m-d')) {
        if ($newdate > date('Y-m-d H:i:s')) {
            $start_ts = strtotime($newdate);
            $end_ts   = strtotime(date('Y-m-d H:i:s'));
            $diff     = $end_ts - $start_ts;
            $n        = round($diff);
            $return   = substr($n, 1);
            return $return;
        } else {
            $start_ts = strtotime($nextyear);
            $end_ts   = strtotime(date('Y-m-d H:i:s'));
            $diff     = $end_ts - $start_ts;
            $n        = round($diff);
            $return   = substr($n, 1);
            return $return;
        }
    } else {
        return 0;
    }
}

//Function to get value from xoopsConfig array
/**
 * @param $key
 * @param $array
 * @return int
 */
function smallworldGetValfromArray($key, $array)
{
    $ar  = smallworld_GetModuleOption($array, $repmodule = 'smallworld');
    $ret = 0;
    if (in_array($key, $ar, true)) {
        $ret = 1;
        return $ret;
    } else {
        return 0;
    }
}

//Function to resize images proportionally
// Using imagesize($imageurl) returns $img[0], $img[1]
// Target = new max height or width in px
/**
 * @param $width
 * @param $height
 * @param $target
 * @return string
 */
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
    $width  = round($width * $percentage);
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
    if ('blank.gif' !== $bn
        || 'blank.png' !== $bn
        || 'blank.jpg' !== $bn
        || 'image_missing.png' !== $bn) {
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
                $result         = curl_exec($ch);
                $img            = imagecreatefromstring($result);
                $imagesize['0'] = imagesx($img);
                $imagesize['1'] = imagesy($img);
            } else {
                $imagesize['0'] = $w;
                $imagesize['1'] = $h;
            }
        }
        return $imagesize;
    } else {
        return [0 => '0', 1 => '0'];
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
    if (isset($req) || !empty($req)) {
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
    $sql    = 'SELECT imgname FROM ' . $xoopsDB->prefix('smallworld_images') . ' WHERE userid = ' . $userid . ' AND time BETWEEN UNIX_TIMESTAMP( ) - 3000 AND UNIX_TIMESTAMP() ORDER BY rand() LIMIT 1';
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
    $protocol   = false === stripos($_SERVER['SERVER_PROTOCOL'], 'https') ? 'http' : 'https';
    $host       = $_SERVER['HTTP_HOST'];
    $script     = $_SERVER['SCRIPT_NAME'];
    $params     = $_SERVER['QUERY_STRING'];
    $currentUrl = $protocol . '://' . $host;
    return $currentUrl;
}

/**
 * @Get htmlentities
 * @param $text
 * @return translated string to utf-8
 */
function smallworld_decodeEntities($text)
{
    $text = html_entity_decode($text, ENT_QUOTES, 'ISO-8859-1'); #NOTE: UTF-8 does not work!
    $text = preg_replace('/&#(\d+);/me', "chr(\\1)", $text); #decimal notation
    $text = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $text);  #hex notation
    return $text;
}

/**
 * @Get string and shorten so contains only ? chars
 * @param $text
 * @param $chars
 * @return string
 */
function smallworld_shortenText($text, $chars)
{
    $text .= ' ';
    $text = substr($text, 0, $chars);
    $text = substr($text, 0, strrpos($text, ' '));
    $text .= '...';
    return $text;
}

/**
 * @Get languagefile if constants are not defined
 * @param $def
 * @param $file
 * return file include
 */
function smallworld_isDefinedLanguage($def, $file)
{
    global $xoopsConfig;
    if (!defined($def)) {
        // language files (main.php)
        if (file_exists(XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/' . $file)) {
            require_once XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/' . $file;
        } else {
            // fallback english
            require_once XOOPS_ROOT_PATH . '/modules/smallworld/language/english/' . '/' . $file;
        }
    }
}

/**
 * @Check if smallworld is for private or public access
 * return value for config
 */
function smallworld_checkPrivateOrPublic()
{
    global $xoopsUser;
    $opt = [];
    $set = smallworld_GetModuleOption('smallworldprivorpub', $repmodule = 'smallworld');
    if (0 != $set) {
        $opt['access'] = 1;
    } else {
        $opt['access'] = 0;
    }
    if ($xoopsUser) {
        $id               = $xoopsUser->getVar('uid');
        $user             = new \XoopsUser($id);
        $check            = new smallworld\SmallWorldUser;
        $profile          = $check->CheckIfProfile($id);
        $opt['xoopsuser'] = 1;
        if (0 != $profile) {
            $opt['smallworlduser'] = 1;
        } else {
            $opt['smallworlduser'] = 0;
        }
    } else {
        $opt['xoopsuser']      = 0;
        $opt['smallworlduser'] = 0;
    }
    return $opt;
}

/**
 * @return array of groups
 * return array
 *
 */
function smallworld_xv_getGroupd()
{
    $db     = XoopsDatabaseFactory::getDatabaseConnection();
    $myts   = MyTextSanitizer::getInstance();
    $sql    = 'SELECT userid, username FROM ' . $db->prefix('smallworld_user') . ' ORDER BY userid';
    $result = $db->queryF($sql);
    $num    = $db->getRowsNum($result);
    if (0 == $num) {
        $ndata = [0 => _MI_SMALLWORLD_ALL];
    } else {
        while ($r = $db->fetchArray($result)) {
            $data[$r['userid']] = $r['username'];
        }
        $ndata = array_merge([0 => _MI_SMALLWORLD_ALL], $data);
    }
    return $ndata;
}

/**
 * Set javascript vars to theme using various values
 * Return void
 */
function smallworld_SetCoreScript()
{
    global $xoopsUser, $xoopsConfig, $xoTheme;

    $moduleHandler = xoops_getHandler('module');
    $module        = $moduleHandler->getByDirname('smallworld');
    $configHandler = xoops_getHandler('config');
    if ($module) {
        $moduleConfig = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
    }

    // IF logged in define xoops / smallworld user id
    $myid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;

    // Check if option is et to allow public reading
    $pub    = smallworld_checkPrivateOrPublic();
    $access = $pub['access'];

    // GET various variables from language folder
    if (file_exists(XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/js/variables.js')) {
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/js/variables.js');
    } else {
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/english/js/variables.js');
    }

    // Check if USER is smallworld-registered user
    $chkUser = new smallworld\SmallWorldUser;
    $ChkProf = $xoopsUser ? $chkUser->CheckIfProfile($myid) : 0;

    // Check if there are requests pending
    $count_invit = $xoopsUser ? count($chkUser->getRequests($myid)) : 0;

    // Get module config for validation and place in javascript
    $validate = $moduleConfig['validationstrenght'];

    // Check to see if smallworld should use username links to point to default xoops or smallworld
    $takeoverlinks   = $moduleConfig['takeoveruserlinks'];
    $fieldstoshow    = array_flip(smallworld_GetModuleOption('smallworldusethesefields', $repmodule = 'smallworld'));
    $useverification = smallworld_GetModuleOption('smallworldmandatoryfields', $repmodule = 'smallworld');
    $smallworldUV    = implode(',', $useverification);

    // Use googlemaps ?
    $googlemaps = $moduleConfig['smallworldUseGoogleMaps'];

    // Get users messages count based on users followerArray
    $getUserMsgNum = $xoopsUser ? smallworld_getCountFriendMessagesEtc() : 0;

    // Check if request url is with www or without
    $urltest   = smallworld_getHostRequest();
    $xoops_url = XOOPS_URL;
    if (false === strpos($urltest, 'www.')) {
        $xoops_url = str_replace('www.', '', $xoops_url);
    }

    // Set javascript vars but only if not already defined.
    // Check prevents multible loads
    $script = 'var Smallworld_myID;' . "\n";
    $script .= "if (typeof Smallworld_myID === 'undefined') {" . "\n";
    $script .= "var smallworld_url = '" . $xoops_url . '/modules/smallworld/' . "';\n";
    $script .= "var smallworld_uploaddir = '" . $xoops_url . '/uploads/avatars/' . "';\n";
    $script .= 'var smallworld_urlReferer = document.referrer;' . "\n";
    $script .= "var xoops_smallworld = jQuery.noConflict();\n";
    $script .= 'var Smallworld_myID = ' . $myid . ";\n";
    $script .= 'var Smallworld_userHasProfile = ' . $ChkProf . ";\n";
    $script .= 'var smallworldTakeOverLinks = ' . $takeoverlinks . ";\n";
    $script .= 'var Smallworld_geocomplete = ' . $googlemaps . ";\n";
    $script .= "var smallworldVerString = '" . $smallworldUV . "';\n";
    $script .= "var smallworlduseverification = new Array();\n";
    $script .= "smallworlduseverification = smallworldVerString.split(',');\n";
    $script .= 'var Smallworld_hasmessages = ' . $count_invit . ";\n";
    $script .= 'var smallworldvalidationstrenght = ' . $validate . ";\n";
    $script .= 'var smallworld_getFriendsMsgComCount = ' . $getUserMsgNum . ";\n";
    //$script .= "var $ = jQuery();\n";
    $script .= '}' . "\n";
    $xoTheme->addScript('', '', $script);

    // Include geolocate styling
    if (1 == $googlemaps) {
        $xoTheme->addScript('https://maps.googleapis.com/maps/api/js?sensor=false&language=' . _LANGCODE);
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/ui.geo_autocomplete.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/ui.geo_autocomplete_now.js');
    }

    smallworld_includeScripts();
}

/**
 * Include script files based on $page
 * @return void
 */

function smallworld_includeScripts()
{
    global $xoopsUser, $xoopsConfig, $xoTheme;
    $page = basename($_SERVER['PHP_SELF'], '.php');
    switch ($page) {
        case 'register':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validate.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validation.functions.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.stepy.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            break;

        case 'publicindex':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.oembed.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/wall.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/ajaxupload.3.5.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.avatar_helper.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.bookmark.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/oembed.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            break;

        case 'permalink':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.oembed.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/wall.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/oembed.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.innerfade.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.bookmark.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            break;

        case 'index':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.oembed.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/wall.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/ajaxupload.3.5.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.avatar_helper.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.bookmark.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/oembed.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/tag-it.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            break;

        case 'img_upload':
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/uploader/bootstrap.min.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/uploader/style.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/uploader/bootstrap-responsive.min.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/uploader/bootstrap-image-gallery.min.css');

            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/vendor/jquery.ui.widget.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/uploader/tmpl.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/uploader/load-image.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/uploader/canvas-to-blob.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/uploader/bootstrap.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/uploader/bootstrap-image-gallery.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.iframe-transport.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.fileupload.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.fileupload-fp.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.fileupload-ui.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/main.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            break;

        case 'galleryshow':
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/galleriffic-5.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.galleriffic.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.history.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.opacityrollover.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/gallery_mod.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.innerfade.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            break;

        case 'friends':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/apprise-1.5.full.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/jquery.fileupload-ui.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/oembed.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.oembed.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/wall.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/ajaxupload.3.5.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.avatar_helper.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.innerfade.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            //$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/assets/css/colorbox.css');
            break;

        case 'editprofile':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validate.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validation.functions.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.stepy.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            break;

        case 'smallworldshare':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.oembed.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/wall.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.innerfade.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.bookmark.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/oembed.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.colorbox.js');
            break;

        case 'userprofile':
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/apprise-1.5.full.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/jquery.fileupload-ui.css');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/oembed.css');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.oembed.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/wall.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/ajaxupload.3.5.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.avatar_helper.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.bookmark.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.colorbox.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.elastic.source.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/jquery.countdown.js');
            $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');
            break;
    }
}

/**
 * Check if permission is set for userid to post publicly
 * @return array
 */

function smallworld_checkUserPubPostPerm()
{
    global $xoopsUser, $xoopsModule;
    $check      = new smallworld\SmallWorldUser;
    $UserPerPub = smallworld_GetModuleOption('smallworldshowPoPubPage');
    $allUsers   = $check->allUsers();
    if (0 != $UserPerPub[0]) {
        $pub = $UserPerPub;
    } else {
        $pub = $check->allUsers();
    }
    return $pub;
}

/**
 * Change @username to urls
 * @param  string|null $status_text
 * @return string $status_text
 */
function linkify_twitter_status($status_text)
{
    // linkify twitter users
    //$keywords = preg_split("/[\s,]+/", "hypertext language, programming");
    $status_text = preg_replace('/(^|\s)@(\w+)/', '\1<a href="' . XOOPS_URL . '/modules/smallworld/userprofile.php?username=\2">' . '@\2</a> ', $status_text);

    return $status_text;
}

/**
 * Extract users from @tags
 * @param $name
 * @return array @users
 */
function smallworld_getUidFromName($name)
{
    global $xoopsDB;
    $sql    = 'SELECT userid FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE username = '" . $name . "'";
    $result = $xoopsDB->queryF($sql);
    while ($r = $xoopsDB->fetchArray($result)) {
        $id = $r['userid'];
    }
    return $id;
}

/**
 * Extract users from @tags
 * @param        $txt
 * @param        $sender
 * @param string $permalink
 * @return void @users
 * @throws \phpmailerException
 */
function smallworld_getTagUsers($txt, $sender, $permalink = '')
{
    $dBase = new smallworld\SmallWorldDB;
    $mail  = new smallworld\SmallWorldMail;
    preg_match_all("/@([a-zA-Z0-9]+|\\[[a-zA-Z0-9]+\\])/", $txt, $matches);
    $users = array_unique($matches[1]);
    foreach ($users as $users) {
        $uid    = smallworld_getUidFromName($users);
        $notify = json_decode($dBase->GetSettings($uid), true);
        if (isset($notify['notify'])) {
            if (0 != $notify['notify']) {
                $mail->sendMails($sender, $uid, 'tag', $link = $permalink, [$txt]);
            }
        }
    }
}

/**
 * Function to get count of messages in wall
 * @param int $uid
 * @return int $count
 */
function smallworld_countUserWallMsges($uid)
{
    $db     = XoopsDatabaseFactory::getDatabaseConnection();
    $sql    = 'SELECT message FROM ' . $db->prefix('smallworld_messages') . " WHERE uid_fk='" . $uid . "'";
    $result = $db->queryF($sql);
    $count  = $db->getRowsNum($result);
    return $count;
}
