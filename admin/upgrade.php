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
    if (smallworld_DoTableExists($xoopsDB->prefix('smallworld_settings'))) {
        // Table exists
        return false;
    } else {
        // Table does not exist -> create

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $xoopsDB->prefix('smallworld_settings') . ' (';
        $sql .= '`id` int(11) NOT NULL AUTO_INCREMENT,';
        $sql .= '`userid` int(11) NOT NULL,';
        $sql .= '`value` text NOT NULL,';
        $sql .= 'PRIMARY KEY (`id`)';
        $sql .= ') ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';
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
    $sql = 'ALTER TABLE ' . $xoopsDB->prefix('smallworld_messages') . ' CHANGE message message TEXT';
    $result = $xoopsDB->queryF($sql);
}

/**
 * function to correct the comments from varchar to text
 * @return void
 */
function smallworld_comToBlog()
{
    global $xoopsDB;
    $sql = 'ALTER TABLE ' . $xoopsDB->prefix('smallworld_comments') . " CHANGE 'comments' 'comments' TEXT";
    $result = $xoopsDB->queryF($sql);
}
