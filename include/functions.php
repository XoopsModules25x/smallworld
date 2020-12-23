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

use Xmf\FilterInput;
use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;
use XoopsModules\Smallworld\Helper;

//require_once __DIR__ . '/common.php';
include dirname(__DIR__) . '/preloads/autoloader.php';

/**
 * Get array of timestamps based on the timetype configured in preferences
 *
 * @return array
 */
function SmallworldGetTimestampsToForm()
{
    $timearray = []; // init time array
    $end       = date('Y');
    $start     = $end - 120; // set to 120 years ago
    for ($i = $start; $i <= $end; ++$i) {
        $timearray[$i] = $i;
    }
    ksort($timearray);

    return $timearray;
}

/**
 * Clean vars or arrays
 *
 * If array check for magicQuotes.
 * Pass string to XoopsModules\Smallworld\MyFunctions\cleanupString function
 *
 * @param array|string $text
 * @return array|mixed
 */
function smallworld_cleanup($text)
{
    if (is_array($text)) {
        foreach ($text as $key => $value) {
            $text[$key] = smallworld_cleanup_string($value);
        }
    } else {
        $text = smallworld_cleanup_string($text);
    }

    return $text;
}

/**
 * Sanitize string
 *
 * @param $text
 * @return mixed
 */
function smallworld_cleanup_string($text)
{
    /** @var \MyTextSanitizer $myts */
    $myts = \MyTextSanitizer::getInstance();
    $text = $myts->displayTarea($text, $html = 1, $smiley = 1, $xcode = 1, $image = 1, $br = 1);

    return $text;
}

/**
 * Clean Array for mysql insertion
 *
 * @param string|array $text
 * @return string|array
 */
function smallworld_sanitize($text)
{
    $retVal = [];
    if (is_array($text) && 0 < count($text)) {
        foreach ($text as $key => $value) {
            $retVal[$key] = smallworld_sanitize_string($value);
        }
    } else {
        $retVal = smallworld_sanitize_string($text);
    }

    return $retVal;
}

/**
 * Santize string for insert into dB
 *
 * @param $value
 * @return mixed
 */
function smallworld_sanitize_string($value)
{
    return $GLOBALS['xoopsDB']->escape($value);
}

/**
 * @deprecated
 *
 * @param $array
 * @return array
 */
function smallworld_DateOfArray($array)
{
    $data = [];
    foreach ($array as $k => $v) {
        $data[$k] = strtotime($v);
    }

    return $data;
}

/**
 * YearOfArray
 *
 * @todo - figure out what this is suppose to do,
 * currently it doesn't really do anything.
 *
 * @param $array
 * @return array
 */
function smallworld_YearOfArray($array)
{
    $data = [];
    foreach ($array as $k => $v) {
        $data[$k] = $v;
    }

    return $data;
}

/**
 * Create an index file in
 *
 * @deprecated - no longer used
 * @param $folderUrl
 */
function smallworld_CreateIndexFiles($folder)
{
    file_put_contents($folder . 'index.html', '<script>history.go(-1);</script>');
}

/**
 * @deprecated
 * @param string $glue
 * @param array  $pieces
 * @return string
 */
function smallworld_ImplodeArray($glue, $pieces)
{
    return implode($glue, $pieces);
}

/** Recursively reduces deep arrays to single-dimensional arrays
 *
 * $preserve_keys: (0=>never, 1=>strings, 2=>always)
 *
 * @param       $array
 * @param int   $preserve_keys
 * @param array $newArray
 * @return array
 */
