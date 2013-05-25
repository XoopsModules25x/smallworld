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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/img_upload.php $
* @Last committed:		$Revision: 11576 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-05-22 15:25:30 +0200 (on, 22 maj 2013) $
* @ID:					$Id: img_upload.php 11576 2013-05-22 13:25:30Z djculex $
**/
include_once("../../mainfile.php");
$xoopsOption['template_main'] = 'smallworld_userprofile_imgupload.html';
include XOOPS_ROOT_PATH.'/header.php';
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/arrays.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");

	if ($xoopsUser) {
		global $xoTheme;	
        $xoopsLogger->activated = false;        
        $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/bootstrap.min.css');
        $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/style.css');
        $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/bootstrap-responsive.min.css');
        $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/uploader/bootstrap-image-gallery.min.css');

        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/vendor/jquery.ui.widget.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/tmpl.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/load-image.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/canvas-to-blob.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/bootstrap.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/uploader/bootstrap-image-gallery.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.iframe-transport.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.fileupload.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.fileupload-fp.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.fileupload-ui.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/main.js');    
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
        //$xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/colorbox.css');	
		
        $id = $xoopsUser->getVar('uid');
		$check = new SmallWorldUser;
		$profile = $check->CheckIfProfile($id);
		if ($profile >= 2) {
		$xoopsTpl->assign('check',$profile);
		$item = new SmallWorldForm;
		$db = new SmallWorldDB;
		
		// ------------ DISPLAY IMAGES ------------ // 
		// ------------ IMAGE UPLOADER ------------ // 
		// Image upload form
		$upload = $item->uploadform ($id);
		} else {
		  redirect_header("register.php", 1); 
		}
	} else {
		redirect_header(XOOPS_URL . "/user.php", 1, _NOPERM);
	}
include(XOOPS_ROOT_PATH."/footer.php");
?>