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
* @Repository path:		$HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/stats.php $
* @Last committed:		$Revision: 8817 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2012-01-26 19:05:41 +0100 (to, 26 jan 2012) $
* @ID:					$Id: stats.php 8817 2012-01-26 18:05:41Z djculex $
**/
include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/arrays.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once (XOOPS_ROOT_PATH.'/class/template.php');
global $xoopsUser, $xoopsTpl, $xoTheme;
$xoopsLogger->activated = false;
$tpl = new XoopsTpl();

$userid = $xoopsUser->getVar('uid');
$newusers = smallworld_Stats_newest ();
$m_a_users = Smallworld_mostactiveusers_allround();
$br_users = Smallworld_topratedusers();
$wo_users = Smallworld_worstratedusers();
$birth = smallworld_nextBirthdays ();
$sp = smallworld_sp ();
$tpl->assign('newusers',$newusers);		
$tpl->assign('mostactiveU',$m_a_users);	
$tpl->assign('bestratedU',$br_users);
$tpl->assign('worstratedU',$wo_users);
$tpl->assign('sp',$sp);

if (!empty($birth)) {
	$tpl->assign('birthdays',$birth);
} else {
	$tpl->assign('birthdays',0);
}
$tpl->display(XOOPS_ROOT_PATH .'/modules/smallworld/templates/getStat.html');
?>