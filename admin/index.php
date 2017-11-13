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

//require_once dirname(dirname(dirname( __DIR__))) . '/include/cp_header.php';
require_once __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';

xoops_cp_header();
global $xoTheme;
$xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/SmallworldAdmin.css');
$xoTheme->addScript(XOOPS_URL . '/modules/smallworld/assets/js/adminsmallworld.js');

$adminObject = \Xmf\Module\Admin::getInstance();

$configurator = include __DIR__ . '/../include/config.php';
foreach (array_keys($configurator->uploadFolders) as $i) {
    $utility::createFolder($configurator->uploadFolders[$i]);
    $adminObject->addConfigBoxLine($configurator->uploadFolders[$i], 'folder');
}

//$admin = new SmallworldAdmin();
//$d = new smallworld\SmallWorldDoSync;
$d->checkOrphans();

// Find oldest message and apply to template
$dfm = $admin->oldestMsg();
if (0 == $dfm) {
    $dfm = _AM_SMALLWORLD_NONEYET;
} else {
    $dfm = date(_SHORTDATESTRING, $admin->oldestMsg());
}
$dateoffirstmessage = $dfm;

// Get days number
$totaldays = $admin->CountDays();
// get average messages per day
$avgperday = $admin->AvgMsgDay($totaldays);
// Smallworld version number
$installversion = $admin->ModuleInstallVersion();
// Smallworld install date
$installdate = $admin->ModuleInstallDate();
//check current version of Smallworld, return desc,link,version if new available
$installCheck = $admin->doCheckUpdate();
// Count members using Smallworld
$sumallusers = $admin->TotalUsers();
// Find list of most active users (total)
$maAllround = $admin->mostactiveusers_allround();
$ma_cnt = 0;
if (!empty($maAllround)) {
    $count = count($maAllround['cnt']);
} else {
    $count = 0;
}
$mat_cnt = 0;
if (0 != $count) {
    $ma_cnt = 1;
    $ma = "<table class='smallworldadmin'><tr>";
    $ma .= '<td><b>' . _AM_SMALLWORLD_STATS_POS . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_IMG . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_AMOUNT . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_NAME . '</b></td></tr>';
    $i = 1;
    while ($i <= count($maAllround['cnt'])) {
        $ma .= vsprintf('<tr><td>%s</td>', [$maAllround['counter'][$i]]);
        $ma .= vsprintf('<td>%s</td>', [$maAllround['img'][$i]]);
        $ma .= vsprintf('<td>%s</td>', [$maAllround['cnt'][$i]]);
        $ma .= vsprintf('<td>%s</td></tr>', [$maAllround['from'][$i]]);
        ++$i;
    }
    $ma .= '</tr></table>';
} else {
    $maAllround = 0;
}
// Find list of most active users (24 hours)
$maToday = $admin->mostactiveusers_today();
if (!empty($maToday)) {
    $count = count($maToday['cnt']);
} else {
    $count = 0;
}
$mat_cnt = 0;
if (0 != $count) {
    $mat_cnt = 1;
    $mat = "<table class='smallworldadmin'><tr>";
    $mat .= '<td><b>' . _AM_SMALLWORLD_STATS_POS . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_IMG . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_AMOUNT . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_NAME . '</b></td></tr>';
    $i = 1;
    while ($i <= $count) {
        $mat .= vsprintf('<tr><td>%s</td>', [$maToday['counter'][$i]]);
        $mat .= vsprintf('<td>%s</td>', [$maToday['img'][$i]]);
        $mat .= vsprintf('<td>%s</td>', [$maToday['cnt'][$i]]);
        $mat .= vsprintf('<td>%s</td></tr>', [$maToday['from'][$i]]);
        ++$i;
    }
    $mat .= '</tr></table>';
} else {
    $mat = 0;
}
// FInd list of best rated users overall
$topusers = $admin->topratedusers('up');
if (!empty($topusers)) {
    $count = count($topusers['cnt']);
} else {
    $count = 0;
}
$top_cnt = 0;
if (0 != $count) {
    $top_cnt = 1;
    $top = "<table class='smallworldadmin'><tr>";
    $top .= '<td><b>' . _AM_SMALLWORLD_STATS_POS . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_IMG . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_AMOUNT . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_NAME . '</b></td></tr>';
    $i = 1;
    while ($i <= $count) {
        $top .= vsprintf('<tr><td>%s</td>', [$topusers['counter'][$i]]);
        $top .= vsprintf('<td>%s</td>', [$topusers['img'][$i]]);
        $top .= vsprintf('<td>%s</td>', [$topusers['cnt'][$i]]);
        $top .= vsprintf('<td>%s</td></tr>', [$topusers['user'][$i]]);
        ++$i;
    }
    $top .= '</tr></table>';
} else {
    $top = 0;
}

