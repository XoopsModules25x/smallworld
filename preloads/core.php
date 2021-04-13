<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * SmallWorld
 *
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';

/**
 * Class SmallworldCorePreload
 */
class SmallworldCorePreload extends \XoopsPreloadItem
{
    // to add PSR-4 autoloader
    /**
     * @param $args
     */
    public static function eventCoreIncludeCommonEnd($args)
    {
        include __DIR__ . '/autoloader.php';
    }

    public static function eventCoreHeaderAddmeta()
    {
        //Load language if not defined
        smallworld_isDefinedLanguage('_SMALLWORLD_SYSERROR', 'main.php');
		
		// Include jquery new or framework ?
		$jq  = "if(jQuery.fn.jquery.split('.')" . "\n";
		$jq .= ".map(function(i){return('0'+i).slice(-2)})" . "\n";
		$jq .= "		.join('.') > '01.08.03')" . "\n";
		$jq .= "	{" . "\n";
		$jq .= "		console.log('yes! Version is ' + jQuery.fn.jquery);" . "\n";
		$jq .= "	}" . "\n";
		$jq .= "	else" . "\n";
		$jq .= "	{" . "\n";
		$jq .= "		console.log('no! Version is ' + jQuery.fn.jquery);" . "\n";
		$jq .= "		var script = document.createElement('script');" . "\n";
		$jq .= "		script.src = 'https://code.jquery.com/jquery-latest.min.js';" . "\n";
		$jq .= "		script.type = 'text/javascript';" . "\n";
		$jq .= "		document.getElementsByTagName('head')[0].appendChild(script);" . "\n";
		$jq .= "	};";
		//$GLOBALS['xoTheme']->addScript(null, array( 'type' => 'text/javascript' ), $jq, 'CheckJquery');
		
        //$GLOBALS['xoTheme']->addScript("http://code.jquery.com/jquery-1.9.1.js");
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.min.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jqueryui.min.js');
        //$GLOBALS['xoTheme']->addScript("http://code.jquery.com/ui/1.10.2/jquery-ui.js");
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/base/jquery.ui.all.css');
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');

        //Get variables
        smallworld_SetCoreScript();
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validate.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validation.functions.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.stepy.js');
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
        $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');

        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.countdown.js');

        if (file_exists(XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $GLOBALS['xoopsConfig']['language'] . '/js/jquery.ui.datepicker-language.js')) {
            $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/language/' . $GLOBALS['xoopsConfig']['language'] . '/js/jquery.ui.datepicker-language.js');
            $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/language/' . $GLOBALS['xoopsConfig']['language'] . '/js/jquery.countdown.js');
        } else {
            $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/language/english/js/jquery.ui.datepicker-language.js');
            $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/language/english/js/jquery.countdown.js');
        }
        $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/smallworld.js');
		//$GLOBALS['xoTheme']->addScript(XOOPS_URL . '/modules/smallworld/assets/js/domaps.js');
    }
}
