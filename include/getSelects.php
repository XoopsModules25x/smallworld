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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/include/getSelects.php $
* @Last committed:		$Revision: 11699 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-06-16 19:29:32 +0200 (sø, 16 jun 2013) $
* @ID:					$Id: getSelects.php 11699 2013-06-16 17:29:32Z djculex $
**/
include_once("../../../mainfile.php");
include XOOPS_ROOT_PATH.'/header.php';
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/arrays.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
global $xoopsUser, $xoopsModule,$xoopsLogger;
$xoopsLogger->activated = false;
    if ($xoopsUser) {
		$db = new SmallWorldDB;
		$id = $xoopsUser->getVar('uid');
		$relat = $db->getVar($id,'relationship');
		$partner = $db->getVar($id,'partner');
		$gender = $db->getVar($id,'gender');
		$politic = $db->getVar($id,'politic');
		$religion = $db->getVar($id,'religion');
        header('Content-type: "application/json"');
        echo "{\"relat\":\"$relat\", \"partner\":\"$partner\", \"gender\":\"$gender\", \"politic\":\"$politic\", \"religion\":\"$religion\"}";
	} else {
        header('Content-type: "application/json"');
        echo "{\"relat\":\"\", \"partner\":\"\", \"gender\":\"\", \"politic\":\"\", \"religion\":\"\"}";
    }
?>