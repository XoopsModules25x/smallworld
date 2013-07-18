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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/userprofile.php $
* @Last committed:		$Revision: 11843 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-07-18 19:29:48 +0200 (to, 18 jul 2013) $
* @ID:					$Id: userprofile.php 11843 2013-07-18 17:29:48Z djculex $
**/
include_once("../../mainfile.php");
$xoopsOption['template_main'] = 'smallworld_userprofile_template.html';
include XOOPS_ROOT_PATH.'/header.php';
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/arrays.php");
global $xoopsUser,$xoopsLogger;
 $xoopsLogger->activated = false;
      //error_reporting(E_ALL); 
	if ($xoopsUser) {
		SmallworldDeleteOldInspects ();
		$id = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
		$yourid = $xoopsUser->getVar('uid'); // your uid
		$Xuser = new XoopsUser($id);
		$Xusername = $Xuser->getVar('uname');
		$check = new SmallWorldUser;
		$profile = $check->CheckIfProfile($yourid);
		$fr[0] = '';
		$fl[0] = '';
		
		if ($profile >= 2) {
			$user = new SmallWorldProfile;
            $dBase = new SmallWorldDB;
			$user->ShowUser($id);
			$username = $xoopsUser->getVar('uname'); //Myusername
			$inspected = Smallworld_isInspected($id);
				$xoopsTpl->assign('inspect',$inspected['inspect']);
				if ($inspected['inspect'] != 'no') {
					$xoopsTpl->assign('inspecttime',$inspected['totaltime']);
				}
				$xoopsTpl->assign('ownerofpage', $id);
			if($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
				$xoopsTpl->assign('isadminuser', 'YES');
				
			}
		      
		// Check status for relationship
		$fr = $check->friendcheck($yourid,$id);
				if ($fr[0] == 0) {
					$friendship_text = _SMALLWORLD_JSON_ADDFR_TEXT;
					if($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
						$xoopsTpl->assign('isuserafriend','no');
					} else {
						$xoopsTpl->assign('isuserafriend','no');
					}
				}
				if ($fr[0] == 1) {
					$friendship_text = _SMALLWORLD_JSON_CANCELFR_TEXT;
					if($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
						$xoopsTpl->assign('isuserafriend','no');
					} else {
						$xoopsTpl->assign('isuserafriend','no');
					}
				}
				if ($fr[0] == 2) {
					$friendship_text = _SMALLWORLD_JSON_REMOVEFR_TEXT;
					$xoopsTpl->assign('isuserafriend','yes');
				}		

		// Check status for follow
		$fl = $check->following_or($yourid, $id);
				if ($yourid == $id) {
					$following_text = _SMALLWORLD_JSON_FLYES_TEXT;
					$fl[0] == 0;
				}
				if ($fl[0] == 0) {
					$following_text = _SMALLWORLD_JSON_FLYES_TEXT;
				}
				if ($fl[0] == 1) {
					$following_text = _SMALLWORLD_JSON_FLNO_TEXT;
				}	

		// Get requests
		$getInvitations = $check->getRequests ($yourid);
		
		// Things to do with wall
		$Wall = new Wall_Updates();
		$myavatar 			=	$Wall->Gravatar($id); //profile owners data
		$myavatarlink		=	smallworld_getAvatarLink($id, $myavatar); //profile owners data
        $myavatar_size  = smallworld_getImageSize(80, 100, $myavatarlink);
        $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 35);
        
		$visitorAvatar		=	$Wall->Gravatar($yourid);
		$visitorAvatarlink	=	smallworld_getAvatarLink($yourid, $visitorAvatar);
        $visitorAvatar_size  = smallworld_getImageSize(80, 100, $visitorAvatarlink);
        $visitorAvatar_highwide = smallworld_imageResize($visitorAvatar_size[0], $visitorAvatar_size[1], 35);        
		
		// Follow array here
		$followers = $Wall->getFollowers($id);
		$updatesarray=$Wall->Updates(0,$id, $id);
        if (!empty($updatesarray)) {
            foreach ($updatesarray as $data) {
                
                // Is update's user a friend ?
                $frU = $check->friendcheck($id,$data['uid_fk']);
                
                $USW = array();
                $USW['posts'] = 0;
                $USW['comments'] = 0;
            
                if ($xoopsUser) {
                    
                    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) && $data['uid_fk'] == $id) {
                        $USW['posts'] = 1;
                        $USW['comments'] = 1;
                        $frU[0] == 2;
                    } else {
                        $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                    } 
                }
                
                if (!$xoopsUser) {
                    $USW = json_decode($dBase->GetSettings($data['uid_fk']), true);
                }

                    $wm['msg_id']			= 	$data['msg_id'];
                    $wm['orimessage']		=	($USW['posts'] == 1 || $profile >= 2) ? 
                                                    str_replace(array("\r", "\n"), '',Smallworld_stripWordsKeepUrl($data['message'])):'';
                    $wm['message']			=	($USW['posts'] == 1 || $profile >= 2) ? 
                                                    smallworld_tolink(htmlspecialchars_decode($data['message']), $data['uid_fk']):_SMALLWORLD_MESSAGE_PRIVSETPOSTS;
                    $wm['message']          =   Smallworld_cleanup($wm['message']);
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
                    $wm['sharelinkurl']		=	XOOPS_URL."/modules/smallworld/smallworldshare.php?ownerid=".$data['uid_fk'];
                    $wm['sharelinkurl']	   .=	"&updid=".$data['msg_id']."";
                    $wm['usernameTitle']	=	$wm['username']._SMALLWORLD_UPDATEONSITEMETA.$xoopsConfig['sitename'];
                        if ($USW['posts'] == 1 || $frU[0] == 2) {
                            $wm['sharelink'] = $Wall->GetSharing ($wm['msg_id'],$wm['priv']);
                        } else {
                            $wm['sharelink'] = $Wall->GetSharing ($wm['msg_id'],1);
                        }
                        
                        if ($USW['posts'] == 1 || $profile >= 2) {
                            $wm['sharediv']	= $Wall->GetSharingDiv ($wm['msg_id'],$wm['priv'], $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                        } else {
                            $wm['sharediv']	= $Wall->GetSharingDiv ($wm['msg_id'],1, $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                        }					
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
                            $wc['comment']			=		($USC['comments']  == 1 || $profile >= 2) ? 
                                                                smallworld_tolink(htmlspecialchars_decode($cdata['comment']),$cdata['uid_fk']):
                                                                _SMALLWORLD_MESSAGE_PRIVSETCOMMENTS;
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
                            $wc['vote_up']			= 		$Wall->countVotesCom ('com', 'up', $cdata['msg_id_fk'],$cdata['com_id']);
                            $wc['vote_down']		= 		$Wall->countVotesCom ('com', 'down', $cdata['msg_id_fk'],$cdata['com_id']);			
                            $xoopsTpl->append('comm', $wc);
                        }
                    }
            }
        } 
			
            // Create form for private settings
            $form = new SmallWorldForm;
            $usersettings = $form->usersettings ($yourid, $selected=null);
            $xoopsTpl->assign('usersetting', $usersettings);
		
            // Get usermenu to template
			$menu_startpage = "<a href='".XOOPS_URL."/modules/smallworld/publicindex.php'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/highrise.png'>"._SMALLWORLD_STARTPAGE."</a>";
            $menu_home = "<a href='".XOOPS_URL."/modules/smallworld/'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/house.png'>"._SMALLWORLD_HOME."</a>";
			$menu_profile = "<a href='".XOOPS_URL."/modules/smallworld/userprofile.php?username=".$Xusername."'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/user_silhouette.png'>"._SMALLWORLD_PROFILEINDEX."</a>";
			$menu_gallery = "<a href='".XOOPS_URL."/modules/smallworld/galleryshow.php?username=".$Xusername."'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/picture.png'>"._SMALLWORLD_GALLERY."</a>";
			$menu_friends = "<a href='".XOOPS_URL."/modules/smallworld/friends.php?username=".$Xusername."'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/group.png'>"._SMALLWORLD_FRIENDSPAGE."</a>";
			
            if (smallworld_XIMversion () > 102) {
                    $menu_xim_js = "javascript:xim_chatWith('".$id."','".$Xusername."')";
            } else {
                $menu_xim_js = "javascript:chatWith('".$id."','".$Xusername."')";
            }
			
            $menu_ximme = "<a href='javascript:void(0);' onClick=".$menu_xim_js."><img height='10px' width='10px' src='".XOOPS_URL."/modules/smallworld/images/messenger.png'>"._SMALLWORLD_XIMUSER.$Xusername."</a>";
			
			// Check for folder xim to add messenger user to menu items
			$hasxim = smallworld_checkForXim ();
			if ($hasxim == true) {
				$xoopsTpl->assign('sendxim','YES');
				if ($fr[0] == 2) {
					if ($yourid != $id) {
						$xoopsTpl->assign('menu_xim',$menu_ximme);
					}
				}
			}
			
			$birthday_today_text  = "<img width='20px' heigh='20px' src='images/bdayballoons_l.png'/>";
			$birthday_today_text .= " "._SMALLWORLD_BDAY_TODAY." ";
			$birthday_today_text .= "<img width='20px' heigh='20px' src='images/bdayballoons_r.png'/>";
			
			$xoopsTpl->assign('menu_startpage',$menu_startpage);
            $xoopsTpl->assign('menu_home',$menu_home);
			$xoopsTpl->assign('menu_profile',$menu_profile);
			$xoopsTpl->assign('menu_friends',$menu_friends);
			$xoopsTpl->assign('menu_gallery',$menu_gallery);
		
			$xoopsTpl->assign('check',$profile);
			$xoopsTpl->assign('friendID',$id);
			$xoopsTpl->assign('myUid',$yourid);
			$xoopsTpl->assign('friendship_text',$friendship_text);
			$xoopsTpl->assign('followfriend_text',$following_text);
			$xoopsTpl->assign('friendinvitations',$getInvitations);
			$xoopsTpl->assign('myavatar',$myavatar);
			$xoopsTpl->assign('myavatarlink',$myavatarlink);	
            $xoopsTpl->assign('myavatar_highwide',$visitorAvatar_highwide);
			$xoopsTpl->assign('visitoravatar',$visitorAvatar);
			$xoopsTpl->assign('visitoravatarlink',$visitorAvatarlink);
            $xoopsTpl->assign('visitoravatar_highwide',$visitorAvatar_highwide);
			$xoopsTpl->assign('myusername',$username);
			$xoopsTpl->assign('username',$Xusername);
			$xoopsTpl->assign('bdaynow',$birthday_today_text);
			$xoopsTpl->assign('isfollowing',$fl[0]);	
			$xoopsTpl->assign('flds',smallworld_GetModuleOption('smallworldusethesefields', $repmodule='smallworld'));
		}
        
        if ($profile < 2) {
           redirect_header(XOOPS_URL . "/modules/smallworld/index.php");
        } 
			//Check Language
			$lang = $xoopsConfig['language'];
			
			// GET various variables from language folder
			if ( file_exists(XOOPS_ROOT_PATH.'/modules/smallworld/language/js/'.$lang.'/jquery.countdown.js')) {
				$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/'.$lang.'/js/jquery.countdown.js');
			} else {
				$xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/english/js/jquery.countdown.js');
			}
	} else {
		redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
	  }
     
include(XOOPS_ROOT_PATH."/footer.php");
?>