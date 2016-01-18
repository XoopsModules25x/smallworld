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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/permalink.php $
* @Last committed:		$Revision: 12114 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-10-01 13:11:18 -0400 (Tue, 01 Oct 2013) $
* @ID:					$Id: permalink.php 12114 2013-10-01 17:11:18Z djculex $
**/
include_once("../../mainfile.php");
$xoopsOption['template_main'] = 'smallworld_permalink.html';
include_once(XOOPS_ROOT_PATH."/header.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
global $xoopsUser, $xoTheme, $xoopsLogger, $xoopsModule;

    $xoopsLogger->activated = false;
      /* error_reporting(E_ALL); */
    
    $module_handler =& xoops_gethandler('module');
        $module = $module_handler->getByDirname('smallworld');
        $config_handler =& xoops_gethandler('config');
        $moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
    
    $set = smallworld_checkPrivateOrPublic ();
    $check = new SmallWorldUser;
    $pub = smallworld_checkUserPubPostPerm ();
    $dBase = new SmallWorldDB;
    
        if (isset($_GET['updid']) AND isset($_GET['ownerid'])) {
            $updID = $_GET['updid'];
            $ownerID = $_GET['ownerid'];
        } else {
            redirect_header(XOOPS_URL . "/modules/smallworld/index.php", 5, _SMALLWORLD_UPDATEID_NOT_EXIST);
        }
        
        $id = ($xoopsUser) ? $xoopsUser->getVar('uid'):0;
        $user = ($xoopsUser) ? new XoopsUser($id) : 0;
        
        if ($xoopsUser) {
            if ( $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                $pub = $check->allUsers();
                $xoopsTpl->assign('isadminuser', 'YES');
                $xoopsTpl->assign('ownerofpage', $id);
            } else {
                $xoopsTpl->assign('isadminuser', 'NO');
            }
        }
        $username = ($xoopsUser) ? $user->getVar('uname'):"";
        
        $friend = $check->friendcheck($id,$ownerID);

            $profile = $check->checkIfProfile($id);
                $menu_startpage = "<a href='".XOOPS_URL."/modules/smallworld/publicindex.php'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/highrise.png'>"._SMALLWORLD_STARTPAGE."</a>";
                $menu_home = "<a href='".XOOPS_URL."/modules/smallworld/'><img id='menuimg' src='".XOOPS_URL."/modules/smallworld/images/house.png'>"._SMALLWORLD_HOME."</a>";
                
                // Things to do with wall
                $Wall = new Wall_Updates();
                
                
                // Follow array here
                $followers = Smallworld_array_flatten($Wall->getFollowers($id),0);

                $updatesarray=$Wall->UpdatesPermalink ($updID,$id, $ownerID);
                $Wall->ParsePubArray ($updatesarray, $id);
                
                $xoopsTpl->assign('menu_startpage',$menu_startpage);
                $xoopsTpl->assign('menu_home',$menu_home);
                $xoopsTpl->assign('myusername',$username);
                $xoopsTpl->assign('pagename','index');
                $xoopsTpl->assign('check',$profile);

 

include(XOOPS_ROOT_PATH."/footer.php");