function smallworld_array_flatten($array, $preserve_keys = 1, &$newArray = [])
{
    foreach ($array as $key => $child) {
        if (is_array($child)) {
            $newArray = smallworld_array_flatten($child, $preserve_keys, $newArray);
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
 *
 * @param string $birth
 * @return int
 */
function smallworld_Birthday($birth)
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
 * Get SW userid from username
 *
 * @deprecated - use Smallworld\SwUserHandler::getByName() method instead
 * @param $check
 * @return bool|int
 */
function smallworld_isset_or($check)
{
    $depMsg = __FUNCTION__ . " is deprecated, use SwUserHandler::getByName() instead";
    if (isset($GLOBALS['xoopsLogger'])) {
        $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
    } else {
        trigger_error($depMsg, E_USER_WARNING);
    }
    $query  = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE username = '" . $check . "' LIMIT 1";
    $result = $GLOBALS['xoopsDB']->queryF($query);
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        if ('' == $row['userid']) {
            return false;
        }

        return $row['userid'];
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
    $time_difference = time() - $session_time;
    $seconds         = $time_difference;
    $minutes         = round($time_difference / 60);
    $hours           = round($time_difference / 3600);
    $days            = round($time_difference / 86400);
    $weeks           = round($time_difference / 604800);
    $months          = round($time_difference / 2419200);
    $years           = round($time_difference / 29030400);
    if ($seconds <= 60) {
        $t = (string)$seconds . _SMALLWORLD_SECONDSAGO;
    } elseif ($minutes <= 60) {
        if (1 == $minutes) {
            $t = _SMALLWORLD_ONEMINUTEAGO;
        } else {
            $t = (string)$minutes . _SMALLWORLD_MINUTESAGO;
        }
    } elseif ($hours <= 24) {
        if (1 == $hours) {
            $t = _SMALLWORLD_ONEHOURAGO;
        } else {
            $t = (string)$hours . _SMALLWORLD_HOURSAGO;
        }
    } elseif ($days <= 7) {
        if (1 == $days) {
            $t = _SMALLWORLD_ONEDAYAGO;
        } else {
            $t = (string)$days . _SMALLWORLD_DAYSAGO;
        }
    } elseif ($weeks <= 4) {
        if (1 == $weeks) {
            $t = _SMALLWORLD_ONEWEEKAGO;
        } else {
            $t = (string)$weeks . _SMALLWORLD_WEEKSAGO;
        }
    } elseif ($months <= 12) {
        if (1 == $months) {
            $t = _SMALLWORLD_ONEMONTHAGO;
        } else {
            $t = (string)$months . _SMALLWORLD_MONTHSAGO;
        }
    } else {
        if (1 == $years) {
            $t = _SMALLWORLD_ONEYEARAGO;
        } else {
            $t = (string)$years . _SMALLWORLD_YEARSAGO;
        }
    }

    return $t;
}

/**
 * Return only url/link
 *
 * if url is image link return <img>
 *
 * @param string $text
 * @param int $uid user id
 * @return string
 */
function smallworld_tolink($text, $uid)
{
	$helper = Helper::getInstance();
    $ext        = mb_substr($text, -4, 4);
    $ext2       = mb_substr($text, -5, 5);
    $xUser      = new \XoopsUser($uid);
    $xUserUname = $xUser->uname();
    $gallery    = $helper->url('galleryshow.php?username=' . $xUserUname);

    if (in_array(strtolower($ext), ['.jpg', '.bmp', '.gif', '.png']) || in_array(strtolower($ext2), ['.jpeg'])) {
        if (false !== mb_strpos($text, 'UPLIMAGE')) {
            $text = str_replace('UPLIMAGE', '', $text);
            $text = preg_replace(
                '/(((f|ht){1}tp:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                '<span class="smallworldUplImgTxt"><br><img class="smallworldAttImg" src="\\1"><br><br><a id="smallworldUplImgLnk" href="' . $gallery . '" target="_SELF">' . $xUserUname . _SMALLWORLD_UPLOADEDSOMEIMAGES . '</a><br></span>',
                $text
            );
            $text = preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<a href="\\1">lala</a>', $text);
            $text = preg_replace(
                '/([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i',
                '\\1<span class="smallworldUplImgTxt"><br><img class="smallworldAttImg" src="//\\2"><br><br><a id="smallworldUplImgLnk" href="' . $gallery . '" target="_SELF">' . $xUserUname . _SMALLWORLD_UPLOADEDSOMEIMAGES . '</a><br></span>',
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
function smallworld_stripWordsKeepUrl($text)
{
    preg_replace('/(((f|ht){1}tps:\/\/)[-a-zA-Z0-9@:%_\+.~#?&\/\/=]+)/i', '<div class=".embed"><a href="\\1">\\1</a></div>', $text);

    return $text;
}

/**
 * Get social image and link HTML
 *
 * @param int $num
 * @param string $name
 * @return string
 */
function smallworld_sociallinks($num, $name)
{
    /** @var \XoopsModules\Smallworld\Helper $helper */
    $helper = Helper::getInstance();
    switch ($num) {
        case 0:
            $image = '<img title="Msn" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/msn.png') . '">';
            $link  = '<a title="Msn" id="Smallworld_socialnetwork" target="_blank" href="http://members.msn.com/' . $name . '">';
            break;
        case 1:
            $image = '<img title="facebook" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/facebook.png') . '">';
            $link  = '<a title="facebook" id="Smallworld_socialnetwork" target="_blank" href="http://www.facebook.com/' . $name . '">';
            break;
        case 2:
            $image = '<img title="GooglePlus" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/googleplus.png') . '">';
            $link  = '<a title="GooglePlus" id="Smallworld_socialnetwork" target="_blank" href="https://plus.google.com/' . $name . '">';
            break;
        case 3:
            $image = '<img title="icq" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/icq.png') . '">';
            $link  = '<a title="icq" id="Smallworld_socialnetwork" target="_blank" href="http://www.icq.com/people/' . $name . '/">';
            break;
        case 4:
            $image = '<img title="Skype" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/skype.png') . '">';
            $link  = '<a title="Skype" id="Smallworld_socialnetwork" target="_blank" href="skype:' . $name . '?userinfo">';
            break;
        case 5:
            $image = '<img title="Twitter" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/twitter.png') . '">';
            $link  = '<a title="Twitter" id="Smallworld_socialnetwork" target="_blank" href="http://twitter.com/#!/' . $name . '">';
            break;
        case 6:
            $image = '<img title="MySpace" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/myspace.png') . '">';
            $link  = '<a title="MySpace" id="Smallworld_socialnetwork" target="_blank" href="http://www.myspace.com/' . $name . '">';
            break;
        case 7:
            $image = '<img title="XOOPS" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/xoops.png') . '">';
            $link  = '<a title="XOOPS" id="Smallworld_socialnetwork" target="_blank" href="https://xoops.org/modules/profile/userinfo.php?uid=' . $name . '">';
            break;
        case 8:
            $image = '<img title="Yahoo Messenger" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/yahoo.png') . '">';
            $link  = '<a title="Yahoo Messenger" id="Smallworld_socialnetwork" target="_blank" href="ymsgr:sendim?' . $name . '">';
            break;
        case 9:
            $image = '<img title="Youtube" id="Smallworld_socialnetworkimg" src="' . $helper->url('assets/images/socialnetworkicons/youtube.png') . '">';
            $link  = '<a title="Youtube" id="Smallworld_socialnetwork" target="_blank" href="http://www.youtube.com/user/' . $name . '">';
            break;
    }

    return $image . $link;
}

/**
 * @param string $option name of config option to read
 * @param string $repmodule name of module
 * @param bool   $flushFirst true causes configs to be loaded again, false read from cache
 * @return bool|mixed
 */
