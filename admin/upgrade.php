<?php
// Upgrade functions used when adding or altering tables etc between versions
/**
 * Function to do the creatind
 *
 */
function smallworld_doUpgrade()
{
    global $xoopsDB, $xoopsUser;
    varcharToBlog();
    smallworld_comToBlog();
    smallworld_adminAvatarRename();
	smallworld_adminDefaultNull();
	smallworld_comDefaultNull();
	smallworld_complaintsDefaultNull();
	smallworld_followersDefaultNull();
	smallworld_friendsDefaultNull();
	smallworld_imagesDefaultNull();
	smallworld_messagesDefaultNull();
	smallworld_settingsDefaultNull();
	smallworld_userDefaultNull();
	smallworld_voteDefaultNull();
    if (smallworld_DoTableExists($xoopsDB->prefix('smallworld_settings'))) {
        // Table exists
        return false;
    } else {
        // Table does not exist -> create

        $sql = "CREATE TABLE IF NOT EXISTS " . $xoopsDB->prefix('smallworld_settings') . " (';
        $sql .= '`id` int(11) NOT NULL AUTO_INCREMENT,';
        $sql .= '`userid` int(11) NOT NULL,';
        $sql .= '`value` text NOT NULL,';
        $sql .= 'PRIMARY KEY (`id`)';
        $sql .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        $result = $xoopsDB->queryF($sql);
    }
}

function smallworld_adminAvatarRename()
{
    global $xoopsDB;
    $sql     = 'update ' . $xoopsDB->prefix('smallworld_admin') . " set userimage = '' WHERE userimage = 'blank.gif' || userimage NOT REGEXP '\.(jpe?g|gif|png|bmp)'";
    $sql2    = 'update ' . $xoopsDB->prefix('smallworld_user') . " set userimage = '' WHERE userimage = 'blank.gif' || userimage NOT REGEXP '\.(jpe?g|gif|png|bmp)'";
    $result  = $xoopsDB->queryF($sql);
    $result2 = $xoopsDB->queryF($sql2);
}

/**
 * @param $tablename
 * @return bool
 */
function smallworld_DoTableExists($tablename)
{
    global $xoopsDB;
    $result = $xoopsDB->queryF("SHOW TABLES LIKE '$tablename'");

    return ($xoopsDB->getRowsNum($result) > 0);
}

/**
 * Alter table messages from varchar(200) to text()
 * @return void
 */
function varcharToBlog()
{
    global $xoopsDB;
    $sql = "ALTER TABLE " . $xoopsDB->prefix('smallworld_messages') . " CHANGE message message TEXT";
    $result = $xoopsDB->queryF($sql);
}

/**
 * function to correct the comments from varchar to text
 * @return void
 */
function smallworld_comToBlog()
{
    global $xoopsDB;
    $sql = "ALTER TABLE " . $xoopsDB->prefix('smallworld_comments') . " CHANGE `comment` `comment` TEXT";
    $result = $xoopsDB->queryF($sql);
}

/**
 * function to correct null values in MariaDb / Mysql
 * @return void
 */
function smallworld_adminDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE " . $xoopsDB->prefix('smallworld_admin') . "  
	CHANGE `userid` `userid` INT(11) NULL, 
	CHANGE `username` `username` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `realname` `realname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `userimage` `userimage` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `ip` `ip` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `complaint` `complaint` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `inspect_start` `inspect_start` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `inspect_stop` `inspect_stop` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
}

/**
 * function to correct null values in MariaDb / Mysql
 * @return void
 */
function smallworld_comDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE  " . $xoopsDB->prefix('smallworld_comments') . " 
	CHANGE `com_id` `com_id` INT(11) NOT NULL AUTO_INCREMENT, 
	CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `msg_id_fk` `msg_id_fk` INT(11) NULL, CHANGE `uid_fk` `uid_fk` INT(11) NULL, 
	CHANGE `ip` `ip` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
}

/**
 * function to correct null values in MariaDb / Mysql
 * @return void
 */
function smallworld_complaintsDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE   " . $xoopsDB->prefix('smallworld_complaints') . " CHANGE `complaint_id` `complaint_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
	CHANGE `link` `link` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `byuser_id` `byuser_id` INT(11) NULL, 
	CHANGE `owner` `owner` INT(11) NULL; ";
	$result = $xoopsDB->queryF($sql);
}

