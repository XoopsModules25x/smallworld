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

use Xmf\Request;
use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

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
$GLOBALS['xoopsTpl']->caching = 0;

xoops_cp_header();

if (Request::hasVar('xim_admin_message', 'POST')) {
    $_POST['xim_admin_message'] = '';
}

$admin = new Smallworld\Admin();

$ai  = [];
$ani = [];

// --------------- First tab in admin ---------------
// Find oldest message and apply to template

//$dateoffirstmessage = date('d-m-Y H:i:s', $admin->oldestMsg());
$dfm = $admin->oldestMsg();
$dateoffirstmessage = (Constants::NO_DATE == $dfm) ? _AM_SMALLWORLD_NONEYET : date(_SHORTDATESTRING, $dfm);

// Get days number
$totaldays = $admin->countDays();
// get average messages per day
$avgperday = $admin->AvgMsgDay($totaldays);
// Smallworld version number
$installversion = $admin->ModuleInstallVersion();
// Smallworld install date
$installdate = $admin->ModuleInstallDate();

//check current version of XIM, return desc,link,version if new available
$installCheck = $admin->doCheckUpdate();

// Count members using XIM
$sumallusers = $swUserHandler->getCount();
//$sumallusers = $admin->TotalUsers();

// Find list of most active users (total)
$GLOBALS['xoopsTpl']->assign('topuser', $admin->mostactiveusers_allround());

// Find list of most active users (24 hours)
$GLOBALS['xoopsTpl']->assign('topusertoday', $admin->mostactiveusers_today());

// Find list of best rated users overall
$GLOBALS['xoopsTpl']->assign('topratedusers', $admin->topratedusers('up'));

// Find list of worst rated users overall
$GLOBALS['xoopsTpl']->assign('bottomratedusers', $admin->topratedusers('down'));

$allusers_inspect = $admin->getAllUsers('yes');
if (!empty($allusers_inspect)) {
    foreach ($allusers_inspect as $data) {
        $ai['id']                    = $data['id'];
        $ai['userid']                = $data['userid'];
        $ai['username']              = $data['username'];
        $ai['realname']              = $data['realname'];
        $ai['userimage']             = smallworld_getAvatarLink($data['userid'], $data['userimage']);
        $ai['avatar_size']           = smallworld_getImageSize(80, 100, $ai['userimage']);
        $ai['avatar_highwide']       = smallworld_imageResize($ai['avatar_size'][0], $ai['avatar_size'][1], 50);
        $ai['ip']                    = $data['ip'];
        $ai['complaint']             = $data['complaint'];
        $ai['inspect_start']         = $data['inspect_start'];
        $ai['inspect_stop']          = $data['inspect_stop'];
        $ai['userinspect_timetotal'] = ($data['inspect_start'] + $data['inspect_stop']) - time();
        $GLOBALS['xoopsTpl']->append('allusersinspect', $ai);
    }
}
$GLOBALS['xoopsTpl']->assign('allusersinspectcounter', count($ai));

$allusers_noinspect = $admin->getAllUsers('no');
if (!empty($allusers_noinspect)) {
    foreach ($allusers_noinspect as $data) {
        $ani['id']                    = $data['id'];
        $ani['userid']                = $data['userid'];
        $ani['username']              = $data['username'];
        $ani['realname']              = $data['realname'];
        $ani['userimage']             = smallworld_getAvatarLink($data['userid'], $data['userimage']);
        $ani['avatar_size']           = smallworld_getImageSize(80, 100, $ani['userimage']);
        $ani['avatar_highwide']       = smallworld_imageResize($ani['avatar_size'][0], $ani['avatar_size'][1], 50);
        $ani['ip']                    = $data['ip'];
        $ani['complaint']             = $data['complaint'];
        $ani['inspect_start']         = '';
        $ani['inspect_stop']          = '';
        $ani['userinspect_timetotal'] = '';
        $GLOBALS['xoopsTpl']->append('allusersnoinspect', $ani);
    }
}
$GLOBALS['xoopsTpl']->assign('allusersnoinspectcounter', count($ani));
// ---------------- end of tabs ---------------- //

