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
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/preloads/core.php $
* @Last committed:		$Revision: 11723 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2013-06-19 12:48:22 -0400 (Wed, 19 Jun 2013) $
* @ID:					$Id: core.php 11723 2013-06-19 16:48:22Z djculex $
**/
defined('XOOPS_ROOT_PATH') or die('Restricted access');
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");

class SmallworldCorePreload extends XoopsPreloadItem
{
   
   function eventCoreHeaderAddmeta()
   {
        global $xoTheme,$xoopsUser,$xoopsConfig;         
        //Load language if not defined
        smallworld_isDefinedLanguage ('_SMALLWORLD_SYSERROR', 'main.php');
        
        //$xoTheme->addScript("http://code.jquery.com/jquery-1.9.1.js");
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.min.js');
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jqueryui.min.js');
        //$xoTheme->addScript("http://code.jquery.com/ui/1.10.2/jquery-ui.js");
        $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/base/jquery.ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');                        
    
        //Get variables
        smallworld_SetCoreScript ();
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.colorbox.js');
         $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.validate.js');
         $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.validation.functions.js');
         $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.stepy.js');
         $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.elastic.source.js');
		 $xoTheme->addStylesheet(XOOPS_URL.'/modules/smallworld/css/smallworld.css');     
        
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/jquery.countdown.js');
    
        if ( file_exists(XOOPS_ROOT_PATH.'/modules/smallworld/language/'.$xoopsConfig['language'].'/js/jquery.ui.datepicker-language.js')) {
            $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/'.$xoopsConfig['language'].'/js/jquery.ui.datepicker-language.js');
            $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/'.$xoopsConfig['language'].'/js/jquery.countdown.js');
        } else {
             $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/english/js/jquery.ui.datepicker-language.js');
             $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/language/english/js/jquery.countdown.js');
        }
        $xoTheme->addScript(XOOPS_URL.'/modules/smallworld/js/smallworld.js');
   }
	
	function isActive() {
		$module_handler =& xoops_getHandler('module');
		$module = $module_handler->getByDirname('smallworld');
		return ($module && $module->getVar('isactive')) ? true : false;
	}
}
?>