// FInd list of worst rated users overall
$lowusers = $admin->topratedusers('down');
$low_cnt = 0;
if (!empty($lowusers)) {
    $count = count($lowusers['cnt']);
} else {
    $count = 0;
}
if (0 != $count) {
    $low_cnt = 1;
    $low = "<table class='smallworldadmin'><tr>";
    $low .= '<td><b>' . _AM_SMALLWORLD_STATS_POS . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_IMG . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_AMOUNT . '</b></td><td><b>' . _AM_SMALLWORLD_STATS_NAME . '</b></td></tr>';
    $i = 1;
    while ($i <= $count) {
        $low .= vsprintf('<tr><td>%s</td>', [$lowusers['counter'][$i]]);
        $low .= vsprintf('<td>%s</td>', [$lowusers['img'][$i]]);
        $low .= vsprintf('<td>%s</td>', [$lowusers['cnt'][$i]]);
        $low .= vsprintf('<td>%s</td></tr>', [$lowusers['user'][$i]]);
        ++$i;
    }
    $low .= '</tr></table>';
} else {
    $low = 0;
}

//-----------------------

// template assignments
$xoopsTpl->assign('lang_moduleinfo', _AM_SMALLWORLD_MODULEINFO);
$xoopsTpl->assign('lang_installversion', _AM_SMALLWORLD_MODULEINSTALL);
$xoopsTpl->assign('lang_installversion_status', _AM_SMALLWORLD_UPDATE_STATUS);
$xoopsTpl->assign('lang_installdate', _AM_SMALLWORLD_INSTALLDATE);

//$xoopsTpl->assign('installversion', $installversion);
//$xoopsTpl->assign('installdate', $installdate);
//$xoopsTpl->assign('installversion_status',$installCheck);
//$xoopsTpl->display(XOOPS_ROOT_PATH .'/modules/smallworld/templates/admin_moduleinfo.html');

//-----------------------

$adminObject->addInfoBox(_AM_SMALLWORLD_MODULEINFO);
$adminObject->addInfoBoxLine(sprintf( "<class='smallworldadmin'>" . _AM_SMALLWORLD_MODULEINSTALL . ' : %s</br>', $installversion),'', 'Green', 'default');
$adminObject->addInfoBoxLine(sprintf( "<class='smallworldadmin'>" . _AM_SMALLWORLD_INSTALLDATE . ': %s', $installdate),'', 'Green', 'default');
$adminObject->addInfoBoxLine(sprintf( "<class='smallworldadmin'>" . '%s', $installCheck),'', 'Green', 'default');

$adminObject->addInfoBox(_AM_SMALLWORLD_USERSTATS);
$adminObject->addInfoBoxLine(sprintf( "<class='smallworldadmin'>" . _AM_SMALLWORLD_DATEOFFIRSTMESSAGE . ' : %s</br>', $dateoffirstmessage),'', 'Green', 'default');
$adminObject->addInfoBoxLine(sprintf( "<class='smallworldadmin'>" . _AM_SMALLWORLD_TOTALUSERS . ' : %s</br>', $sumallusers),'', 'Red', 'default');

if ($avgperday > 0) {
    $adminObject->addInfoBoxLine(sprintf( "<class='smallworldadmin'>" . _AM_SMALLWORLD_AVERAGEMSGPERDAY . ' : %s</br>', $avgperday),'', 'Red', 'default');
}

if (0 != $mat_cnt) {
    $adminObject->addInfoBoxLine(sprintf( "<p class='smallworldadmin'>" . _AM_SMALLWORLD_TOPCHATTERS_TODAY . ' : %s</p>', $mat),'', 'Red', 'default');
}
if (0 != $ma_cnt) {
    $adminObject->addInfoBoxLine(sprintf( "<p class='smallworldadmin'>" . _AM_SMALLWORLD_TOPCHATTERS . ' : %s</p>', $ma),'', 'Red', 'default');
}
if (0 != $top_cnt) {
    $adminObject->addInfoBoxLine(sprintf( "<p class='smallworldadmin'>" . _AM_SMALLWORLD_TOPRATEDUSERS . ' : %s</p>', $top),'', 'Red', 'default');
}
if (0 != $low_cnt) {
    $adminObject->addInfoBoxLine(sprintf( "<p class='smallworldadmin'>" . _AM_SMALLWORLD_BOTTOMRATEDUSERS . ' : %s</p>', $low),'', 'Red', 'default');
}

echo $adminObject->displayNavigation('index.php');
echo $adminObject->displayIndex();

echo $utility::getServerStats();

require_once __DIR__ . '/admin_footer.php';