// template assignments
// tab titles
$GLOBALS['xoopsTpl']->assign([
    'lang_statistics'            => _AM_SMALLWORLD_STATISTICS_TITLE,
    'lang_moduleinfo'            => _AM_SMALLWORLD_MODULEINFO,
    'lang_userstats'             => _AM_SMALLWORLD_USERSTATS,
    'lang_installversion'        => _AM_SMALLWORLD_MODULEINSTALL,
    'lang_installversion_status' => _AM_SMALLWORLD_UPDATE_STATUS,
    'lang_installdate'           => _AM_SMALLWORLD_INSTALLDATE,
    'lang_dateoffirstmessage'    => _AM_SMALLWORLD_DATEOFFIRSTMESSAGE,
    'lang_totalusers'            => _AM_SMALLWORLD_TOTALUSERS,
    'lang_averagemsgperday'      => _AM_SMALLWORLD_AVERAGEMSGPERDAY,
    'lang_topchatters'           => _AM_SMALLWORLD_TOPCHATTERS,
    'lang_topchatterstoday'      => _AM_SMALLWORLD_TOPCHATTERS_TODAY,
    'lang_toprated'              => _AM_SMALLWORLD_TOPRATEDUSERS,
    'lang_bottomrated'           => _AM_SMALLWORLD_BOTTOMRATEDUSERS,
    'lang_useradmin'             => _AM_SMALLWORLD_USERADMIN_TITLE,
    'lang_help'                  => _AM_SMALLWORLD_HELP,
    'lang_prefs'                 => _MI_SYSTEM_ADMENU6,
    'lang_prefslink'             => "<a href='../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $helper->getModule()->mid() . "'>" . _MI_SYSTEM_ADMENU6 . '</a>'
]);

// help file from admin
$GLOBALS['xoopsTpl']->assign([
    'lang_hlp_about'            => _AM_SMALLWORLD_HELP_ABOUT,
    'lang_hlp_preface'          => _AM_SMALLWORLD_HELP_PREFACE,
    'lang_hlp_requirements_t'   => _AM_SMALLWORLD_HELP_HEADER_REQUIREMENTS,
    'lang_hlp_requirements'     => _AM_SMALLWORLD_HELP_REQUIREMENTS,
    'lang_hlp_recommended_t'    => _AM_SMALLWORLD_HELP_HEADER_RECOMMENDED,
    'lang_hlp_recommended'      => _AM_SMALLWORLD_HELP_RECOMMENDED,
    'lang_hlp_installation_t'   => _AM_SMALLWORLD_HELP_HEADER_INSTALLATION,
    'lang_hlp_firsttime'        => _AM_SMALLWORLD_HELP_FIRSTTIMEINSTALL,
    'lang_hlp_hostedplatform_t' => _AM_SMALLWORLD_HELP_HEADER_HOSTED_PLATFORM,
    'lang_hlp_hostedplatform'   => _AM_SMALLWORLD_HELP_HOSTED_PLATFORM,
    'lang_hlp_upgrading_t'      => _AM_SMALLWORLD_HELP_HEADER_UPGRADING,
    'lang_hlp_upgrading'        => _AM_SMALLWORLD_HELP_UPGRADING,
    'lang_hlp_faq_t'            => _AM_SMALLWORLD_HELP_HEADER_FAQ,
    'lang_hlp_commen1_t'        => _AM_SMALLWORLD_HELP_HEADER_COMMENPROBLEMS1,
    'lang_hlp_commen1'          => _AM_SMALLWORLD_HELP_COMMENPROBLEMS1,
    'lang_hlp_contacts_t'       => _AM_SMALLWORLD_HELP_HEADER_CONTACTS,
    'lang_hlp_otherhelp'        => _AM_SMALLWORLD_HELP_OTHERHELP,
    'installversion'            => $installversion,
    'installdate'               => $installdate,
    'installversion_status'     => $installCheck,
    'dateoffirstmessage'        => $dateoffirstmessage,
    'totalusers'                => $sumallusers,
    'averagemsgperday'          => $avgperday
]);
$GLOBALS['xoopsTpl']->display('db:smallworld_admin.tpl');

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
if (file_exists($helper->path('language/' . $lang . '/js/jquery.ui.datepicker-language.js'))) {
    $GLOBALS['xoTheme']->addScript($helper->url('language/' . $lang . '/js/jquery.ui.datepicker-language.js'));
    $GLOBALS['xoTheme']->addScript($helper->url('language/' . $lang . '/js/jquery.countdown.js'));
} else {
    $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/jquery.ui.datepicker-language.js'));
    $GLOBALS['xoTheme']->addScript($helper->url('language/english/js/jquery.countdown.js'));
}
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/adminsmallworld.js'));

xoops_cp_footer();