function smallworld_GetModuleOption($option = null, $repmodule = 'smallworld', $flushFirst = false)
{
    static $modOptions = [];
    if ($flushFirst) { // clears the 'cache', forces reload of configs
        $modOptions = [];
    }
    if (!array_key_exists($repmodule, $modOptions)) {
        //this module's options aren't set
        $modHelper = Helper::getInstance()->getHelper($repmodule);
        if (!$modHelper instanceof \Xmf\Module\Helper) { // check to see if module is installed & active
            return null;
        }
        // first time to get configs for this module so read them all into an array
        $modOptions[$repmodule] = $modHelper->getConfig();
        // now return the specific config/option requested
        if (null == $option) { // return the array if no specific option was requested
            return $modOptions[$repmodule];
        } elseif (isset($modOptions[$repmodule][$option])) {
            return $modOptions[$repmodule][$option];
        } else {
            return null;
        }
    } else {
        // module options are in 'cache' so see if the one requested is valid
        if (null == $option) { // return the array if no specific option was requested
            return $modOptions[$repmodule];
        } elseif (isset($modOptions[$repmodule][$option])) {
            return $modOptions[$repmodule][$option];
        } else {
            return null;
        }
    }

    return null;
/*
    static $tbloptions = [];
    if (is_array($tbloptions) && array_key_exists($option, $tbloptions)) {
        return $tbloptions[$option];
    }
    $retval = false;
    if (isset($GLOBALS['xoopsModuleConfig']) && (is_object($GLOBALS['xoopsModule']) && $GLOBALS['xoopsModule']->getVar('dirname') == $repmodule && $GLOBALS['xoopsModule']->getVar('isactive'))) {
        if (isset($GLOBALS['xoopsModuleConfig'][$option])) {
            $retval = $GLOBALS['xoopsModuleConfig'][$option];
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
    */
}

/**
 * Check image extension and users gender.
 *
 * If image is legal image extension return avatar, else return default gender based image
 * @deprecated
 * @param int    $userid
 * @param string $image
 * @return string
 */
function smallworld_getAvatarLink($userid, $image)
{
    $depMsg = __FUNCTION__ . " is deprecated use SwUserHandler::getAvatarLink() instead.";
    if (isset($GLOBALS['xoopsLogger'])) {
        $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
    } else {
        trigger_error($depMsg, E_USER_WARNING);
    }
    $ext     = pathinfo(mb_strtolower($image), PATHINFO_EXTENSION);
    $sql     = 'SELECT gender FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . (int)$userid . "'";
    $result  = $GLOBALS['xoopsDB']->queryF($sql);
    //$counter = $GLOBALS['xoopsDB']->getRowsNum($result);
    $gender  = Constants::GENDER_UNKNOWN;
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $gender = $row['gender'];
    }

    if (preg_match('/avatars/i', $image)) {
        $link = XOOPS_UPLOAD_URL . '/' . $image;
    } else {
        $link = $image;
    }

    if (in_array($ext, ['jpg', 'bmp', 'gif', 'png', 'jpeg']) || '' == $image || 'blank.gif' == $image) {
        switch ($gender) {
            case Constants::FEMALE:
                $pict = 'ano_woman.png';
                break;
            case Constants::MALE:
                $pict = 'ano_man.png';
                break;
            case Constants::GENDER_UNKNOWN:
            default:
                $pict = 'genderless.png';
                break;
        }
        $link = Helper::getInstance()->url("assets/images/{$pict}");
    }

    return $link;
}

/**
 * Check if Xim module is installed and active
 *
 * @return bool
 */
function smallworld_checkForXim()
{
    $ximHelper = Helper::getInstance()->getHelper('xim');

    return ($ximHelper instanceof \Xmf\Module\Helper) ? true : false;
    /*
    $filename = XOOPS_ROOT_PATH . '/modules/xim/chat.php';
    if (file_exists($filename)) {
        return true;
    }

    return false;
    */
}

/**
 * Get version number of xim module if exists
 *
 * @return int $version returns 0 if not found
 */
function smallworld_XIMversion()
{
    $version   = 0; // init version var
    $ximHelper = Helper::getInstance()->getHelper('xim');
    if ($ximHelper instanceof \Xmf\Module\Helper) {
        $version = $ximHelper->getModule()->version();
    }
    /*
    $sql    = 'SELECT version FROM ' . $GLOBALS['xoopsDB']->prefix('modules') . " WHERE dirname = 'xim'";
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    if ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $version = $r['version'];
        }
    } else {
        $version = 0;
    }
    */
    return $version;
}

/**
* Input: Message Id,
*
* Return owner of thread (original poster)
* Return Integer
*/
/**
 * @param $msg_id_fk
 * @return bool|int false if not found, integer of uid otherwise
 */
function smallworld_getOwnerFromComment($msg_id_fk)
{
    $owner = false;
    /** @todo looks like this should have LIMIT set to 1 to reduce execution time & possible ambiguity */
    $sql    = 'SELECT uid_fk FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE msg_id = '" . $msg_id_fk . "'";
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $owner = $r['uid_fk'];
    }

    return $owner;
}

/**
 * Get username from userID
 *
 * @deprecated - moved to SwUserHandler::getName() method
 * @param $userID
 * @return array
 */
function smallworld_getName($userID)
{
    $depMsg = __FUNCTION__ . " is deprecated, use SwUserHandler::getName() instead";
    if (isset($GLOBALS['xoopsLogger'])) {
        $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
    } else {
        trigger_error($depMsg, E_USER_WARNING);
    }

    $name = [];
    $sql    = 'SELECT username FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . (int)$userID . "'";
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $name = $row['username'];
    }

    return $name;
}

/**
 * Check if user has been taken down for inspection by admin
 *
 * @param int $userid user to check
 * @return array
 */
function smallworld_isInspected($userid)
{
    $data   = [];
    $sql    = 'SELECT inspect_start, inspect_stop FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " WHERE userid = '" . (int)$userid . "' AND (inspect_start+inspect_stop) > " . time() . '';
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    if ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $data['inspect']   = 'yes';
            $data['totaltime'] = ($row['inspect_start'] + $row['inspect_stop']) - time();
        }
    } else {
        $data['inspect'] = 'no';
    }

    return $data;
}

