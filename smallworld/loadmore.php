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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/loadmore.php $
* @Last committed:		$Revision: 11843 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-07-18 19:29:48 +0200 (to, 18 jul 2013) $
* @ID:					$Id: loadmore.php 11843 2013-07-18 17:29:48Z djculex $
**/

include '../../mainfile.php';
include_once (XOOPS_ROOT_PATH.'/class/template.php');
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/arrays.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/publicWall.php");
$set = smallworld_checkPrivateOrPublic ();
$pub = smallworld_checkUserPubPostPerm ();
$hm = smallworld_GetModuleOption('msgtoshow');

$last  = mysql_real_escape_string($_POST['last']);
$page  = mysql_real_escape_string($_POST['page']);

global $xoopsUser, $xoTheme, $xoopsTpl,$xoopsLogger;
$xoopsLogger->activated = false;
 /* error_reporting(E_ALL); */
$tpl = new XoopsTpl();
$id = ($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
if ($id <= 0 || $page == 'publicindex' && $set['access'] = 1) {
    $Wall = new Public_Wall_Updates();
} else {
    $Wall = new Wall_Updates();
}
if (isset($_POST['userid'])) {
	$userid  = intval($_POST['userid']);
} else {
	$userid = ($xoopsUser) ? $xoopsUser->getVar('uid'):0;
}
$Xuser = ($id > 0) ? new XoopsUser($id) : 0;
$username = ($id > 0) ? $Xuser->getVar('uname'):'';
$dBase = new SmallWorldDB;
$check = new SmallWorldUser;
$profile = ($xoopsUser) ? $check->checkIfProfile($id) : 0;

	// 
    if ($id > 0) {
        if ( $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $pub = $check->allUsers();
            $tpl->assign('isadminuser', 'YES');
        }
	} else {
            $tpl->assign('isadminuser', 'NO');
            $pub = smallworld_checkUserPubPostPerm ();
    }
		$myavatar 		=	$Wall->Gravatar($id);
		$myavatarlink	=	smallworld_getAvatarLink($id, $myavatar);
        $myavatar_size  = smallworld_getImageSize(80, 100, $myavatarlink);
        $myavatar_highwide = smallworld_imageResize($myavatar_size[0], $myavatar_size[1], 35);    
    
    
    
    if ($id <= 0 && $set['access'] == 1 ) {
        //$pub = $check->allUsers();
        $followers = $pub;	
    } elseif ($id > 0 && $set['access'] == 1 && $page == 'publicindex') {
        //$pub = $check->allUsers();
        $followers = $pub;	
    }else {
        $followers = Smallworld_array_flatten($Wall->getFollowers($id),0);	
    }

    if ($page == 'index') {
        $updatesarray = ($id > 0) ? $Wall->Updates($_POST['last'], $id, $followers) : $Wall->Updates($_POST['last'], $followers);
    } elseif ($page == 'profile') {
        $updatesarray = ($id > 0) ? $Wall->Updates($_POST['last'], $userid, $userid) : $Wall->Updates($_POST['last'], $userid);
    } elseif ($page == 'publicindex') {
        $updatesarray = $Wall->Updates($_POST['last'], $followers);
    }

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
                $wm['avatar']			=	$Wall->Gravatar($data['uid_fk']);
                $wm['avatar_link']		=	smallworld_getAvatarLink ($data['uid_fk'], $wm['avatar']);
                $wm['avatar_size']      =   smallworld_getImageSize(80, 100, $wm['avatar_link']);
                $wm['avatar_highwide']  =   smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);                    
                $wm['vote_up']			= 	$Wall->countVotes ('msg', 'up', $data['msg_id']);
                $wm['vote_down']		= 	$Wall->countVotes ('msg', 'down', $data['msg_id']);
                $wm['sharelinkurl']		=	XOOPS_URL."/modules/smallworld/smallworldshare.php?ownerid=".$data['uid_fk'];
                $wm['sharelinkurl']	   .=	"&updid=".$data['msg_id']."";
                $wm['usernameTitle']	=	$wm['username']._SMALLWORLD_UPDATEONSITEMETA.$xoopsConfig['sitename'];
                    if ($USW['posts'] == 1 || $profile >= 2) {
                        $wm['sharelink'] = $Wall->GetSharing ($wm['msg_id'],$wm['priv']);
                    } else {
                        $wm['sharelink'] = $Wall->GetSharing ($wm['msg_id'],1);
                    }
                    
                    if ($USW['posts'] == 1 || $profile >= 2) {
                        $wm['sharediv']	= $Wall->GetSharingDiv ($wm['msg_id'],$wm['priv'], $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                    } else {
                        $wm['sharediv']	= $Wall->GetSharingDiv ($wm['msg_id'],1, $wm['sharelinkurl'],$wm['orimessage'],$wm['usernameTitle']);
                    }					
                $wm['sharelink']		=	$Wall->GetSharing ($wm['msg_id'],$wm['priv']);
                $wm['linkimage']        =   XOOPS_URL."/modules/smallworld/images/link.png";
                $wm['permalink']        =   XOOPS_URL."/modules/smallworld/permalink.php?ownerid=".$data['uid_fk']."&updid=".$data['msg_id'];
                $wm['commentsarray']	=	$Wall->Comments($data['msg_id']);	
                $tpl->append('walldata', $wm);
                
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
                        $tpl->append('comm', $wc);
                    }
                }
        }
    } 

$tpl->assign('sCountResp',count($updatesarray)); 

$tpl->assign('msgtoshow',$hm);	       
$tpl->assign('myusername',$username);		
$tpl->assign('pagename',$page);	
$tpl->assign('myavatar',$myavatar);
$tpl->assign('myavatarlink',$myavatarlink);
$tpl->assign('myavatar_highwide',$myavatar_highwide);

if ($id > 0) {
    $tpl->display(XOOPS_ROOT_PATH .'/modules/smallworld/templates/getmore.html');
} else {
    $tpl->display(XOOPS_ROOT_PATH .'/modules/smallworld/templates/getmorepublic.html');
}
?>
