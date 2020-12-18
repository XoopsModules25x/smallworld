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

// English language file for Smallworld Admin.php

// tab titles
define('_AM_SMALLWORLD_ADMINMESSAGESEND', 'Post to all');
define('_AM_SMALLWORLD_LOGSMAINTNANCE', 'Database maintenance');

// tab one in admin section
define('_AM_SMALLWORLD_MODULEINSTALL', 'Module version installed');
define('_AM_SMALLWORLD_INSTALLDATE', 'Module installed on');
define('_AM_SMALLWORLD_DATEOFFIRSTMESSAGE', 'Date of the oldest message');
define('_AM_SMALLWORLD_TOTALUSERS', 'Total members using Smallworld');
define('_AM_SMALLWORLD_AVERAGEMSGPERDAY', 'Average messages per day');
define('_AM_SMALLWORLD_TOPCHATTERS', 'Most active users overall');
define('_AM_SMALLWORLD_TOPCHATTERS_TODAY', 'Most active users in last 24 hours');
define('_AM_SMALLWORLD_TOPRATEDUSERS', 'Highest rated users');
define('_AM_SMALLWORLD_BOTTOMRATEDUSERS', 'Worst rated users');
define('_AM_SMALLWORLD_STATISTICS_TITLE', 'Smallworld Statistics');
define('_AM_SMALLWORLD_MODULEINFO', 'Module info');
define('_AM_SMALLWORLD_USERSTATS', 'User stats');
define('_AM_SMALLWORLD_NONEYET', 'No messages in database');
define('_AM_SMALLWORLD_NO', 'none');
define('_AM_SMALLWORLD_THEREARE', 'There are');
define('_AM_SMALLWORLD_UPDATE_STATUS', 'Status of your Smallworld version:');

// tab two in admin section

// tab three in admin section
define('_AM_SMALLWORLD_USERADMIN_TITLE', 'User administration');
define('_AM_SMALLWORLD_USERADMININSPECT_TITLE', 'Users under admin inspection');
define('_AM_SMALLWORLD_USERADMINNOINSPECT_TITLE', 'Normal users');
define('_AM_SMALLWORLD_TITLE_IMAGE', 'User image');
define('_AM_SMALLWORLD_TITLE_USERNAME', 'User name');
define('_AM_SMALLWORLD_TITLE_REALNAME', 'Real name');
define('_AM_SMALLWORLD_TITLE_IP', 'Ip');
define('_AM_SMALLWORLD_TITLE_COMPLAINTS', 'Complaints received');
define('_AM_SMALLWORLD_TITLE_INSPECTTIME', 'Inspection time');
define('_AM_SMALLWORLD_TITLE_INSPECTADD', 'Add inspection time');
define('_AM_SMALLWORLD_TITLE_DELETEUSER', 'Delete user account');
define('_AM_SMALLWORLD_TITLE_INSPECTDELETE', 'Clear inspection time');
define('_AM_SMALLWORLD_ADDTIMEDROPDOWN_MINUTES', ' Minutes');
define('_AM_SMALLWORLD_ADDTIMEDROPDOWN_NOCHANGE', 'No change');

// various others
define('_AM_SMALLWORLD_UPDATE_CRITICAL_UPD', ' There is a critical update ready!!');
define('_AM_SMALLWORLD_UPDATE_NORMAL_UPD', ' There is a newer version ready for download');
define('_AM_SMALLWORLD_UPDATE_SERVER_ERROR', 'Server seems to be down or update is in progress.<br>Try again later.');
define('_AM_SMALLWORLD_UPDATE_FILE_DESC', 'Description of newest version');
define('_AM_SMALLWORLD_UPDATE_SERVER_FILE', 'You can download the new version from here');
define('_AM_SMALLWORLD_UPDATE_YOUHAVENEWESTVERSION', ' You have the newest version of Smallworld');
define('_AM_SMALLWORLD_HELP', 'Help');
define('_AM_SMALLWORLD_ADMIN_USERDELETEDALERT', ' and associated folders, files and table rows has been deleted');
define('_AM_SMALLWORLD_STATS_POS', '#');
define('_AM_SMALLWORLD_STATS_NAME', 'Name');
define('_AM_SMALLWORLD_STATS_AMOUNT', 'Amount');
define('_AM_SMALLWORLD_STATS_IMG', 'Icon');

define(
    '_AM_SMALLWORLD_SP',
       '<img style="margin: 0pt 5px 0pt 0pt; vertical-align: middle;" src="../assets/images/sp.png" height="30px" width="30px"> <a style="position: relative; margin: 0px;vertical-align: middle;" href="javascript:void(0);" id="smallworldDonate">If you like this module and the work I put into it, you could keep me awake by buying me a coffee</a>'
);
define('_AM_SMALLWORLD_FOOTER', "<div class='center smallsmall italic pad5'>Module SmallWorld is maintained by <a class='tooltip' rel='external' href='http://culex.dk/' title='Visit my Website'>Culex</a></div>");

//1.16
define('_AM_SMALLWORLD_UPGRADEFAILED0', "Update failed - couldn't rename field '%s'");
define('_AM_SMALLWORLD_UPGRADEFAILED1', "Update failed - couldn't add new fields");
define('_AM_SMALLWORLD_UPGRADEFAILED2', "Update failed - couldn't rename table '%s'");
define('_AM_SMALLWORLD_ERROR_COLUMN', 'Could not create column in database : %s');
define('_AM_SMALLWORLD_ERROR_BAD_XOOPS', 'This module requires XOOPS %s+ (%s installed)');
define('_AM_SMALLWORLD_ERROR_BAD_PHP', 'This module requires PHP version %s+ (%s installed)');
define('_AM_SMALLWORLD_ERROR_TAG_REMOVAL', 'Could not remove tags from Tag Module');

define('_AM_SMALLWORLD_FOLDERS_DELETED_OK', 'Upload Folders have been deleted');

// Error Msgs
define('_AM_SMALLWORLD_ERROR_BAD_DEL_PATH', 'Could not delete %s directory');
define('_AM_SMALLWORLD_ERROR_BAD_REMOVE', 'Could not delete %s');
define('_AM_SMALLWORLD_ERROR_NO_PLUGIN', 'Could not load plugin');
define('_AM_SMALLWORLD_ERROR_INVALID_ENTRY', 'Invalid Entry %s');