/**
 * Auto delete all inspects from DB where time has passed
 *
 * inspect_start + inspect_stop - time() is deleted if negative intval
 *
 * @return bool
 */
function SmallworldDeleteOldInspects()
{
    $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET inspect_start = '', inspect_stop = '' WHERE (inspect_start+inspect_stop) <= " . time() . '';
    $result = $GLOBALS['xoopsDB']->queryF($sql);

    return ($result) ? true : false;
}

/**
 * Function to get sum of users in your following array
 *
 * Used to calculate new message flash in jQuery intval fetch
 *
 * @return int
 */
function smallworld_getCountFriendMessagesEtc()
{
    $total     = 0;
    $wall      = new Smallworld\WallUpdates();
    $userid    = $GLOBALS['xoopsUser']->uid();
    $followers = smallworld_array_flatten($wall->getFollowers($userid), 0);
    if (1 == Helper::getInstance()->getConfig('usersownpostscount')) {
        $followers[] = $userid;
    }
    if (0 < count($followers)) {
        $ids    = implode(',', $followers);
        $sql    = 'SELECT COUNT(*) AS total '
                  . ' FROM ( '
                  . ' SELECT com_id , COUNT( * ) AS comments FROM '
                  . $GLOBALS['xoopsDB']->prefix('smallworld_comments')
                  . " WHERE uid_fk IN ($ids) GROUP BY com_id "
                  . ' UNION ALL '
                  . ' SELECT msg_id , COUNT( * ) AS messages FROM '
                  . $GLOBALS['xoopsDB']->prefix('smallworld_messages')
                  . " WHERE uid_fk IN ($ids) GROUP BY msg_id "
                  . ' ) as d';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $total = $r['total'];
        }
    }

    return (int)$total;
}

/**
 * Function to get sum of users in you following array
 *
 * Used to calculate new message flash in jQuery intval fetch
 *
 *@ deprecated - not used
 * @param $id
 * @return mixed
 */
function smallworld_countUsersMessages($id)
{
    $id     = int($id);
    $total  = 0;
    if (0 < $id ) {
        $sql    = 'SELECT COUNT(*) AS total '
                . ' FROM ( ' . ' SELECT com_id , COUNT( * ) AS comments FROM '
                . $GLOBALS['xoopsDB']->prefix('smallworld_comments')
                . ' WHERE uid_fk = ' . (int)$id
                . ' GROUP BY com_id ' . ' UNION ALL '
                . ' SELECT msg_id , COUNT( * ) AS messages FROM '
                . $GLOBALS['xoopsDB']->prefix('smallworld_messages')
                . ' WHERE uid_fk = ' . (int)$id . 'group BY msg_id ' . ' ) AS d';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $total = $r['total'];
        }
    }

    return $total;
}

/**
 * Get the three newest members to array
 *
 * @deprecated - no longer used
 * @return array
 */
function smallworld_Stats_newest()
{
    $depMsg = __FUNCTION__ . " is deprecated";
    if (isset($GLOBALS['xoopsLogger'])) {
        $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
    } else {
        trigger_error($depMsg, E_USER_WARNING);
    }
    $swUserHandler = Helper::getInstance()->getHandler('SwUser');
    $nu     = [];
    $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' ORDER BY regdate DESC LIMIT 3';
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    if ($GLOBALS['xoopsDB']->getRowsNum($result) > 0) {
        $i = 0;
        while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $nu[$i]['userid']         = $r['userid'];
            $nu[$i]['username']       = $r['username'];
            $nu[$i]['regdate']        = date('d-m-Y', $r['regdate']);
            $nu[$i]['username_link']  = "<a href = '" . $helper->url('userprofile.php?username=' . $r['username']) . "'>";
            $nu[$i]['username_link']  .= $r['username'] . ' (' . $r['realname'] . ') [' . $nu[$i]['regdate'] . '] </a>';
            $nu[$i]['userimage']      = $r['userimage'];
            $nu[$i]['userimage_link'] = $swUserHandler->getAvatarLink($r['userid'], $swUserHandler->gravatar($r['userid']));
            ++$i;
        }
    }

    return $nu;
}

//Avatar Image
/**
 * @deprecated - use Smallworld\SwUserHandler::gravatar()
 * @param $uid
 * @return string
 */
function smallworld_Gravatar($uid)
{
    $depMsg = __FUNCTION__ . " is deprecated use SwUserHandler::gravatar() instead.";
    if (isset($GLOBALS['xoopsLogger'])) {
        $GLOBALS['xoopsLogger']->addDeprecated($depMsg);
    } else {
        trigger_error($depMsg, E_USER_WARNING);
    }
    $image  = '';
    $sql    = 'SELECT userimage FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . (int)$uid . "'";
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $image = $r['userimage'];
    }

    $image = ('' == $image || 'blank.gif' === $image) ? smallworld_getAvatarLink($uid, $image) : $image;

    $type = [
        1 => 'jpg',
        2 => 'jpeg',
        3 => 'png',
        4 => 'gif',
    ];

    $ext = explode('.', $image);

    $avatar = '';
    if (array_key_exists(1, $ext) && in_array(mb_strtolower($ext[1]), $type)) {
        $avatar = '';
    }

    return $avatar;
}

// find user with most posted messages
/**
 * @return array
 */
