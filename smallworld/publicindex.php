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
* @Repository path:		$HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/index.php $
* @Last committed:		$Revision: 9482 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2012-05-11 12:02:24 +0200 (fr, 11 maj 2012) $
* @ID:					$Id: index.php 9482 2012-05-11 10:02:24Z djculex $
**/

include_once("../../mainfile.php");
    $page = basename ($_SERVER['PHP_SELF'],".php");
    
    if ($xoopsUser && $page != 'publicindex') {
        $xoopsOption['template_main'] = 'smallworld_index.html';
    } else {
        $xoopsOption['template_main'] = 'smallworld_publicindex.html';
    }
include_once(XOOPS_ROOT_PATH."/header.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/publicWall.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
global $xoopsUser, $xoTheme,$xoopsConfig,$xoopsLogger;
    $xoopsLogger->activated = true;
     error_reporting(E_ALL);
    
    
	$set = smallworld_checkPrivateOrPublic ();
    $dBase = new SmallWorldDB;
    $check = new SmallWorldUser;
    
    $id = ($xoopsUser) ? $xoopsUser->getVar('uid'):0;
    $username = ($xoopsUser) ? $xoopsUser->getVar('uname'):'';
    $profile = ($xoopsUser) ? $check->checkIfProfile($id) : 0;
    
    $module_handler =& xoops_gethandler('module');
            $module = $module_handler->getByDirname('smallworld');
            $config_handler =& xoops_gethandler('config');
            $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));						   
    $pub = smallworld_checkUserPubPostPerm ();   
    $wall = new Public_Wall_Updates; 
    $updates = $wall->updates(0, $pub);
     
    if ($id > 0) {
        $xoopsTpl->assign('ownerofpage', $id);
        $xoopsTpl->assign('myusername',$username); 
    }
    
    if ($xoopsUser) {
        if ( $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $pub = $check->allUsers();
            $xoopsTpl->assign('isadminuser', 'YES');
        }
    } else {
            $xoopsTpl->assign('isadminuser', 'NO');
    }
    
    // Create form for private settings
        $form = new SmallWorldForm;
            $usersettings = $form->usersettings ($id, $selected=null);
            $xoopsTpl->assign('usersetting', $usersettings);
		
			$xuser = new SmallWorldProfile;

			$menu_home = "<a href='".XOOPS_URL."/modules/smallworld/'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/house.png'>"._SMALLWORLD_HOME."</a>";
            $menu_register = ($profile < 2) ? "<a href='".XOOPS_URL."/modules/smallworld/register.php'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/join.jpg'>"._MB_SYSTEM_RNOW."</a>":'';			
			
			$updatesarray=$wall->updates(0, $pub);
            
            $myavatar 		=	$wall->Gravatar($id);
			$myavatarlink 	=	smallworld_getAvatarLink($id, $myavatar);
            $myavatar_size  = smallworld_getImageSize(80, 100, $myavatarlink);
            $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 100);
            $user_img = "<img src='".smallworld_getAvatarLink($id, $myavatar)."' id='smallworld_user_img' ".$myavatar_highwide."/>";
            
            if (!empty($updatesarray)) {
                foreach ($updatesarray as $data) {
 
                    $USW = array();
                    $USW['posts'] = 0;
                    $USW['comments'] = 0;
                
                    if ($xoopsUser) {                      
                        if ($xoopsUser->isAdmin($xoopsModule->getVar('mid')) && $data['uid_fk'] == $id) {
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
                        $wm['orimessage']		=	($USW['posts'] == 1 || $profile >= 2) ? 
                                                        str_replace(array("\r", "\n"), '',Smallworld_stripWordsKeepUrl($data['message'])):'';
                        $wm['message']			=	($USW['posts'] == 1 || $profile >= 2) ? 
                                                        smallworld_tolink(htmlspecialchars_decode($data['message']), $data['uid_fk']):_SMALLWORLD_MESSAGE_PRIVSETPOSTS;
                        $wm['message']          =   Smallworld_cleanup($wm['message']);
                        $wm['created']			=	smallworld_time_stamp($data['created']);
                        $wm['username']			=	$data['username'];
                        $wm['uid_fk']			=	$data['uid_fk'];
                        $wm['priv']				=	$data['priv'];
                        $wm['avatar']			=	$wall->Gravatar($data['uid_fk']);
                        $wm['avatar_link']		=	smallworld_getAvatarLink ($data['uid_fk'], $wm['avatar']);
                        $wm['avatar_size']      =   smallworld_getImageSize(80, 100, $wm['avatar_link']);
                        $wm['avatar_highwide']  =   smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);                    
                        $wm['vote_up']			= 	$wall->countVotes ('msg', 'up', $data['msg_id']);
                        $wm['vote_down']		= 	$wall->countVotes ('msg', 'down', $data['msg_id']);
                        $wm['sharelinkurl']		=	XOOPS_URL."/modules/smallworld/smallworldshare.php?ownerid=".$data['uid_fk'];
                        $wm['sharelinkurl']	   .=	"&updid=".$data['msg_id']."";
                        $wm['usernameTitle']	=	$wm['username']._SMALLWORLD_UPDATEONSITEMETA.$xoopsConfig['sitename'];
                            if ($USW['posts'] == 1 || $profile >= 2) {
                                $wm['sharelink'] = $wall->GetSharing ($wm['msg_id'],$wm['priv']);
                            } else {
                                $wm['sharelink'] = $wall->GetSharing ($wm['msg_id'],1);
                            }
                            
                            if ($USW['posts'] == 1 || $profile >= 2) {
                                $wm['sharediv']	= $wall->GetSharingDiv ($wm['msg_id'],$wm['priv'], $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                            } else {
                                $wm['sharediv']	= $wall->GetSharingDiv ($wm['msg_id'],1, $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                            }		
                        $wm['linkimage']        =   XOOPS_URL."/modules/smallworld/images/link.png";
                        $wm['permalink']        =   XOOPS_URL."/modules/smallworld/permalink.php?ownerid=".$data['uid_fk']."&updid=".$data['msg_id'];
                        $wm['commentsarray']	=	$wall->Comments($data['msg_id']);	
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
                                $wc['myavatar']			=		$wall->Gravatar($id);
                                $wc['myavatar_link']	=		$myavatarlink;
                                $wc['avatar_size']      =       smallworld_getImageSize(80, 100, $wc['myavatar_link']);
                                $wc['avatar_highwide']  =       smallworld_imageResize($wc['avatar_size'][0], $wc['avatar_size'][1], 35);                             
                                $wc['cface']			=		$wall->Gravatar($cdata['uid_fk']);
                                $wc['avatar_link']		=		smallworld_getAvatarLink ($cdata['uid_fk'], $wc['cface']);
                                $wc['vote_up']			= 		$wall->countVotesCom ('com', 'up', $cdata['msg_id_fk'],$cdata['com_id']);
                                $wc['vote_down']		= 		$wall->countVotesCom ('com', 'down', $cdata['msg_id_fk'],$cdata['com_id']);			
                                $xoopsTpl->append('comm', $wc);
                            }
                        }
                }
            } 
            
            $myavatar 		=	$wall->Gravatar($id);
			$myavatarlink 	=	smallworld_getAvatarLink($id, $myavatar);
            $myavatar_size  = smallworld_getImageSize(80, 100, $myavatarlink);
            $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 100);
            $user_img = "<img src='".smallworld_getAvatarLink($id, $myavatar)."' id='smallworld_user_img' ".$myavatar_highwide."/>";
			

					
			$xoopsTpl->assign('menu_home',$menu_home);
            $xoopsTpl->assign('menu_register',$menu_register);
            $xoopsTpl->assign('avatar', $user_img);
			$xoopsTpl->assign('pagename','publicindex');			
            $xoopsTpl->assign('check',$profile); 
            $xoopsTpl->assign('access',$set['access']);
		           
           
		
include(XOOPS_ROOT_PATH."/footer.php");