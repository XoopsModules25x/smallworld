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
* @Last committed:		$Revision: 11576 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-05-22 15:25:30 +0200 (on, 22 maj 2013) $
* @ID:					$Id: index.php 11576 2013-05-22 13:25:30Z djculex $
**/

include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/publicWall.php");
global $xoopsUser, $xoTheme,$xoopsConfig,$xoopsLogger, $xoopsModule;
    
$set = smallworld_checkPrivateOrPublic ();
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
			$myavatar 		=	$Wall->Gravatar($id);
			$myavatarlink 	=	smallworld_getAvatarLink($id, $myavatar);
            $myavatar_size  = smallworld_getImageSize(80, 100, $myavatarlink);
            $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 35);
			
			//Get friends invitations
			$getInvitations = ($xoopsUser) ? $check->getRequests ($id) : 0;
			
			if (!empty($updatesarray)) {
			foreach ($updatesarray as $data) {
                    
                    $USW = array();
                        $USW['posts'] = 0;
                        $USW['comments'] = 0;
                    
                    if ($xoopsUser) {
                        
                        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) || $data['uid_fk'] == $id) {
                            $USW['posts'] = 1;
                            $USW['comments'] = 1;
                        } else {
                            $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                        } 
                    }
                    
                    if (!$xoopsUser) {
                        $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                    }

                $wm['msg_id']			= 	$data['msg_id'];
                $wm['orimessage']		=	($USW['posts'] == 1) ? str_replace(array("\r", "\n"), '',Smallworld_stripWordsKeepUrl($data['message'])):'';
				$wm['message']			=	($USW['posts'] == 1) ? smallworld_tolink(htmlspecialchars_decode($data['message']), $data['uid_fk']):_SMALLWORLD_MESSAGE_PRIVSETPOSTS;
				$wm['message'] = Smallworld_cleanup($wm['message']);
                $wm['created']			=	smallworld_time_stamp($data['created']);
				$wm['username']			=	$data['username'];
				$wm['uid_fk']			=	$data['uid_fk'];
				$wm['priv']				=	$data['priv'];
				$wm['avatar']			=	$Wall->Gravatar($data['uid_fk']);
				$wm['avatar_link']		=	smallworld_getAvatarLink ($data['uid_fk'], $wm['avatar']);
                $wm['avatar_size']      =   smallworld_getImageSize(80, 100, $wm['avatar_link']);
                $wm['avatar_highwide']  =   smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);
				$wm['vote_up']			= 	$Wall->countVotes ('msg', 'up', $data['msg_id']);
				$wm['vote_down']		= 	$Wall->countVotes ('msg', 'down', $data['msg_id']);
				$wm['compl_msg_lnk']	=	"<a href='".XOOPS_URL."/modules/smallworld/permalink.php?ownerid=".$data['uid_fk'];
				$wm['compl_msg_lnk']   .=	"&updid=".$data['msg_id']."'>"._SMALLWORLD_COMP_MSG_LNK_DESC."</a>";
				$wm['sharelinkurl']		=	XOOPS_URL."/modules/smallworld/smallworldshare.php?ownerid=".$data['uid_fk'];
				$wm['sharelinkurl']	   .=	"&updid=".$data['msg_id']."";
				$wm['usernameTitle']	=	$wm['username']._SMALLWORLD_UPDATEONSITEMETA.$xoopsConfig['sitename'];
				$wm['sharelink']		=	$Wall->GetSharing ($wm['msg_id'],$wm['priv']);
				$wm['linkimage']        =   XOOPS_URL."/modules/smallworld/images/link.png";
                $wm['permalink']        =   XOOPS_URL."/modules/smallworld/permalink.php?ownerid=".$data['uid_fk']."&updid=".$data['msg_id'];
                $wm['sharediv']			=	$Wall->GetSharingDiv ($wm['msg_id'],$wm['priv'], $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
				$wm['commentsarray']	=	$Wall->Comments($data['msg_id']);	
                $xoopsTpl->append('walldata', $wm);
                if (!empty($wm['commentsarray'])){
					foreach($wm['commentsarray'] as $cdata) {
                    
                    $USC = array();
                        $USC['posts'] = 0;
                        $USC['comments'] = 0;
                    
                    if ($xoopsUser) {
                        
                        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) && $cdata['uid_fk'] == $id) {
                            $USC['posts'] = 1;
                            $USC['comments'] = 1;
                        } else {
                            $USC = json_decode($dBase->GetSettings($cdata['uid_fk']), true);
                        } 
                    }
                    
                    if (!$xoopsUser) {
                        $USC = json_decode($dBase->GetSettings($cdata['uid_fk']), true);
                    }
                    
                        $wc['msg_id_fk']		=		$cdata['msg_id_fk'];
						$wc['com_id']			=		$cdata['com_id'];
						$wc['comment']			=		($USC['comments']  == 1) ? nl2br(smallworld_tolink(htmlspecialchars_decode($cdata['comment']),$cdata['uid_fk'])):_SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
                        $wc['comment']		    =		Smallworld_cleanup($wc['comment']);
						$wc['time']				=		smallworld_time_stamp($cdata['created']);
						$wc['username']			=		$cdata['username'];
						$wc['uid']				=		$cdata['uid_fk'];
						$wc['myavatar']			=		$Wall->Gravatar($id);
						$wc['myavatar_link']	=		$myavatarlink;
                        $wc['avatar_size']      =       smallworld_getImageSize(80, 100, $wc['myavatar_link']);
                        $wc['avatar_highwide']  =       smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);                        
                        $wc['cface']			=		$Wall->Gravatar($cdata['uid_fk']);
						$wc['avatar_link']		=		smallworld_getAvatarLink ($cdata['uid_fk'], $wc['cface']);
						$wc['compl_msg_lnk']	=		"<a href='".XOOPS_URL."/modules/smallworld/permalink.php?ownerid=".Smallworld_getOwnerFromComment($cdata['msg_id_fk']);
						$wc['compl_msg_lnk']   .=		"&updid=".$cdata['msg_id_fk']."#".$cdata['com_id']."'>"._SMALLWORLD_COMP_MSG_LNK_DESC."</a>";
						$wc['vote_up']			= 		$Wall->countVotesCom ('com', 'up', $cdata['msg_id_fk'],$cdata['com_id']);
						$wc['vote_down']		= 		$Wall->countVotesCom ('com', 'down', $cdata['msg_id_fk'],$cdata['com_id']);			
                        $xoopsTpl->append('comm', $wc);
					}
				}
			
			}
			}
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
			$xoopsTpl->assign('myavatar',$myavatar);
			$xoopsTpl->assign('myavatarlink',$myavatarlink);
            $xoopsTpl->assign('myavatar_highwide',$myavatar_highwide);
			$xoopsTpl->assign('friendinvitations',$getInvitations);
            $xoopsTpl->assign('access',$set['access']);
			
	//	}
        
		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.oembed.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/wall.js');
		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/ajaxupload.3.5.js');
		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.avatar_helper.js');
		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.bookmark.js');
		$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/oembed.css');
        //$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/colorbox.css');
		$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
		$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');  	
    
	}  
    if ($profile == 1 && $set['access'] == 0) {
       redirect_header(XOOPS_URL . "/modules/smallworld/register.php");
    } 
    
    if ($profile == 0 && $set['access'] == 0) {
        redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
    }

include(XOOPS_ROOT_PATH."/footer.php");