function smallworld_mostactiveusers_allround()
{
    $msg     = [];
    $sql     = 'SELECT uid_fk, COUNT( * ) as cnt ';
    $sql     .= 'FROM ( ';
    $sql     .= 'SELECT uid_fk ';
    $sql     .= 'FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' ';
    $sql     .= 'UNION ALL SELECT uid_fk ';
    $sql     .= 'FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . ' ';
    $sql     .= ') AS u ';
    $sql     .= 'GROUP BY uid_fk ';
    $sql     .= 'ORDER BY count( * ) DESC limit 3';
    $result  = $GLOBALS['xoopsDB']->queryF($sql);
    $counter = $GLOBALS['xoopsDB']->getRowsNum($result);
    if ($counter < 1) {
    } else {
        $helper        = Helper::getInstance();
        $swUserHandler = $helper->getHandler('SwUser');
        $counter = 1;
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $msg[$counter]['counter']       = $counter;
            $msg[$counter]['img']           = $swUserHandler->getAvatarLink($row['uid_fk'], $swUserHandler->gravatar($row['uid_fk']));
            $msg[$counter]['msgs']          = _SMALLWORLD_TOTALPOSTS . ' : ' . $row['cnt'];
            $msg[$counter]['cnt']           = $row['cnt'];
            $msg[$counter]['username']      = $GLOBALS['xoopsUser']->getUnameFromId($row['uid_fk']);
            $msg[$counter]['username_link'] = "<a href = '" . $helper->url('userprofile.php?username=' . $msg[$counter]['username']) . "'>";
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
function smallworld_worstratedusers()
{
    $swUserHandler = Helper::getInstance()->getHandler('SwUser');
    $array   = [];
    $counter = 1;
    $sql     = 'SELECT owner, (';
    $sql     .= 'sum( up ) - sum( down )';
    $sql     .= ') AS total';
    $sql     .= ' FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . '';
    $sql     .= ' GROUP BY owner ORDER by total ASC LIMIT 5';
    $result  = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $array[$counter]['counter']   = $counter;
        $array[$counter]['img']       = $swUserHandler->getAvatarLink($row['owner'], $swUserHandler->gravatar($row['owner']));
        $array[$counter]['user']      = $GLOBALS['xoopsUser']->getUnameFromId($row['owner']);
        $array[$counter]['rating']    = $row['total'];
        $array[$counter]['user_link'] = "<a href = '" . Helper::getInstance()->url('userprofile.php?username=' . $array[$counter]['user']) . "'>";
        $array[$counter]['user_link'] .= $array[$counter]['user'] . ' (' . $array[$counter]['rating'] . ')</a>';
        ++$counter;
    }

    return $array;
}

// Find best rated users overall
/**
 * @return array
 */
function smallworld_topratedusers()
{
    $swUserHandler = Helper::getInstance()->getHandler('SwUser');
    $array   = [];
    $counter = 1;
    $sql     = 'SELECT owner, (';
    $sql     .= 'sum( up ) - sum( down )';
    $sql     .= ') AS total';
    $sql     .= ' FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . '';
    $sql     .= ' GROUP BY owner ORDER by total DESC LIMIT 5';
    $result  = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $array[$counter]['counter']   = $counter;
        $array[$counter]['img']       = $swUserHandler->getAvatarLink($row['owner'], $swUserHandler->gravatar($row['owner']));
        $array[$counter]['user']      = $GLOBALS['xoopsUser']->getUnameFromId($row['owner']);
        $array[$counter]['rating']    = $row['total'];
        $array[$counter]['user_link'] = "<a href = '" . Helper::getInstance()->url('userprofile.php?username=' . $array[$counter]['user']) . "'>";
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
    $swUserHandler = Helper::getInstance()->getHandler('SwUser');
    $now       = date('d-m');
    $res       = [];
    $sql       = 'SELECT userid, username, userimage, realname, birthday, CURDATE(),'
               . ' DATE_FORMAT(birthday, "%d / %m") AS daymon , '
               . ' (YEAR(CURDATE())-YEAR(birthday))'
               . ' - (RIGHT(CURDATE(),5)<RIGHT(birthday,5))'
               . ' AS age_now'
               . ' FROM '
               . $GLOBALS['xoopsDB']->prefix('smallworld_user')
               . ' WHERE right(birthday,5) = right(CURDATE(),5)'
               . ' ORDER BY MONTH( birthday ) , DAY( birthday ) '
               . ' LIMIT 10 ';
    $result    = $GLOBALS['xoopsDB']->queryF($sql);
    $counter   = $GLOBALS['xoopsDB']->getRowsNum($result);
    $i         = 0;
    while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $res[$i]['amount']        = $counter;
        $res[$i]['userid']        = $r['userid'];
        $res[$i]['userimage']     = $swUserHandler->getAvatarLink($r['userid'], $swUserHandler->gravatar($r['userid']));
        $res[$i]['birthday']      = $r['daymon'];
        $res[$i]['agenow']        = $r['age_now'];
        $res[$i]['username']      = $GLOBALS['xoopsUser']->getUnameFromId($r['userid']);
        $res[$i]['username_link'] = "<a href = '" . Helper::getInstance()->url('userprofile.php?username=' . $res[$i]['username']) . "'>";
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
function smallworld_euroToUsDate($stringDate)
{
    if (0 != $stringDate || '' != $stringDate) {
        $theData = explode('-', trim($stringDate));
        $ret     = $theData[2] . '-' . $theData[1] . '-' . $theData[0];

        return $ret;
    }

    return '1900-01-01';
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
function smallworld_UsToEuroDate($stringDate)
{
    if (0 != $stringDate || '' != $stringDate) {
        $theData = explode('-', trim($stringDate));
        $ret     = $theData[2] . '-' . $theData[1] . '-' . $theData[0];

        return $ret;
    }

    return '01-01-1900';
}

/**
 * @return array
 */
function smallworld_sp()
{
    $sp = [0 => ['spimage' => "<img id='smallworld_img_sp' src='" . Helper::getInstance()->url('assets/images/sp.png') . "' height='30px' width='30px' >"]];
    return $sp;
}

//Check privacy settings in permapage
/**
 * @param $id
 * @return mixed
 */
function smallworldCheckPriv($id)
{
    $public = 'SELECT priv FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . ' WHERE msg_id = ' . $id . '';
    $result = $GLOBALS['xoopsDB']->queryF($public);
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $priv = $row['priv'];
    }

    return $priv;
}

/**
 * Function to calculate remaining seconds until user's birthday
 *
 * Input $d : date('Y-m-d') format
 * return seconds until date at midnight, or
 * return 0 if date ('Y-m-d') is equal to today
 *
 * @param $d
 * @return bool|int|string
 */
function smallworldNextBDaySecs($d)
{
    $olddate   = mb_substr($d, 4);
    $exactdate = date('Y') . '' . $olddate;
    $newdate   = date('Y') . '' . $olddate . ' 00:00:00';
    $nextyear  = date('Y') + 1 . '' . $olddate . ' 00:00:00';
    if ($exactdate != date('Y-m-d')) {
        if ($newdate > date('Y-m-d H:i:s')) {
            $start_ts = strtotime($newdate);
            $end_ts   = strtotime(date('Y-m-d H:i:s'));
            $diff     = $end_ts - $start_ts;
            $n        = round($diff);
            $return   = mb_substr($n, 1);

            return $return;
        }
        $start_ts = strtotime($nextyear);
        $end_ts   = strtotime(date('Y-m-d H:i:s'));
        $diff     = $end_ts - $start_ts;
        $n        = round($diff);
        $return   = mb_substr($n, 1);

        return $return;
    }

    return 0;
}

/**
 * Function to get value from xoopsConfig array
/**
 * @param string $key
 * @param array $array
 * @return int
 */
function smallworldGetValfromArray($key, $array)
{
    $ret = 0;
    $ar  = Helper::getInstance()->getConfig($array);
    if (in_array($key, $ar, true)) {
        $ret = 1;
    }

    return $ret;
}

/**
 * Resize images proportionally
 *
 * Using imagesize($imageurl) returns $img[0], $img[1]
 * Target = new max height or width in px
 *
 * @param int $width
 * @param int $height
 * @param int $target
 * @return string returns the new sizes in html image tag format
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

    return "width='{$width}' height='{$height}'";
}

/**
 * Fetch image width and height
 *
 * will attempt to use the getimagesize method first, then curl
 *
 * @param int $w
 * @param int $h
 * @param string $url
 * @returns array
 * @return array
 */
function smallworld_getImageSize($w, $h, $url)
{
    $bn        = basename($url);
    $w         = (int)$w;
    $h         = (int)$h;
    $imagesize = [0 => '0', 1 => '0']; // init image size

    if ('blank.gif' !== $bn
        && 'blank.png' !== $bn
        && 'blank.jpg' !== $bn
        && 'image_missing.png' !== $bn)
    {
        $imagesize = [0 => $w, 1 => $h];
        if (ini_get('allow_url_fopen')) {
            $imagesize = getimagesize($url);
        }

        if (!ini_get('allow_url_fopen')) {
            $imagesize = [0 => $w, 1 => $h];
            if (function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result       = curl_exec($ch);
                $img          = imagecreatefromstring($result);
                $imagesize[0] = imagesx($img);
                $imagesize[1] = imagesy($img);
            }
        }
    }

    return $imagesize;
}

/**
 * Check whether requests are set or not
 *
 * If not return '' else return false
 *
 * @param string $req
 * @returns string or void
 * @return string|null
 * @return string|null
 */
function smallworld_isset($req)
{
    return (isset($req) && !empty($req)) ? $req : '';
}

/**
 * Do db query for images upload last 5 minutes by spec. userid and return random
 * @param int $userid
 * @return bool|string  false if no image found
 */
function smallworld_getRndImg($userid)
{
    $sql    = 'SELECT imgname FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images')
            . ' WHERE userid = ' . (int)$userid . ' AND time BETWEEN UNIX_TIMESTAMP( ) - 3000 AND UNIX_TIMESTAMP() ORDER BY rand() LIMIT 1';
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $img = $r['imgname'];
    }
    if (!empty($img)) {
        return $img;
    }

    return false;
}

