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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/galleryshow.php $
* @Last committed:		$Revision: 11659 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-06-12 19:42:10 +0200 (on, 12 jun 2013) $
* @ID:					$Id: galleryshow.php 11659 2013-06-12 17:42:10Z djculex $
**/
include_once("../../mainfile.php");
$xoopsOption['template_main'] = 'smallworld_galleryshow.html';
include_once(XOOPS_ROOT_PATH."/header.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
global $xoopsUser, $xoTheme;
	
	if ($xoopsUser) {			
		$id = $xoopsUser->getVar('uid');
		$check = new SmallWorldUser;
		$image = new SmallWorldImages;
		
		// Check if inspected userid -> redirect to userprofile and show admin countdown
			$inspect = Smallworld_isInspected ($id);
			if ($inspect['inspect'] == 'yes') {
				redirect_header("userprofile.php?username=".$xoopsUser->getVar('uname'), 1); 
			}		
		
		$profile = $check->checkIfProfile($id);
		if ($profile >= 2) {
			$myusername = $xoopsUser->getVar('uname');
			if ( $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
				$xoopsTpl->assign('isadminuser', 'YES');
			}
			$user = new XoopsUser($id);
			$userID = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
			$username = $_GET['username'];
			$countimages = $image->count($userID);
			$userisfriend = $check->friendcheck($id,$userID);
			
			//$gallery = $image->viewalbum ($id, $user=$xoopsUser->getVar('uid'));
			$gallery = $image->viewalbum ($id, $userID);
			 $xoopsTpl->assign('countimages',$countimages);
			 $xoopsTpl->assign('userisfriend',$userisfriend);
			 $xoopsTpl->assign('gallery',$gallery);
			 $xoopsTpl->assign('closealbum',_SMALLWORLD_ALBUMTITLETEXT);
			 $xoopsTpl->assign('username',$username);
			 $xoopsTpl->assign('myusername',$myusername);
			 $xoopsTpl->assign('gallerytitleheader',_SMALLWORLD_TITLEHEADER);
			$xoopsTpl->assign('check',1); 
		}	elseif ($profile == 0) {
			 $check->chkUser ();
			}
	} else {
		redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
	}
include(XOOPS_ROOT_PATH."/footer.php");
?>