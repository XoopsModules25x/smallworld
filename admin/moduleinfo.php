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

use XoopsModules\Smallworld;

require_once __DIR__ . '/admin_header.php';
/**
 * Vars defined by inclusion of ./admin_header.php
 *
 * @var \XoopsModules\Smallworld\Admin $admin
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

xoops_cp_header();

$admin = new Smallworld\Admin();

// Smallworld version number & install date
$installversion = $admin->ModuleInstallVersion();
$installdate    = $admin->ModuleInstallDate();

//check current version of Smallworld, return desc,link,version if new available
$installCheck = $admin->doCheckUpdate();

// template assignments
$GLOBALS['xoopsTpl']->assign([
    'lang_moduleinfo'           => _AM_SMALLWORLD_MODULEINFO,
    'lang_installversion'       => _AM_SMALLWORLD_MODULEINSTALL,
    'lang_installversion_status'=> _AM_SMALLWORLD_UPDATE_STATUS,
    'lang_installdate'          => _AM_SMALLWORLD_INSTALLDATE,
    'installversion'            => $installversion,
    'installdate'               => $installdate,
    'installversion_status'     => $installCheck
]);
$GLOBALS['xoopsTpl']->display($helper->path('templates/admin_moduleinfo.tpl'));

$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/SmallworldAdmin.css'));
$GLOBALS['xoTheme']->addScript($helper->url('assets/js/adminsmallworld.js'));

xoops_cp_footer();