/**
 * Get url of smallworld
 *
 * @returns string
 */
function smallworld_getHostRequest()
{
    $protocol   = false === mb_stripos($_SERVER['SERVER_PROTOCOL'], 'https') ? 'http' : 'https';
    $host       = $_SERVER['HTTP_HOST'];
    $script     = $_SERVER['SCRIPT_NAME'];
    $params     = $_SERVER['QUERY_STRING'];
    $currentUrl = $protocol . '://' . $host;

    return $currentUrl;
}

/**
 * Get htmlentities
 *
 * @param string $text
 * @return string translated string to utf-8
 */
function smallworld_decodeEntities($text)
{
    $text = html_entity_decode($text, ENT_QUOTES, 'ISO-8859-1'); /**{@internal NOTE: UTF-8 does not work!}} */
    $text = preg_replace('/&#(\d+);/me', 'chr(\\1)', $text); #decimal notation
    $text = preg_replace('/&#x([a-f0-9]+);/mei', 'chr(0x\\1)', $text);  #hex notation
    return $text;
}

/**
 * Get string and shorten so contains only # of chars
 *
 * @param string $text
 * @param int $numChars
 * @return string
 */
function smallworld_shortenText($text, $numChars)
{
    $text .= ' ';
    $text = mb_substr($text, 0, $numChars);
    $text = mb_substr($text, 0, mb_strrpos($text, ' '));
    $text .= '...';

    return $text;
}

/**
 * Get language file if constants are not defined
 *
 * @param string $def
 * @param string $file
 * @return void
 */
