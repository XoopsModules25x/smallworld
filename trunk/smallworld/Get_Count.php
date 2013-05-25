<?php
/**
* You may not change or alter any portion of this comment or credits
* of supporting developers from this source code or any supporting source code
* which is considered copyrighted (c) material of the original comment or credit authors.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* @copyright:			The XOOPS Project http://sourceforge.net/projects/xoops/
* @license:				http://www.fsf.org/copyleft/gpl.html GNU public license
* @module:				Smallworld
* @Author:				Michael Albertsen (http://culex.dk) <culex@culex.dk>
* @copyright:			2011 Culex
* @Repository path:		$HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/Get_Count.php $
* @Last committed:		$Revision: 9712 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2012-06-25 16:00:46 +0200 (ma, 25 jun 2012) $
* @ID:					$Id: Get_Count.php 9712 2012-06-25 14:00:46Z djculex $
**/
include '../../mainfile.php';
include_once (XOOPS_ROOT_PATH.'/class/template.php');
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");

global $xoopsUser, $xoopsLogger, $xoopsDB, $xoopsTpl;
$xoopsLogger->activated = false;
$xoopsTpl->caching = 0;
$_COOKIE[session_name()] = session_id();
if ($xoopsUser) {
	if($_GET['SmallworldGetUserMsgCount']) {
        $counts = smallworld_getCountFriendMessagesEtc();
        header('Content-type: application/json');
        echo "{\"NewUserMsgCount\":$counts}";
	}
}
?>