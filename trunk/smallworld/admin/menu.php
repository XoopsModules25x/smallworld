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
* @Repository path:		$HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/admin/menu.php $
* @Last committed:		$Revision: 8905 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2012-02-07 22:57:57 +0100 (ti, 07 feb 2012) $
* @ID:					$Id: menu.php 8905 2012-02-07 21:57:57Z djculex $
**/

$dirname = basename(dirname(dirname(__FILE__)));
$module_handler = xoops_gethandler('module');
$module = $module_handler->getByDirname($dirname);
$pathIcon32 = $module->getInfo('icons32');

$i = 1;
$adminmenu[$i]['title'] = _MI_SMALLWORLDMENU_INDEX;
$adminmenu[$i]['link'] = 'admin/index.php';
$adminmenu[$i]['icon'] = $pathIcon32."/home.png";

//$i++;
//$adminmenu[$i]['title'] = _MI_SMALLWORLD_ADMENU;
//$adminmenu[$i]['link'] = 'admin/moduleinfo.php';
//$adminmenu[$i]['icon'] = $pathIcon32."/update.png";

$i++;
$adminmenu[$i]['title'] = _MI_SMALLWORLDMENU_USERADMIN;
$adminmenu[$i]['link'] = "admin/useradmin.php";
$adminmenu[$i]['icon'] = $pathIcon32."/administration.png";

$i++;
$adminmenu[$i]["title"] =_MI_SMALLWORLDMENU_ABOUT;
$adminmenu[$i]["link"]  = "admin/about.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';