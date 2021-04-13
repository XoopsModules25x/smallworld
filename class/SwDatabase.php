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

use Xmf\Request;
use XoopsModules\Smallworld\Constants;

/**
 * SmallWorld
 *
 * @package      \XoopsModules\SmallWorld
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

/**
 *
 * SwDatabase to manage SW activity
 *
 */
class SwDatabase
{
    /**
     * getJobsToDiv method
     *
     * @todo switch to use SwUser class methods
     * @param int $id
     * @return array
     */
    public function getJobsToDiv($id)
    {
        $msg    = [];
        $sql    = 'SELECT employer,position,jobstart,jobstop,description  FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid ='" . $id . "'";
        $result = $GLOBALS['xoopsDB']->query($sql);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $employer    = unserialize($row['employer']);
            $position    = unserialize($row['position']);
            $jobstart    = unserialize($row['jobstart']);
            $jobstop     = unserialize($row['jobstop']);
            $description = unserialize($row['description']);
        }
        $start = 0;
        $end   = count($employer) - 1;
        while ($start <= $end) {
            $msg[$start]['employer']    = $employer[$start];
            $msg[$start]['position']    = $position[$start];
            $msg[$start]['jobstart']    = $jobstart[$start];
            $msg[$start]['jobstop']     = $jobstop[$start];
            $msg[$start]['description'] = $description[$start];
            ++$start;
        }

