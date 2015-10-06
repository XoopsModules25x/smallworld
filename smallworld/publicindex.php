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
			    
			$updatesarray       =   $wall->updates(0, $pub);                      
			$wall->ParsePubArray ($updatesarray, $id);
			
			$xoopsTpl->assign('menu_home',$menu_home);
            $xoopsTpl->assign('menu_register',$menu_register);
			$xoopsTpl->assign('pagename','publicindex');			
            $xoopsTpl->assign('check',$profile); 
            $xoopsTpl->assign('access',$set['access']);
		           
           
		
include(XOOPS_ROOT_PATH."/footer.php");