function smallworld_isDefinedLanguage($def, $file)
{
    /** @var \XoopsModules\Smallworld\Helper $helper */
    $helper = Helper::getInstance();
    if (!defined($def)) {
        // language files (main.php)
        if (file_exists($helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/' . $file))) {
            require_once $helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/' . $file);
        } else {
            // fallback english
            require_once $helper->path('language/english/' . $file);
        }
    }
}

/**
 * @Check if smallworld is for private or public access
 * return value for config
 */
function smallworld_checkPrivateOrPublic()
{
    $opt                   = [];
    $helper                = \XoopsModules\Smallworld\Helper::getInstance();
    $set                   = $helper->getConfig('smallworldprivorpub');
    $opt['access']         = (0 !== $set) ? Constants::HAS_ACCESS : Constants::NO_ACCESS;
    $opt['xoopsuser']      = Constants::IS_NOT_USER;
    $opt['smallworlduser'] = Constants::IS_NOT_USER;

    if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
        $id                    = $GLOBALS['xoopsUser']->uid();
        //$user                  = new \XoopsUser($id);
        //$check                 = new \XoopsModules\Smallworld\User();
        //$profile               = $check->checkIfProfile($id);
        $swUserHandler         = $helper->getHandler('SwUser');
        $profile               = $swUserHandler->checkIfProfile($id);
        $opt['xoopsuser']      = Constants::IS_USER;
        $opt['smallworlduser'] = (Constants::PROFILE_HAS_BOTH !== $profile) ? Constants::IS_NOT_USER : Constants::IS_USER;
    }

    return $opt;
}

/**
 * Get an array of all Users
 *
 * @todo move this to Smallworld\SwUserHandler class
 * @return array 'userid' as keys, 'username' as values
 */
function smallworld_xv_getGroupd()
{
    /** @var \XoopsModules\Smallworld\Helper $helper */
    $helper          = Helper::getInstance();
    $helper->loadLanguage('modinfo');
    $swUserHandler   = $helper->getHandler('SwUser');
    $criteria        = new \Criteria('');
    $criteria->setSort('userid');
    $criteria->order = 'ASC';
    $userObjArray    = $swUserHandler->getAll($criteria, ['userid', 'username'], false);
    $retArray        = [0 => _MI_SMALLWORLD_ALL]; // initialize the array
    foreach ($userObjArray as $swUser) {
        $retArray[$swUser['userid']] = $swUser['username'];
    }

    return $retArray;
    /*
    $db     = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql    = 'SELECT userid, username FROM ' . $db->prefix('smallworld_user') . ' ORDER BY userid';
    $result = $db->queryF($sql);
    $num    = $db->getRowsNum($result);
    if (0 == $num) {
        $ndata = [0 => _MI_SMALLWORLD_ALL];
    } else {
        while (false !== ($r = $db->fetchArray($result))) {
            $data[$r['userid']] = $r['username'];
        }
        $ndata = array_merge([0 => _MI_SMALLWORLD_ALL], $data);
    }

    return $ndata;
    */
}

/**
 * Set javascript vars to theme using various values
 * Return void
 */
function smallworld_SetCoreScript()
{
    /** @var \XoopsModules\Smallworld\Helper $helper */
    $helper = Helper::getInstance();

    // IF logged in define xoops / smallworld user id
    $myId = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? $GLOBALS['xoopsUser']->uid() : Constants::DEFAULT_UID;

    // Check if option is et to allow public reading
    $pub    = smallworld_checkPrivateOrPublic();
    $access = $pub['access'];

    // GET various variables from language folder
    if (file_exists($helper->path('language/' . $GLOBALS['xoopsConfig']['language'] . '/js/variables.js'))) {
        $GLOBALS['xoTheme']->addScript($helper->url('language/' . $GLOBALS['xoopsConfig']['language'] . '/js/variables.js'));
    } else {
        $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/variables.js'));
    }

    // Check if USER is smallworld-registered user
    $chkUser = new Smallworld\User();
    //$profile = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? $chkUser->checkIfProfile($myId) : Constants::DEFAULT_UID;
    $swUserHandler = $helper->getHandler('SwUser');
    $profile       = $swUserHandler->checkIfProfile($myId);

    // Check if there are requests pending
    $count_invit = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? count($chkUser->getRequests($myId)) : Constants::DEFAULT_UID;

    // Get module config for validation and place in javascript
    $validate = $helper->getConfig('validationstrength');

    // Check to see if smallworld should use username links to point to default xoops or smallworld
    $takeoverlinks   = $helper->getConfig('takeoveruserlinks');
    $fieldstoshow    = array_flip($helper->getConfig('smallworldusethesefields'));
    $useverification = $helper->getConfig('smallworldmandatoryfields');
    $smallworldUV    = implode(',', $useverification);

    // Use googlemaps ?
    $googlemaps = $helper->getConfig('smallworldUseGoogleMaps');

    // Get users messages count based on users followerArray
    $getUserMsgNum = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? smallworld_getCountFriendMessagesEtc() : 0;

    // Check if request url is with www or without
    $urltest   = smallworld_getHostRequest();
    $xoops_url = XOOPS_URL;
    if (false === mb_strpos($urltest, 'www.')) {
        $xoops_url = str_replace('www.', '', $xoops_url);
    }

    // Set javascript vars but only if not already defined.
    // Check prevents multiple loads
    $script = 'var Smallworld_myID;' . "\n";
    $script .= "if (typeof Smallworld_myID === 'undefined') {" . "\n";
    $script .= "var smallworld_url = '" . $xoops_url . '/modules/smallworld/' . "';\n";
    $script .= "var smallworld_uploaddir = '" . $xoops_url . '/uploads/avatars/' . "';\n";
    $script .= 'var smallworld_urlReferer = document.referrer;' . "\n";
    $script .= "var xoops_smallworld = jQuery.noConflict();\n";
    $script .= 'var Smallworld_myID = ' . $myId . ";\n";
    $script .= 'var Smallworld_userHasProfile = ' . $profile . ";\n";
    $script .= 'var smallworldTakeOverLinks = ' . $takeoverlinks . ";\n";
    $script .= 'var Smallworld_geocomplete = ' . $googlemaps . ";\n";
    $script .= "var smallworldVerString = '" . $smallworldUV . "';\n";
    $script .= "var smallworlduseverification = new Array();\n";
    $script .= "smallworlduseverification = smallworldVerString.split(',');\n";
    $script .= 'var Smallworld_hasmessages = ' . $count_invit . ";\n";
	//smallworldvalidationstrenght
    $script .= 'var smallworldvalidationstrength = ' . $validate . ";\n";
    $script .= 'var smallworld_getFriendsMsgComCount = ' . $getUserMsgNum . ";\n";
    //$script .= "var $ = jQuery();\n";
    $script .= '}' . "\n";
    $GLOBALS['xoTheme']->addScript('', '', $script);

    // Include geolocate styling
    if (1 == $googlemaps) {
        //$GLOBALS['xoTheme']->addScript('https://maps.googleapis.com/maps/api/js?sensor=false&language=' . _LANGCODE);
        //$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_birth.js'));
        //$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_now.js'));
    }

    smallworld_includeScripts();
}

