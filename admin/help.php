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

$GLOBALS['xoopsTpl']->caching = 0;

xoops_cp_header();

// template assignments
$GLOBALS['xoopsTpl']->assign('lang_help', _AM_SMALLWORLD_HELP);

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
    'lang_hlp_otherhelp'        => _AM_SMALLWORLD_HELP_OTHERHELP
]);

$GLOBALS['xoopsTpl']->display($helper->path('templates/admin_help.tpl'));

$GLOBALS['xoTheme']->addStylesheet($helper->url('assets/css/SmallworldAdmin.css'));

xoops_cp_footer();
