<?php

namespace XoopsModules\Smallworld;

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
class Admin
{
    /**
     * Get oldest message in Db
     * @returns time
     */
    public function oldestMsg()
    {
        global $xoopsDB;
        $date    = 0;
        $sql     = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_messages') . ' ORDER BY created LIMIT 1';
        $result  = $xoopsDB->queryF($sql);
        $counter = $xoopsDB->getRowsNum($result);
        if ($counter >= 1) {
            while (false !== ($sqlfetch = $xoopsDB->fetchArray($result))) {
                $date = $sqlfetch['created'];
            }
        }

        return $date;
    }

    /**
     * Get average messages sent per day
     * @param int $totaldays
     * @return int|string
     */
    public function AvgMsgDay($totaldays)
    {
        global $xoopsDB;
        $sql    = 'SELECT count( * ) / ' . $totaldays . ' AS averg FROM ' . $xoopsDB->prefix('smallworld_messages') . '';
        $result = $xoopsDB->queryF($sql);
        while (false !== ($sqlfetch = $xoopsDB->fetchArray($result))) {
            $avg = number_format($sqlfetch['averg'], 2, '.', ',');
        }

        return $avg;
    }

    /**
     * total users using smallworld
     * @return int
     */
    public function TotalUsers()
    {
        global $xoopsDB;
        $sql     = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . '';
        $result  = $xoopsDB->queryF($sql);
        $counter = $xoopsDB->getRowsNum($result);
        if ($counter < 1) {
            $sum = 0;
        } else {
            $i = 0;
            while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
                $user[$i]['username'] = $myrow['username'];
                ++$i;
            }
            $all    = $this->flatten($user);
            $sum    = count(array_unique($all));
            $unique = array_unique($all);
        }