function smallworld_followersDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE   " . $xoopsDB->prefix('smallworld_followers') . " CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
	CHANGE `me` `me` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `you` `you` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `status` `status` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `date` `date` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
} 

function smallworld_friendsDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE   " . $xoopsDB->prefix('smallworld_friends') . " CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
	CHANGE `me` `me` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `you` `you` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `status` `status` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `date` `date` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
} 

function smallworld_imagesDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE  " . $xoopsDB->prefix('smallworld_images') . " CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
	CHANGE `userid` `userid` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `imgname` `imgname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `imgurl` `imgurl` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `time` `time` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `desc` `desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
}  

function smallworld_messagesDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE  " . $xoopsDB->prefix('smallworld_messages') . " CHANGE `msg_id` `msg_id` INT(11) NULL AUTO_INCREMENT, 
	CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `uid_fk` `uid_fk` INT(11) NULL, 
	CHANGE `priv` `priv` INT(1) NOT NULL, 
	CHANGE `created` `created` INT(11) NULL; ";
	$result = $xoopsDB->queryF($sql);
} 

function smallworld_settingsDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE  " . $xoopsDB->prefix('smallworld_settings') . " CHANGE `id` `id` INT(11) NULL AUTO_INCREMENT, 
	CHANGE `userid` `userid` INT(11) NULL, 
	CHANGE `value` `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
}  

function smallworld_userDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE  " . $xoopsDB->prefix('smallworld_user') . " CHANGE `id` `id` INT(5) UNSIGNED NOT NULL AUTO_INCREMENT, 
	CHANGE `userimage` `userimage` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `realname` `realname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `gender` `gender` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `intingender` `intingender` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `relationship` `relationship` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `partner` `partner` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `searchrelat` `searchrelat` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `birthday` `birthday` DATE NULL, 
	CHANGE `birthplace` `birthplace` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `birthplace_lat` `birthplace_lat` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `birthplace_lng` `birthplace_lng` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `birthplace_country` `birthplace_country` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `politic` `politic` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `religion` `religion` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `emailtype` `emailtype` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `screenname_type` `screenname_type` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `screenname` `screenname` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `mobile` `mobile` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `phone` `phone` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `adress` `adress` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `present_city` `present_city` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `present_lat` `present_lat` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `present_lng` `present_lng` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `present_country` `present_country` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `website` `website` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `interests` `interests` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `music` `music` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `tvshow` `tvshow` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `movie` `movie` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `books` `books` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `aboutme` `aboutme` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `school_type` `school_type` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `school` `school` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `schoolstart` `schoolstart` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `schoolstop` `schoolstop` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `employer` `employer` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `position` `position` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `jobstart` `jobstart` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `jobstop` `jobstop` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `friends` `friends` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `followers` `followers` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `admin_flag` `admin_flag` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;";
	$result = $xoopsDB->queryF($sql);
} 

function smallworld_voteDefaultNull()
{	
	global $xoopsDB;
	$sql = "ALTER TABLE " . $xoopsDB->prefix('smallworld_vote') . " CHANGE `vote_id` `vote_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
	CHANGE `msg_id` `msg_id` INT(8) UNSIGNED NULL, 
	CHANGE `com_id` `com_id` INT(11) NULL, 
	CHANGE `user_id` `user_id` INT(11) NULL, 
	CHANGE `owner` `owner` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL, 
	CHANGE `up` `up` TINYINT(3) UNSIGNED NULL, 
	CHANGE `down` `down` TINYINT(3) UNSIGNED NULL; ";
	$result = $xoopsDB->queryF($sql);
} 