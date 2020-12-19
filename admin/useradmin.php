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
 * @package      \XoopsModules\SmallWorld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

require_once __DIR__ . '/admin_header.php';
/**
 * Vars defined by inclusion of ./admin_header.php
 *
 * @var \XoopsModules\Smallworld\Admin $admin
 * @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler
 * @var \XoopsModules\Smallworld\DoSync $d
 * @var \XoopsModules\Smallworld\User $check
 * @var \XoopsModules\Smallworld\SwDatabase $swDB
 * @var \XoopsModules\Smallworld\WallUpdates $wall
 * @var \Xmf\Module\Admin $adminObject
 * @var \XoopsModules\Smallworld\Helper $helper
 * @var string $moduleDirName
 * @var string $moduleDirNameUpper
 */
require_once $helper->path('include/functions.php');
require_once XOOPS_ROOT_PATH . '/class/template.php';

$GLOBALS['xoopsTpl']->caching = false;

xoops_cp_header();

$ai    = [];
$ani   = [];

/** @var Xmf\Module\Admin $adminObject */
$adminObject->displayNavigation(basename(__FILE__));

$allusers_inspect = $admin->getAllUsers('yes');
foreach ($allusers_inspect as $data) {
    $ai['id']                    = $data['id'];
    $ai['userid']                = $data['userid'];
    $ai['username']              = $data['username'];
    $ai['realname']              = $data['realname'];
    $ai['userimage']             = $swUserHandler->getAvatarLink($data['userid'], $data['userimage']);
    $ai['avatar_size']           = smallworld_getImageSize(80, 100, $ai['userimage']);
    $ai['avatar_highwide']       = smallworld_imageResize($ai['avatar_size'][0], $ai['avatar_size'][1], 50);
    $ai['ip']                    = $data['ip'];
    $ai['complaint']             = $data['complaint'];
    $ai['inspect_start']         = $data['inspect_start'];
    $ai['inspect_stop']          = $data['inspect_stop'];
    $ai['userinspect_timetotal'] = ($data['inspect_start'] + $data['inspect_stop']) - time();
    $GLOBALS['xoopsTpl']->append('allusersinspect', $ai);
}
$GLOBALS['xoopsTpl']->assign('allusersinspectcounter', count($ai));


$allusers_noinspect = $admin->getAllUsers('no');
foreach ($allusers_noinspect as $data) {
    $ani['id']                    = $data['id'];
    $ani['userid']                = $data['userid'];
    $ani['username']              = $data['username'];
    $ani['realname']              = $data['realname'];
    $ani['userimage']             = $swUserHandler->getAvatarLink($data['userid'], $data['userimage']);
    $ani['avatar_size']           = smallworld_getImageSize(80, 100, $ani['userimage']);
    $ani['avatar_highwide']       = smallworld_imageResize($ani['avatar_size'][0], $ani['avatar_size'][1], 50);
    $ani['ip']                    = $data['ip'];
    $ani['complaint']             = $data['complaint'];
    $ani['inspect_start']         = '';
    $ani['inspect_stop']          = '';
    $ani['userinspect_timetotal'] = '';
    $GLOBALS['xoopsTpl']->append('allusersnoinspect', $ani);
}
$GLOBALS['xoopsTpl']->assign('allusersnoinspectcounter', count($ani));
// ---------------- end of tabs ---------------- //

// template assignments
// tab titles
$GLOBALS['xoopsTpl']->assign('lang_useradmin', _AM_SMALLWORLD_USERADMIN_TITLE);

// help file from admin
$GLOBALS['xoopsTpl']->display($helper->path('templates/admin_useradmin.tpl'));

//Check Language
$lang = $GLOBALS['xoopsConfig']['language'];
// GET various variables from language folder
if (file_exists($helper->path('language/' . $lang . '/js/variables.js'))) {
    $GLOBALS['xoTheme']->addScript($helper->url('language/' . $lang . '/js/variables.js'));
} else {
    $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/variables.js'));
}

$adminscript = <<<SCRIPT
        var smallworld_url="XOOPS_URL/modules/smallworld/";
        //var $ = jQuery();
SCRIPT;
$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/SmallworldAdmin.css'));
$GLOBALS['xoTheme']->addScript(XOOPS_URL . '/browse.php?Frameworks/jquery/jquery.js');
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery-ui-1.8.11.custom.js'));
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/smallworld_tabs.js'));
$GLOBALS['xoTheme']->addScript('', '', $adminscript);
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.form.js'));
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/jquery.countdown.js'));
if (file_exists($helper->path('language/assets/' . $lang . '/js/jquery.ui.datepicker-language.js'))) {
    $GLOBALS['xoTheme']->addScript($helper->url('language/' . $lang . '/js/jquery.ui.datepicker-language.js'));
    $GLOBALS['xoTheme']->addScript($helper->url('language/' . $lang . '/js/jquery.countdown.js'));
} else {
    $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/jquery.ui.datepicker-language.js'));
    $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/jquery.countdown.js'));
}
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/adminsmallworld.js'));

require_once __DIR__ . '/admin_footer.php';