/**
 * Include script files based on $page
 */
function smallworld_includeScripts()
{
    /** @var \XoopsModules\Smallworld\Helper $helper */
    $helper = Helper::getInstance();
    $page = basename($_SERVER['PHP_SELF'], '.php');
    switch ($page) {
        case 'register':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.validate.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.validation.functions.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.stepy.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.elastic.source.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/esri-leaflet-geocoder.css'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/leaflet.css'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/leaflet.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_birth.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_now.js'));
			
            break;
        case 'publicindex':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.oembed.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.elastic.source.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/wall.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/ajaxupload.3.5.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.avatar_helper.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.min.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.js'));

			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jssocials-theme-minima.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/oembed.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
            break;
        case 'permalink':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.oembed.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/wall.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/oembed.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.innerfade.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.elastic.source.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.min.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.shares.js'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jssocials-theme-minima.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            break;
        case 'index':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.oembed.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.elastic.source.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/wall.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/ajaxupload.3.5.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.avatar_helper.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.min.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.js'));

			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jssocials-theme-minima.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/oembed.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/tag-it.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
            break;
        case 'img_upload':
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/uploader/bootstrap.min.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/uploader/style.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/uploader/bootstrap-responsive.min.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/uploader/bootstrap-image-gallery.min.css'));

            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/vendor/jquery.ui.widget.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/uploader/tmpl.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/uploader/load-image.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/uploader/canvas-to-blob.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/uploader/bootstrap.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/uploader/bootstrap-image-gallery.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.iframe-transport.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.fileupload.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.fileupload-fp.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.fileupload-ui.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/main.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            break;
        case 'galleryshow':
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/galleriffic-5.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.galleriffic.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.history.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.opacityrollover.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/gallery_mod.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.innerfade.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            break;
        case 'friends':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/apprise-1.5.full.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jquery.fileupload-ui.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/oembed.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.oembed.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/wall.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/ajaxupload.3.5.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.avatar_helper.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.innerfade.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            //$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/colorbox.css'));
            break;
        case 'editprofile':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.validate.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.validation.functions.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.stepy.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.elastic.source.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/esri-leaflet-geocoder.css'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/leaflet.css'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/leaflet.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_birth.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_now.js'));
		
            break;
        case 'smallworldshare':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.oembed.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/wall.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.innerfade.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.min.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.js'));

			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jssocials-theme-minima.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/oembed.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            break;
        case 'userprofile':
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/apprise-1.5.full.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jquery.fileupload-ui.css'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/oembed.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.oembed.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/wall.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/ajaxupload.3.5.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.avatar_helper.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.min.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jssocials.js'));

			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/jssocials-theme-minima.css'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.colorbox.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.elastic.source.js'));
            $GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.countdown.js'));
            $GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/smallworld.css'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/esri-leaflet-geocoder.css'));
			$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/leaflet.css'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/leaflet.js'));			
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_birth.js'));
			$GLOBALS['xoTheme']->addScript($helper->url('assets/js/osm_now.js'));			
            break;
    }
}

/**
 * Check if permission is set for userid to post publicly
 *
 * @return array
 */
function smallworld_checkUserPubPostPerm()
{
	$check      = new Smallworld\SmallWorldUser();
    $helper     = Helper::getInstance();
    $userPerPub = $helper->getConfig('smallworldshowPoPubPage');
	$number = number_format($userPerPub[0], 0);  // userPerPub for some reason returned int as string ?!?!
    $pub = ($number > 0) ? $number : $check->allUsers();
    /*
    $check      = new Smallworld\User();
    $userPerPub = smallworld_GetModuleOption('smallworldshowPoPubPage');
    $allUsers   = $check->allUsers();
    if (0 != $userPerPub[0]) {
        $pub = $userPerPub;
    } else {
        $pub = $check->allUsers();
    }
    */
    return $pub;
}

/**
 * Change @username to urls
 *
 * @param string|null $status_text
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
    $id     = [];
    $sql    = 'SELECT userid FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE username = '" . $name . "'";
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $id = $r['userid'];
    }

    return $id;
}

/**
 * Extract users from @tags
 * @param        $txt
 * @param        $sender
 * @param string $permalink
 * @throws \phpmailerException
 */
function smallworld_getTagUsers($txt, $sender, $permalink = '')
{
    $dBase = new Smallworld\SwDatabase();
    $mail  = new Smallworld\Mail();
    preg_match_all('/@([a-zA-Z0-9]+|\\[[a-zA-Z0-9]+\\])/', $txt, $matches);
    $users = array_unique($matches[1]);
    foreach ($users as $user) {
        $uid    = smallworld_getUidFromName($user);
        $notify = json_decode($dBase->getSettings($uid), true);
        if (isset($notify['notify'])) {
            if (0 != $notify['notify']) {
                $mail->sendMails($sender, $uid, 'tag', $permalink, [$txt]);
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
    $db     = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql    = 'SELECT message FROM ' . $db->prefix('smallworld_messages') . " WHERE uid_fk='" . $uid . "'";
    $result = $db->queryF($sql);
    $count  = $db->getRowsNum($result);

    return $count;
}
