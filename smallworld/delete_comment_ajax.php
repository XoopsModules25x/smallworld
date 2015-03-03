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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/delete_comment_ajax.php $
* @Last committed:		$Revision: 8749 $
* @Last changed by:		$Author: beckmi $
* @Last changed date:	$Date: 2012-01-18 00:17:13 -0500 (Wed, 18 Jan 2012) $
* @ID:					$Id: delete_comment_ajax.php 8749 2012-01-18 05:17:13Z beckmi $
**/
include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
global $xoopsUser, $xoopsModule,$xoopsLogger;
$xoopsLogger->activated = false;
    
    $id = $xoopsUser->getVar('uid');
    $msgowner = $_POST['msgowner'];
    
if ($xoopsUser) {
    
  if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) || $id == $msgowner) {
    $db = new SmallWorldDB;
    if (isset($_POST['smallworld_com_id'])) {
        $smallworld_com_id    = $_POST['smallworld_com_id'];
        $data    = $db->deleteWallComment($smallworld_com_id);
    }
 }
}
