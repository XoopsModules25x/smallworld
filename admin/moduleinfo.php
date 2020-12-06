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

require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../../../include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';

require_once XOOPS_ROOT_PATH . '/class/template.php';
if (!isset($xoopsTpl)) {
    $xoopsTpl = new XoopsTpl();
}
$xoopsTpl->caching = 0;

xoops_cp_header();

$admin = new SmallworldAdmin();
$tpl = new XoopsTpl();

// Smallworld version number
$installversion = $admin->ModuleInstallVersion();
// Smallworld install date
$installdate = $admin->ModuleInstallDate();

//check current version of Smallworld, return desc,link,version if new available
$installCheck = $admin->doCheckUpdate();

// template assignments
$xoopsTpl->assign('lang_moduleinfo', _AM_SMALLWORLD_MODULEINFO);
$xoopsTpl->assign('lang_installversion', _AM_SMALLWORLD_MODULEINSTALL);
$xoopsTpl->assign('lang_installversion_status', _AM_SMALLWORLD_UPDATE_STATUS);
$xoopsTpl->assign('lang_installdate', _AM_SMALLWORLD_INSTALLDATE);

$xoopsTpl->assign('installversion', $installversion);
$xoopsTpl->assign('installdate', $installdate);
$xoopsTpl->assign('installversion_status', $installCheck);
$xoopsTpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/admin_moduleinfo.html');

global $xoTheme;
$xoTheme->addStylesheet('modules/smallworld/assets/css/SmallworldAdmin.css');
$xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/adminsmallworld.js');

xoops_cp_footer();
