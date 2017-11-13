<?php
/**
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
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @license      GNU GPL (http://www.gnu.org/licenses/gpl-2.0.html/)
 * @package      SmallWorld
 * @since        1.0
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 */

defined('XOOPS_ROOT_PATH') || die('Restricted access');
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';

/**
 * Class SmallworldCorePreload
 */
class SmallworldCorePreload extends XoopsPreloadItem
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
        global $xoTheme, $xoopsUser, $xoopsConfig;
        //Load language if not defined
        smallworld_isDefinedLanguage('_SMALLWORLD_SYSERROR', 'main.php');

        //$xoTheme->addScript("http://code.jquery.com/jquery-1.9.1.js");
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.min.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jqueryui.min.js');
        //$xoTheme->addScript("http://code.jquery.com/ui/1.10.2/jquery-ui.js");
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/base/jquery.ui.all.css');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');

        //Get variables
        smallworld_SetCoreScript();
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.colorbox.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validate.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.validation.functions.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.stepy.js');
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.elastic.source.js');
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/smallworld.css');

        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/jquery.countdown.js');

        if (file_exists(XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/js/jquery.ui.datepicker-language.js')) {
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/js/jquery.ui.datepicker-language.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/js/jquery.countdown.js');
        } else {
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/english/js/jquery.ui.datepicker-language.js');
            $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/language/english/js/jquery.countdown.js');
        }
        $xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/smallworld.js');
    }

    /**
     * @return bool
     */
//    public function isActive()
//    {
//        $moduleHandler = xoops_getHandler('module');
//        $module        = $moduleHandler->getByDirname('smallworld');
//
//        return ($module && $module->getVar('isactive')) ? true : false;
//    }
}