        return $sum;
    }

    /**
     * Get version of module
     * @returns string
     */
    public function ModuleInstallVersion()
    {
        global $xoopsModule;
        $version = round($xoopsModule->getVar('version') / 100, 2);

        return $version;
    }

    /**
     * Get date when Module was installed
     * @return string|time
     */
    public function ModuleInstallDate()
    {
        global $xoopsModule;
        $date = formatTimestamp($xoopsModule->getVar('last_update'), 'm');

        return $date;
    }

    /**
     * Count total days represented in db
     * @return float|int|time
     */
    public function CountDays()
    {
        global $xoopsDB, $xoopsModule;
        $date = $this->oldestMsg();
        $now  = time();
        $diff = ($now - $date) / (60 * 60 * 24);

        return $diff;
    }

    /**
     * find user with most posted messages
     * @returns array
     */
    public function mostactiveusers_allround()
    {
        global $xoopsDB, $xoopsUser;
        $sql     = 'SELECT uid_fk, COUNT( * ) as cnt ';
        $sql     .= 'FROM ( ';
        $sql     .= 'SELECT uid_fk ';
        $sql     .= 'FROM ' . $xoopsDB->prefix('smallworld_messages') . ' ';
        $sql     .= 'UNION ALL SELECT uid_fk ';
        $sql     .= 'FROM ' . $xoopsDB->prefix('smallworld_comments') . ' ';
        $sql     .= ') AS u ';
        $sql     .= 'GROUP BY uid_fk ';
        $sql     .= 'ORDER BY count( * ) DESC limit 20';
        $result  = $xoopsDB->queryF($sql);
        $counter = $xoopsDB->getRowsNum($result);

        if ($counter < 1) {
            $msg = [];
        } else {
            $msg = [];
            $i   = 1;
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $msg['counter'][$i] = $i;
                $msg['img'][$i]     = "<img style='margin:0px 5px;' src = '../assets/images/" . $i . ".png'>";
                if ($msg['counter'][$i] > 3) {
                    $msg['img'][$i] = '';
                }
                $msg['cnt'][$i]  = $row['cnt'];
                $msg['from'][$i] = $xoopsUser->getUnameFromId($row['uid_fk']);
                ++$i;
            }
        }

        return $msg;
    }

    /**
     * find user with most posted messages in last 24 hours
     * @returns array
     */
    public function mostactiveusers_today()
    {
        global $xoopsDB, $xoopsUser;
        $sql = 'SELECT uid_fk, COUNT( * ) as cnt ';
        $sql .= 'FROM ( ';
        $sql .= 'SELECT uid_fk ';
        $sql .= 'FROM ' . $xoopsDB->prefix('smallworld_messages') . ' ';
        $sql .= 'WHERE `created` > UNIX_TIMESTAMP(DATE_SUB( NOW( ) , INTERVAL 1 DAY )) ';
        $sql .= 'UNION ALL SELECT uid_fk ';
        $sql .= 'FROM ' . $xoopsDB->prefix('smallworld_comments') . ' ';
        $sql .= 'WHERE `created` > UNIX_TIMESTAMP(DATE_SUB( NOW( ) , INTERVAL 1 DAY )) ';
        $sql .= ') AS u ';
        $sql .= 'GROUP BY uid_fk ';
        $sql .= 'ORDER BY count( * ) DESC limit 20';

        $result   = $xoopsDB->queryF($sql);
        $msgtoday = [];

        if (0 != $xoopsDB->getRowsNum($result)) {
            $i = 1;
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $msgtoday['counter'][$i] = $i;
                $msgtoday['img'][$i]     = "<img style='margin:0px 5px;' src = '../assets/images/" . $i . ".png'>";
                if ($msgtoday['counter'][$i] > 3) {
                    $msgtoday['img'][$i] = '';
                }
                $msgtoday['cnt'][$i]  = $row['cnt'];
                $msgtoday['from'][$i] = $xoopsUser->getUnameFromId($row['uid_fk']);
                ++$i;
            }
        } else {
            $msgtoday = [];
        }

        return $msgtoday;
    }

    /**
     * Find best OR worst rated users
     * @param string $direction
     * @returns array
     * @return array
     * @return array
     */
    public function topratedusers($direction)
    {
        global $xoopsUser, $xoopsDB, $xoopsTpl;
        $array = [];

        if ('up' === $direction) {
            $sql    = 'SELECT owner, count(*) AS cnt FROM ' . $xoopsDB->prefix('smallworld_vote') . " WHERE up='1' GROUP BY owner ORDER BY cnt DESC LIMIT 20";
            $result = $xoopsDB->queryF($sql);
            $count  = $xoopsDB->getRowsNum($result);
            $i      = 1;
            if ($count >= $i) {
                while (false !== ($row = $xoopsDB->fetchArray($result))) {
                    $array['counter'][$i] = $i;
                    $array['img'][$i]     = "<img height='10px' width='10px' " . "style='margin:0px 5px;' src = '../assets/images/like.png'>";
                    if ($array['counter'][$i] > 3) {
                        $array['img'][$i] = '';
                    }
                    $array['cnt'][$i]  = $row['cnt'];
                    $array['user'][$i] = $xoopsUser->getUnameFromId($row['owner']);
                    ++$i;
                }
            } else {
                $array = [];
            }
        } else {
            $sql    = 'SELECT owner, count(*) AS cnt FROM ' . $xoopsDB->prefix('smallworld_vote') . " WHERE down='1' GROUP BY owner ORDER BY cnt DESC LIMIT 20";
            $result = $xoopsDB->queryF($sql);
            $count  = $xoopsDB->getRowsNum($result);
            $i      = 1;
            if (0 != $count) {
                while (false !== ($row = $xoopsDB->fetchArray($result))) {
                    $array['counter'][$i] = $i;
                    $array['img'][$i]     = "<img height='10px' width='10px' " . "style='margin:0px 5px;' src = '../assets/images/dislike.png'>";
                    if ($array['counter'][$i] > 3) {
                        $array['img'][$i] = '';
                    }
                    $array['cnt'][$i]  = $row['cnt'];
                    $array['user'][$i] = $xoopsUser->getUnameFromId($row['owner']);
                    ++$i;
                }
            } else {
                $array = [];
            }
        }

        return $array;
    }

    /**
     * Get all users to loop in admin for administration
     * @param string $inspect
     * @returns array
     * @return array
     * @return array
     */
    public function getAllUsers($inspect)
    {
        global $xoopsDB, $xoopsUser, $xoopsTpl;
        $data = [];
        if ('yes' === $inspect) {
            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_admin') . ' WHERE (inspect_start  + inspect_stop) >= ' . time() . ' ORDER BY username';
        } else {
            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_admin') . ' WHERE (inspect_start  + inspect_stop) < ' . time() . ' ORDER BY username';
        }
        $result = $xoopsDB->queryF($sql);
        $count  = $xoopsDB->getRowsNum($result);
        if (0 != $count) {
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $data[] = $row;
            }
        }
        if (!empty($data)) {
            return $data;
        }
    }

    /**
     * check server if update is available
     * Server currently at culex.dk
     * Variable $version = current smallworld version number
     * @return string
     */
    public function doCheckUpdate()
    {
        global $pathIcon16;
        $version  = $this->ModuleInstallVersion();
        $critical = false;
        $update   = false;
        $rt       = '';
        $url      = 'http://www.culex.dk/updates/smallworld_version.csv';
        $fileC    = $this->fetchURL($url, ['fopen', 'curl', 'socket']);
        $read     = explode(';', $fileC);

        $upd_img = $pathIcon16 . '/on.png';

        if ($read[0] > $version && '1' == $read[2]) {
            $critical = true;
            $upd_img  = $pathIcon16 . '/off.png';
        }
        if ($read[0] > $version && '1' != $read[2]) {
            $update  = true;
            $upd_img = '../assets/images/upd_normal.png';
        }
        if ($critical) {
            $rt = "<div class='smallworld_update'><img src='" . $upd_img . "'>";
            $rt .= _AM_SMALLWORLD_UPDATE_CRITICAL_UPD . '</div>';
            $rt .= "<textarea class='xim_update_changelog'>" . $read[1] . '</textarea><br><br>';
            $rt .= _AM_SMALLWORLD_UPDATE_SERVER_FILE . "<br><a href='" . $read[3] . "'>" . $read[3] . '</a>';
        } elseif ($update) {
            $rt = "<div class='smallworld_update'><img src='" . $upd_img . "'>";
            $rt .= _AM_SMALLWORLD_UPDATE_NORMAL_UPD . '</div>';
            $rt .= "<textarea class='smallworld_update_changelog'>" . $read[1] . '</textarea><br><br>';
            $rt .= _AM_SMALLWORLD_UPDATE_SERVER_FILE . "<br><a href='" . $read[3] . "'>" . $read[3] . '</a>';
        } else {
            $rt = "<div class='smallworld_update'><br><img src='" . $upd_img . "'>" . _AM_SMALLWORLD_UPDATE_YOUHAVENEWESTVERSION . '</div>';
        }

        return $rt;
    }

    /**
     * Fetch content of comma separated text file
     * will attempt to use the fopen method first, then curl, then socket
     * @param string $url
     * @param array  $methods
     * @returns string
     * @return bool|false|string
     * @return bool|false|string
     */
    public function fetchURL($url, $methods = ['fopen', 'curl', 'socket'])
    {
        /**
         *   December 21st 2010, Mathew Tinsley (tinsley@tinsology.net)
         *   http://tinsology.net
         *
         *   To the extent possible under law, Mathew Tinsley has waived all copyright and related or
         *   neighboring rights to this work. There's absolutely no warranty.
         */
        if ('string' === gettype($methods)) {
            $methods = [$methods];
        } elseif (!is_array($methods)) {
            return false;
        }
        foreach ($methods as $method) {
            switch ($method) {
                case 'fopen':
                    //uses file_get_contents in place of fopen
                    //allow_url_fopen must still be enabled
                    if (ini_get('allow_url_fopen')) {
                        $contents = file_get_contents($url);
                        if (false !== $contents) {
                            return $contents;
                        }
                    }
                    break;
                case 'curl':
                    if (function_exists('curl_init')) {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        // return the value instead of printing the response to browser
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $result = curl_exec($ch);
                        curl_close($ch);
                        //return curl_exec($ch);
                        return $result;
                    }
                    break;
                case 'socket':
                    //make sure the url contains a protocol, otherwise $parts['host'] won't be set
                    if (0 !== mb_strpos($url, 'http://') && 0 !== mb_strpos($url, 'https://')) {
                        $url = 'http://' . $url;
                    }
                    $parts = parse_url($url);
                    if ('https' === $parts['scheme']) {
                        $target = 'ssl://' . $parts['host'];
                        $port   = isset($parts['port']) ? $parts['port'] : 443;
                    } else {
                        $target = $parts['host'];
                        $port   = isset($parts['port']) ? $parts['port'] : 80;
                    }
                    $page = isset($parts['path']) ? $parts['path'] : '';
                    $page .= isset($parts['query']) ? '?' . $parts['query'] : '';
                    $page .= isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
                    $page = ('' == $page) ? '/' : $page;
                    $fp   = fsockopen($target, $port, $errno, $errstr, 15);
                    if ($fp) {
                        $headers = "GET $page HTTP/1.1\r\n";
                        $headers .= "Host: {$parts['host']}\r\n";
                        $headers .= "Connection: Close\r\n\r\n";
                        if (fwrite($fp, $headers)) {
                            $resp = '';
                            //while not eof and an error does not occur when calling fgets
                            while (!feof($fp) && false !== ($curr = fgets($fp, 128))) {
                                $resp .= $curr;
                            }
                            if (isset($curr) && false !== $curr) {
                                return mb_substr(mb_strstr($resp, "\r\n\r\n"), 3);
                            }
                        }
                        fclose($fp);
                    }
                    break;
            }
        }

        return false;
    }

    /**
     * Smallworld_sanitize(array(array) )
     * flatten multidimentional arrays to one dimentional
     * @param array $array
     * @return array
     */
    public function flatten($array)
    {
        $return = [];
        while (count($array)) {
            $value = array_shift($array);
            if (is_array($value)) {
                foreach ($value as $sub) {
                    $array[] = $sub;
                }
            } else {
                $return[] = $value;
            }
        }

        return $return;
    }

    /**
     * Smallworld_sanitize($string)
     * @param string $text
     * @returns string
     * @return string|string[]
     * @return string|string[]
     */
    public function Smallworld_sanitize($text)
    {
        $text = htmlspecialchars($text, ENT_QUOTES);
        $myts = \MyTextSanitizer::getInstance();
        $text = $myts->displayTarea($text, 1, 1, 1, 1);
        $text = str_replace("\n\r", "\n", $text);
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\n", '<br>', $text);
        $text = str_replace('"', "'", $text);

        return $text;
    }
}
