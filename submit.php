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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/submit.php $
* @Last committed:		$Revision: 8815 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2012-01-26 10:33:48 -0500 (Thu, 26 Jan 2012) $
* @ID:					$Id: submit.php 8815 2012-01-26 15:33:48Z djculex $
**/
include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
global $xoopsUser,$xoopsLogger;
    $xoopsLogger->activated = false;
    $db = new SmallWorldDB;
    $mail = new smallworld_mail;

    $db->handlePosts ();
    
    // Create user albums etc
    $img = new smallWorldImages;
    $userID = $xoopsUser->getVar('uid');
    if ($_POST['function'] != 'edit') {
        $img->createAlbum ($userID);
        if(smallworld_GetModuleOption('smallworldusemailnotis', $repmodule='smallworld') != 0) {
            $mail->sendMails ($userID, $userID, 'register', $link=null, array());
        }
    }