        return $msg;
    }

    /**
     * getSchoolToDiv function
     *
     * @param int $userId smallworld `userid`
     * @return array
     */
    function getSchoolToDiv($id) 
    {
        global $arr7;
        $msg=array();
        $sql = "SELECT school_type,school,schoolstart,schoolstop FROM "
            . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid ='" . $id . "'";
        $result = $GLOBALS['xoopsDB']->query($sql);
        while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) { 
            $school_type = unserialize($row['school_type']);
            $school    = unserialize($row['school']);
            $schoolstart = unserialize($row['schoolstart']);
            $schoolstop = unserialize($row['schoolstop']);
        }
        $start = 0;
        $end = count($school_type) - 1;
        while ($start<=$end) {
            $msg[$start]['school_type'] = $school_type[$start];
            $msg[$start]['school'] = $arr7[$school[$start]];
            $msg[$start]['schoolstart'] = $schoolstart[$start];
            $msg[$start]['schoolstop'] = $schoolstop[$start];
            $start++;
        }
        return $msg;
    }

    /**
     * getScreennamesToDiv function
     *
     * @param int $userId smallworld `userid`
     * @return array
     */
    public function getScreennamesToDiv($userId)
    {
        global $arr06;
        $msg             = [];
        $screenname_type = [];
        $swUser = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser')->getByUserId($userId);
        if ($swUser instanceof \XoopsModules\Smallworld\SwUser) {
            $screenname_type = $swUser->getVar('screenname_type');
            $screenname      = $swUser->getVar('screenname');
        }
        /*
        $sql    = 'SELECT screenname_type,screenname FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid ='" . $userId . "'";
        $result = $GLOBALS['xoopsDB']->query($sql);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $screenname_type = unserialize($row['screenname_type']);
            $screenname      = unserialize($row['screenname']);
        }
        */
        $start = 0;
        $end   = count($screenname_type);
        while ($start < $end) {
            $msg[$start]['screenname']      = $screenname_type[$start];
            $msg[$start]['screenname_type'] = $arr06[$screenname[$start]];
            $msg[$start]['link']            = "<span class='smallworld_website'>" . smallworld_sociallinks($screenname[$start], $msg[$start]['screenname']);
            ++$start;
        }

        return $msg;
    }


    /**
     * getVar function   
     * @param int $id
     * @param string $var
     * @returns Array
     */   
    function getVar($id, $var)
    {
        global $xoopsUser, $xoopsDB;
        $sql = "SELECT ".$var." FROM ".$xoopsDB->prefix('smallworld_user')." WHERE userid = '".$id."'";
        $result = $xoopsDB->queryF($sql);
        if ($xoopsDB->getRowsNum($result) < 1) {
            return 0;//_SMALLWORLD_REPLY_NOTSPECIFIED;
        }
            while ($row = $xoopsDB->fetchArray($result)) { 
                $msg[$var] = $row[$var];
            }
            return $msg[$var];
    }
    

    /**
     * updateSingleValue function
     * @param string $table
     * @param int    $userid
     * @param string $field
     * @param int    $value
     */
    public function updateSingleValue($table, $userid, $field, $value)
    {
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix($table) . ' SET ' . $field . "='" . $myts->addSlashes($value) . "' WHERE userid='" . (int)$userid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $result;
    }

    /**
     * saveImage function
     * @param $values
     */
    public function saveImage($values)
    {
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . ' VALUES (' . $values . ')';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        return $result;
    }

    /**
     * DeleteImage function
     * @param int    $userid
     * @param string $imagename
     */
    public function deleteImage($userid, $imagename)
    {
        $myts   = \MyTextSanitizer::getInstance();
        $sql    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " WHERE imgname = '" . stripslashes($imagename) . "' AND userid='" . (int)$userid . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $result;
    }

    /**
     * handlePosts function
     */
    public function handlePosts()
    {
		$GLOBALS['xoopsLogger']->activated = false;
		if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser'] instanceof \XoopsUser) {
            $uid = $GLOBALS['xoopsUser']->uid();
        } else {
            return false;
        }
        $uid     = ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->uid() : 0;

        $img     = new Images;
        $avatar  = $this->getVar($uid, 'userimage');
        $partner = '';

        if (empty($avatar)) {
            $avatar = $GLOBALS['xoopsUser']->user_avatar();
        }
        if (Constants::RELATIONSHIP_SINGLE !== Request::getInt('relationship', Constants::RELATIONSHIP_COMPLICATED, 'POST')) {
            $partner = smallworld_sanitize($_POST['partner']);
        }

        $regdate                = time();
        $username               = $GLOBALS['xoopsUser']->uname();
        $realname               = smallworld_sanitize($_POST['realname']);
        $gender                 = Request::getInt('gender', Constants::GENDER_UNKNOWN, 'POST');
        $intingender            = isset($_POST['intingender']) ? smallworld_sanitize(serialize($_POST['intingender'])) : smallworld_sanitize(serialize([0 => '3']));
        $relationship           = smallworld_sanitize($_POST['relationship']);
        $searchrelat            = isset($_POST['searchrelat']) ? smallworld_sanitize(serialize($_POST['searchrelat'])) : smallworld_sanitize(serialize([0 => '0']));
        $birthday               = smallworld_sanitize(smallworld_euroToUsDate($_POST['birthday']));
        $birthplace             = smallworld_sanitize($_POST['birthplace']);
        $birthplace_lat         = smallworld_sanitize($_POST['birthplace_lat']);
        $birthplace_lng         = smallworld_sanitize($_POST['birthplace_lng']);
        $birthplace_country     = smallworld_sanitize($_POST['birthplace_country']);
        $birthplace_country_img = isset($_POST['birthplace_country_img']) ? smallworld_sanitize($_POST['birthplace_country_img']) : '';
        $politic                = smallworld_sanitize($_POST['politic']);
        $religion               = smallworld_sanitize($_POST['religion']);
        $emailtype              = smallworld_sanitize(serialize($_POST['emailtype']));
        $screenname_type        = smallworld_sanitize(serialize($_POST['screenname_type']));
        $screenname             = smallworld_sanitize(serialize($_POST['screenname']));
        $mobile                 = smallworld_sanitize($_POST['mobile']);
        $phone                  = smallworld_sanitize($_POST['phone']);
        $adress                 = smallworld_sanitize($_POST['adress']);
        $present_city           = smallworld_sanitize($_POST['present_city']);
        $present_lat            = smallworld_sanitize($_POST['present_lat']);
        $present_lng            = smallworld_sanitize($_POST['present_lng']);
        $present_country        = smallworld_sanitize($_POST['present_country']);
        $present_country_img    = isset($_POST['present_country_img']) ? smallworld_sanitize($_POST['present_country_img']) : '';
        $website                = smallworld_sanitize($_POST['website']);
        $interests              = smallworld_sanitize($_POST['interests']);
        $music                  = smallworld_sanitize($_POST['music']);
        $tvshow                 = smallworld_sanitize($_POST['tvshow']);
        $movie                  = smallworld_sanitize($_POST['movie']);
        $books                  = smallworld_sanitize($_POST['books']);
        $aboutme                = smallworld_sanitize($_POST['aboutme']);
        $school_type            = smallworld_sanitize(serialize($_POST['school_type']));
        $school                 = smallworld_sanitize(serialize($_POST['school']));
        $schoolstart            = smallworld_sanitize(serialize($_POST['schoolstart']));
        $schoolstop             = smallworld_sanitize(serialize($_POST['schoolstop']));
        $jobemployer            = smallworld_sanitize(serialize($_POST['employer']));
        $jobposition            = smallworld_sanitize(serialize($_POST['position']));
        $jobstart               = smallworld_sanitize(serialize(smallworld_YearOfArray($_POST['jobstart'])));
        $jobstop                = smallworld_sanitize(serialize(smallworld_YearOfArray($_POST['jobstop'])));
        $jobdescription         = smallworld_sanitize(serialize($_POST['description']));

        $swUserHandler = \XoopsModules\Smallworld\Helper::getInstance()->getHandler('SwUser');

        //@todo find better way to terminate routine than just 'die' on error(s)
        if ('edit' === $_POST['function']) {
            /*
			$swUserObj = $swUserHandler->get($uid);
            if (!$swUserObj instanceof \XoopsModules\Smallworld\SwUser) {
                return;
            }
            $swUserObj->setVars([
                'realname'           => $realname,
                'username'           => $username,
                'userimage'          => $avatar,
                'gender'             => $gender,
                'intingender'        => $intingender,
                'relationship'       => $relationship,
                'partner'            => $partner,
                'searchrelat'        => $searchrelat,
                'birthday'           => $birthday,
                'birthplace'         => $birthplace,
                'birthplace_lat'     => (float)$birthplace_lat,
                'birthplace_lng'     => (float)$birthplace_lng,
                'birthplace_country' => $birthplace_country,
                'politic'            => $politic,
                'religion'           => $religion,
                'emailtype'          => $emailtype,
                'screenname_type'    => $screenname_type,
                'screenname'         => $screenname,
                'mobile'             => (float)$mobile,
                'phone'              => (float)$phone,
                'adress'             => $adress,
                'present_city'       =>  $present_city,
                'present_lat'        => (float)$present_lat,
                'present_lng'        => (float)$present_lng,
                'present_country'    => $present_country,
                'website'            => $website,
                'interests'          => $interests,
                'music'              => $music,
                'tvshow'             => $tvshow,
                'movie'              => $movie,
                'books'              => $books,
                'aboutme'            => $aboutme,
                'school_type'        => $school_type,
                'school'             => $school,
                'schoolstart'        => $schoolstart,
                'schoolstop'         => $schoolstop,
                'employer'           => $jobemployer,
                'position'           => $jobposition,
                'jobstart'           => $jobstart,
                'jobstop'            => $jobstop,
                'description'        => $jobdescription
            ]);
            $result = $swUserHandler->insert($swUserObj);
            if (false === $result) {
                die('Failed inserting User');
            }
            */
			
            // Update all values in user_table
            $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' SET ';
            $sql    .= "realname = '" . $realname . "', username= '" . $username . "', userimage = '" . $avatar . "', gender = '" . $gender . "',";
            $sql    .= "intingender = '" . $intingender . "',relationship = '" . $relationship . "', partner = '" . $partner . "', searchrelat = '" . $searchrelat . "',";
            $sql    .= "birthday = '" . $birthday . "',birthplace = '" . $birthplace . "',birthplace_lat = '" . (float)$birthplace_lat . "',";
            $sql    .= "birthplace_lng = '" . (float)$birthplace_lng . "',birthplace_country = '" . $birthplace_country . "',politic = '" . $politic . "',";
            $sql    .= "religion = '" . $religion . "',emailtype = '" . $emailtype . "',screenname_type = '" . $screenname_type . "',";
            $sql    .= "screenname = '" . $screenname . "',mobile = '" . (float)$mobile . "',phone = '" . (float)$phone . "',adress = '" . $adress . "',";
            $sql    .= "present_city = '" . $present_city . "',present_lat = '" . (float)$present_lat . "',present_lng = '" . (float)$present_lng . "',";
            $sql    .= "present_country = '" . $present_country . "',website = '" . $website . "',interests = '" . $interests . "',";
            $sql    .= "music = '" . $music . "',tvshow = '" . $tvshow . "',movie = '" . $movie . "',";
            $sql    .= "books = '" . $books . "',aboutme = '" . $aboutme . "',school_type = '" . $school_type . "',";
            $sql    .= "school = '" . $school . "', schoolstart = '" . $schoolstart . "',schoolstop = '" . $schoolstop . "',";
            $sql    .= "employer = '" . $jobemployer . "', position = '" . $jobposition . "',jobstart = '" . $jobstart . "',";
            $sql    .= "jobstop = '" . $jobstop . "', description = '" . $jobdescription . "' ";
            $sql    .= "WHERE userid ='" . (int)$uid . "'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            if (false === $result) {
                die('SQL error:' . $sql . '');
            }
            
            $this->EditAdmins($uid, $realname, $avatar);
            $img->createAlbum($uid);
        }

        if ('save' === $_POST['function']) {
            $sql    = 'INSERT INTO '
                      . $GLOBALS['xoopsDB']->prefix('smallworld_user')
                      . ' (userid, regdate, username, userimage, realname, gender, intingender, relationship, partner, searchrelat, birthday, birthplace, birthplace_lat, birthplace_lng, birthplace_country, politic, religion, emailtype, screenname_type, screenname, mobile, phone, adress, present_city, present_lat, present_lng, present_country, website, interests, music, tvshow, movie, books, aboutme, school_type, school, schoolstart, schoolstop, employer, position, jobstart, jobstop, description, friends, followers, admin_flag) ';
            $sql    .= "VALUES ('" . (int)$uid . "', '" . $regdate . "', '" . $username . "', '" . $avatar . "', '" . $realname . "', '" . $gender . "', '" . $intingender . "', '" . $relationship . "', '" . $partner . "', '" . $searchrelat . "','";
            $sql    .= $birthday . "', '" . $birthplace . "', '" . (float)$birthplace_lat . "', '" . (float)$birthplace_lng . "', '" . $birthplace_country . "', '" . $politic . "', '" . $religion . "','";
            $sql    .= $emailtype . "', '" . $screenname_type . "', '" . $screenname . "', '" . (float)$mobile . "', '" . (float)$phone . "', '" . $adress . "', '" . $present_city . "', '" . (float)$present_lat . "','";
            $sql    .= (float)$present_lng . "', '" . $present_country . "', '" . $website . "', '" . $interests . "', '" . $music . "', '" . $tvshow . "', '" . $movie . "', '" . $books . "', '" . $aboutme . "', '";
            $sql    .= $school_type . "', '" . $school . "', '" . $schoolstart . "', '" . $schoolstop . "', '" . $jobemployer . "', '" . $jobposition . "', '" . $jobstart . "', '" . $jobstop . "', '" . $jobdescription . "', ";
            $sql    .= "'0', '0', '0')";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            if (false === $result) {
                die('SQL error:' . $sql . '');
            }
            $this->setAdmins($uid, $username, $realname, $avatar);
            $img->createAlbum($uid);
        }
    }
    /**
     * SetAdmins function
     *
     * @param int    $userID
     * @param string $username
     * @param string $realname
     * @param mixed  $avatar
     */
    public function setAdmins($userID, $username, $realname, $avatar)
    {
        $ip     = $_SERVER['REMOTE_ADDR'];
        $sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . ' (userid,username, realname,userimage,ip,complaint,inspect_start, ' . "inspect_stop) VALUES ('" . (int)$userID . "', '" . $username . "','" . $realname . "', '" . $avatar . "','" . $ip . "','0','0','0')";
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $result;
    }

    /**
     * EditAdmins function
     *
     * @param int    $userID
     * @param string $realname
     * @param mixed  $avatar
     */
    public function EditAdmins($userID, $realname, $avatar)
    {
        // @todo need to sanitize realname and avatar
        $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET realname = '" . $realname . "', userimage = '" . $avatar . "' WHERE userid = '" . (int)$userID . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $result;
    }

    /**
     * alreadycomplaint function
     *
     * Check if user has already sent complaint
     *
     * @param string $msg
     * @param int    $by
     * @param int    $against
     * @return int
     */
    public function alreadycomplaint($msg, $by, $against)
    {
        $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_complaints') . " WHERE byuser_id = '" . (int)$by . "' AND owner = '" . (int)$against . "' AND link = '" . addslashes($msg) . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        if (1 > $i) {
            $query  = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_complaints') . " (complaint_id,link,byuser_id,owner) VALUES ('', '" . addslashes($msg) . "', '" . (int)$by . "', '" . (int)$against . "')";
            $result = $GLOBALS['xoopsDB']->queryF($query);
        }

        return $i;
    }

    /**
     * updateComplaint function
     *
     * @param int $userID
     * @return bool true on successful update
     */
    public function updateComplaint($userID)
    {
        $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . ' SET complaint = complaint + 1 ' . "WHERE userid = '" . (int)$userID . "'";
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $result ? true : false;
    }

    /**
     * updateInspection function
     * @param int   $userID
     * @param int   $start
     * @param bool
     */
    public function updateInspection($userID, $start, $stop)
    {
        $time    = time();
        $newstop = $time + $stop;
        $sql     = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET inspect_start = '" . $time . "', instect_stop = '" . $newstop . "' WHERE userid ='" . (int)$userID . "'";
        $result  = $GLOBALS['xoopsDB']->queryF($sql);

        return $result ? true : false;
    }

    /**
     * handleImageEdit function
     *
     * @return bool true on success, false on failure
     */
    public function handleImageEdit()
    {
        //@todo need to filter $_POST['imgdesc'] array
        $return = true;
        $postCount = count($_POST['id']);
        for ($i = 0, $iMax = $postCount; $i < $iMax; ++$i) {
            $id     = (int)$_POST['id'][$i];
            $desc   = $_POST['imgdesc'][$i];
            $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " SET `desc` = '" . addslashes($desc) . "' WHERE `id`='" . $id . "'";
            $result = $return && $GLOBALS['xoopsDB']->queryF($sql);
        }
        return $result ? true : false;
    }

    /**
     * updateInspection function
     *
     * insert application for friendship into db or delete if denied
     *
     * @param int $status
     * @param int $friendid
     * @param int $userid
     * @return bool
     */
    public function toogleFriendInvite($status, $friendid, $userid)
    {
        $result = true;
        if (0 == $status) {
            $sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " (me,you,status,date) VALUES ('" . $userid . "', '" . $friendid . "', '1', UNIX_TIMESTAMP())";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
        } elseif ($status > 0) {
            $sql     = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . (int)$friendid . "' AND you = '" . (int)$userid . "'";
            $sql2    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . (int)$userid . "' AND you = '" . (int)$friendid . "'";
            $result  = $GLOBALS['xoopsDB']->queryF($sql);
            $result = $result && $GLOBALS['xoopsDB']->queryF($sql2);

            // Since friendship is canceled also following is deleted
            $this->toogleFollow(1, $userid, $friendid);
        }

        return $result ? true : false;
    }

    /**
     * toogleFollow function
     *
     * Insert following to db or delete if requested
     *
     * @param int $following
     * @param int $myUid
     * @param int $friend
     * @return bool true on success
     */
    public function toogleFollow($following, $myUid, $friend)
    {
        if (0 == $following) {
            $sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " (me,you,status,date) VALUES ('" . $myUid . "', '" . $friend . "', '1', UNIX_TIMESTAMP())";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
        } elseif ($following > 0) {
            $sql     = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE you = '" . (int)$friend . "'"
                     . " AND me = '" . (int)$myUid . "'";
            $sql2    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE me = '" . (int)$friend . "'"
                     . " AND you = '" . (int)$myUid . "'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
        }

        return $result ? true : false;
    }

    /**
     * Set Friendship Status in dB
     *
     * @param int $stat
     * @param int $myUid
     * @param int $friend
     * @return bool true on success, false on failure
     */
    public function setFriendshipStat($stat, $myUid, $friend)
    {
        $result = $result2 = false;
        if (1 == $stat) {
            $query  = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " SET status = '2' WHERE `me` = '" . $friend . "' AND `you` = '" . $myUid . "'";
            $query2 = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " (me,you,status,date) VALUES ('" . $myUid . "', '" . $friend . "', '2', UNIX_TIMESTAMP())";
            $result = $GLOBALS['xoopsDB']->queryF($query);
            $result = $result && $GLOBALS['xoopsDB']->queryF($query2);
        } elseif (0 > $stat) {
            $query  = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . (int)$friend . "' AND you = '" . (int)$myUid . "'";
            $query2 = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE you = '" . (int)$friend . "' AND me = '" . (int)$myUid . "'";
            $result = $GLOBALS['xoopsDB']->queryF($query);
            $result = $result && $GLOBALS['xoopsDB']->queryF($query2);
        }
        return $result ? true : false;
    }

    /**
     * deleteWallMsg function
     * @param int $id
     * @param int $smallworld_msg_id
     * @return bool
     */
    public function deleteWallMsg($id, $smallworld_msg_id)
    {
        $query  = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE msg_id = '" . $smallworld_msg_id . "'";
        $result = $GLOBALS['xoopsDB']->queryF($query);
        $query2 = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE msg_id_fk = '" . $smallworld_msg_id . "'";
        $result = $result && $GLOBALS['xoopsDB']->queryF($query2);
        //delete votes
        $query3 = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE msg_id = '" . $smallworld_msg_id . "'";
        $result = $result && $GLOBALS['xoopsDB']->queryF($query3);

        return $result ? true : false;
    }

    /**
     * deleteWallComment function
     * - Delete Comments
     * @param int $smallworld_com_id
     * @return true
     */
    public function deleteWallComment($smallworld_com_id)
    {
        $query   = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE com_id = '" . $smallworld_com_id . "'";
        $result  = $GLOBALS['xoopsDB']->queryF($query);
        $query2  = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE com_id = '" . $smallworld_com_id . "'";
        $result2 = $GLOBALS['xoopsDB']->queryF($query2);

        return true;
    }

    /**
     * Count Users rates
     *
     * @param int    $userid
     * @param string $column
     * @return int
     */
    public function countUsersRates($userid, $column)
    {
        $sum    = 0;
        // @sanitize $column - make sure it's a valid column in the vote dB table
        $validCol = in_array($column, ['up', 'down']) ? $column : 'vote_id';
        $query    = 'SELECT SUM(' . $validCol . ') AS sum FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE owner = '" . (int)$userid . "'";
        $result   = $GLOBALS['xoopsDB']->queryF($query);
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $sum = $row['sum'];
        }

        return (int)$sum;
    }

    /**
     * Delete user account and associate rows across tables
     *
     * echos string to display
     *
     * @param int $userid
     * @return bool  true on success, false on failure
     */
    public function deleteAccount($userid)
    {
        $userid = (int)$userid;
        $user     = new \XoopsUser($userid);
        $username = $user->uname();
        $sql01    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " WHERE userid = '" . $userid . "'";
        $sql02    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_comments') . " WHERE uid_fk = '" . $userid . "'";
        $sql03    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_followers') . " WHERE me = '" . $userid . "' OR you = '" . $userid . "'";
        $sql04    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_friends') . " WHERE me = '" . $userid . "' OR you = '" . $userid . "'";
        $sql05    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_images') . " WHERE userid = '" . $userid . "'";
        $sql06    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_messages') . " WHERE uid_fk = '" . $userid . "'";
        $sql07    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . $userid . "'";
        $sql08    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " WHERE user_id = '" . $userid . "'";
        $sql09    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_complaints') . " WHERE owner = '" . $userid . "' OR byuser_id = '" . $userid . "'";
        $sql10    = 'DELETE FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . " WHERE userid = '" . $userid . "'";

        $result01 = $GLOBALS['xoopsDB']->queryF($sql01);
        $result02 = $GLOBALS['xoopsDB']->queryF($sql02);
        $result03 = $GLOBALS['xoopsDB']->queryF($sql03);
        $result04 = $GLOBALS['xoopsDB']->queryF($sql04);
        $result05 = $GLOBALS['xoopsDB']->queryF($sql05);
        $result06 = $GLOBALS['xoopsDB']->queryF($sql06);
        $result07 = $GLOBALS['xoopsDB']->queryF($sql07);
        $result08 = $GLOBALS['xoopsDB']->queryF($sql08);
        $result09 = $GLOBALS['xoopsDB']->queryF($sql09);
        $result10 = $GLOBALS['xoopsDB']->queryF($sql10);
        // Remove picture dir
        $dirname = XOOPS_ROOT_PATH . '/uploads/albums_smallworld' . '/' . $userid . '/';
        $result11 = $this->smallworld_remDir($userid, $dirname, $empty = false);
        echo $username . _AM_SMALLWORLD_ADMIN_USERDELETEDALERT;

        return $result01 && $result02 && $result03 && $result04 && $result05 && $result06 && $result07 && $result08 && $result09 && $result10 && $result11;
    }

    /**
     * Delete images from users on delete
     *
     * @param int $userid
     * @return bool
     */
    public function SmallworldDeleteDirectory($userid)
    {
        $dirname = XOOPS_ROOT_PATH . '/uploads/albums_smallworld' . '/' . (int)$userid . '/';
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }
        while (false !== ($file = readdir($dir_handle))) {
            if ('.' !== $file && '..' !== $file) {
                if (!is_dir($dirname . '/' . $file)) {
                    unlink($dirname . '/' . $file);
                } else {
                    $this->SmallworldDeleteDirectory($dirname . '/' . $file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);

        return true;
    }

    /**
     * Remove user image dir in uploads
     *
     * @param int         $userid
     * @param string|bool $directory
     * @param bool|int    $empty
     * @return bool
     */
    public function smallworld_remDir($userid, $directory, $empty = false)
    {
        //@todo verify $userid should be int and then sanitize $userid accordingly before
        //      executing this routine
        if (!empty($userid)) {
            if ('/' === mb_substr($directory, -1)) {
                $directory = mb_substr($directory, 0, -1);
            }

            if (!file_exists($directory) || !is_dir($directory)) {
                return false;
            } elseif (!is_readable($directory)) {
                return false;
            }
            $directoryHandle = opendir($directory);
            while (false !== ($contents = readdir($directoryHandle))) {
                if ('.' !== $contents && '..' !== $contents) {
                    $path = $directory . '/' . $contents;
                    if (is_dir($path)) {
                        $this->smallworld_remDir($userid, $path);
                    } else {
                        unlink($path);
                    }
                }
            }
            closedir($directoryHandle);
            if (false === $empty) {
                if (!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * Update private settings
     *
     * @param mixed $id user's id
     * @param mixed $posts
     * @return string serialized settings for this id
     */
    public function saveSettings($id, $posts)
    {
        $id = (int)$id;
        $sql    = 'SELECT value FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . ' WHERE userid = ' . $id . '';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        if ($i > 0) {
            $sql = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . " SET value = '" . $posts . "' WHERE userid = " . (int)$id . '';
        } else {
            $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . " (userid,value) VALUES ('" . $id . "', '" . $posts . "')";
        }
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        return $this->getSettings($id);
    }

    /**
     * Retrieve private settings
     *
     * @param mixed $userid
     * @return string serialized string
     */
    public function getSettings($userid)
    {
        $sql    = 'SELECT value FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . ' WHERE userid = ' . (int)$userid . '';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        if ($i < 1) {
            $posts = serialize(
                [
                    'posts'    => 0,
                    'comments' => 0,
                    'notify'   => 1,
                ]
            );
            $this->saveSettings($userid, $posts);
            $retVal = $this->getSettings($userid);
        } else {
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $data = $row['value'];
            }

            $retVal = json_encode(unserialize(stripslashes($data)));
        }

        return $retVal;
    }
}
