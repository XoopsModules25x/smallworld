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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/index.php $
* @Last committed:		$Revision: 12114 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-10-01 19:11:18 +0200 (ti, 01 okt 2013) $
* @ID:					$Id: index.php 12114 2013-10-01 17:11:18Z djculex $
**/

include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/publicWall.php");
global $xoopsUser, $xoTheme,$xoopsConfig,$xoopsLogger, $xoopsModule;
    
$set = smallworld_checkPrivateOrPublic ();
$profile = 0;

if ($xoopsUser) {
    $xoopsOption['template_main'] = 'smallworld_index.html';
} elseif (!$xoopsUser && $set['access'] == 1) {
    $xoopsOption['template_main'] = 'smallworld_publicindex.html';
} else {
    redirect_header(XOOPS_URL . "/user.php", 5, _NOPERM);
}	
include_once(XOOPS_ROOT_PATH."/header.php");
	if ($set['access'] == 1) {
		$id = ($xoopsUser) ? $xoopsUser->getVar('uid'):0;
		$user = new XoopsUser($id);
        $dBase = new SmallWorldDB;
        
		// Check if inspected userid -> redirect to userprofile and show admin countdown
			$inspect = Smallworld_isInspected ($id);
			if ($inspect['inspect'] == 'yes') {
				redirect_header("userprofile.php?username=".$xoopsUser->getVar('uname'), 1); 
			}
		
		$xoopsTpl->assign('ownerofpage', $id);
		if ($xoopsUser) {
            if ( $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                $xoopsTpl->assign('isadminuser', 'YES');
                $profile = 2;
            }else {
                $xoopsTpl->assign('isadminuser', 'NO');
            }
        } 
        
        // Create form for private settings
        $form = new SmallWorldForm;
            $usersettings = $form->usersettings ($id, $selected=null);
            $xoopsTpl->assign('usersetting', $usersettings);

         
		$username = $user->getVar('uname');
		$check = new SmallWorldUser;
		$profile = ($xoopsUser) ? $check->checkIfProfile($id) : 0;
		
		if ($profile >= 2) {
			$xuser = new SmallWorldProfile;
			$xuser->ShowUser($id);
			$menu_startpage = "<a href='".XOOPS_URL."/modules/smallworld/publicindex.php'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/highrise.png'>"._SMALLWORLD_STARTPAGE."</a>";
            $menu_home = "<a href='".XOOPS_URL."/modules/smallworld/'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/house.png'>"._SMALLWORLD_HOME."</a>";
			$menu_profile = "<a href='".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$username."'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/user_silhouette.png'>"._SMALLWORLD_PROFILEINDEX."</a>";
			$menu_gallery = "<a href='".XOOPS_URL."/modules/smallworld/galleryshow.php?username=".$username."'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/picture.png'>"._SMALLWORLD_GALLERY."</a>";
			$menu_friends = "<a href='".XOOPS_URL."/modules/smallworld/friends.php?username=".$username."'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/group.png'>"._SMALLWORLD_FRIENDSPAGE."</a>";
		}        
			
			// Things to do with wall
			$Wall = ($profile >= 2) ? new Wall_Updates() : new Public_Wall_Updates; 
            if ($profile < 2 && $set['access'] == 1) {
                $pub = smallworld_checkUserPubPostPerm ();
                $updatesarray = $Wall->updates(0, $pub);
            } else {
                // Follow array here
                $followers = Smallworld_array_flatten($Wall->getFollowers($id),0);	
                $updatesarray=$Wall->Updates(0,$id, $followers);
            }
			
			//Get friends invitations
			$getInvitations = ($xoopsUser) ? $check->getRequests ($id) : 0; 
            $Wall->ParsePubArray ($updatesarray, $id);	
            
            if ($profile >= 2) {
                $xoopsTpl->assign('menu_startpage',$menu_startpage);
                $xoopsTpl->assign('menu_home',$menu_home);
                $xoopsTpl->assign('menu_profile',$menu_profile);
                $xoopsTpl->assign('menu_friends',$menu_friends);
                $xoopsTpl->assign('menu_gallery',$menu_gallery);
            }
			$xoopsTpl->assign('myusername',$username);          
            $xoopsTpl->assign('pagename','index');
			$xoopsTpl->assign('check',$profile); 

			$xoopsTpl->assign('friendinvitations',$getInvitations);
            $xoopsTpl->assign('access',$set['access']);
			
	//	}         	
    
	}  
    if ($profile == 1 && $set['access'] == 0) {
       redirect_header(XOOPS_URL . "/modules/smallworld/register.php");
    } 
    
    if ($profile == 0 && $set['access'] == 0) {
        //redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
    }

include(XOOPS_ROOT_PATH."/footer.php");