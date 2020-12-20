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
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';

$moduleDirName = basename(__DIR__);

// ------------------- Informations ------------------- //
$modversion = [
    'version'             => 1.22,
    'module_status'       => 'Beta 3',
    'release_date'        => '2020/12/20',
    'name'                => _MI_SMALLWORLD_MODULE_NAME,
    'description'         => _MI_SMALLWORLD_MODULE_DESC,
    'official'            => 0,
    //1 indicates official XOOPS module supported by XOOPS Dev Team, 0 means 3rd party supported
    'author'              => 'Michael Albertsen',
    'nickname'            => 'Culex',
    'credits'             => 'XOOPS Development Team, Mariane, Mrsculex, Mamba, Rune, Zth, Cesag, Flipse, Dante, ZySpec',
    'author_mail'         => 'culex@culex.dk',
    'author_website_url'  => 'www.culex.dk',
    'author_website_name' => 'www.culex.dk',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html/',
    'help'                => 'page=help',
    // ------------------- Folders & Files -------------------
    'release_info'        => 'Changelog',
    'release_file'        => XOOPS_URL . "/modules/$moduleDirName/docs/changelog.txt",

    'manual'              => 'link to manual file',
    'manual_file'         => XOOPS_URL . "/modules/$moduleDirName/docs/install.txt",
    // images
    'image'               => 'assets/images/logoModule.png',
    'iconsmall'           => 'assets/images/iconsmall.png',
    'iconbig'             => 'assets/images/iconbig.png',
    'dirname'             => $moduleDirName,
    // Local path icons
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    //About
    'demo_site_url'       => 'www.culex.dk',
    'demo_site_name'      => 'www.culex.dk',
    'support_url'         => 'https://xoops.org/modules/newbb/viewforum.php?forum=28/',
    'support_name'        => 'Support Forum',
    'submit_bug'          => 'https://github.com/XoopsModules25x/' . $moduleDirName . '/issues',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    // ------------------- Min Requirements -------------------
    'min_php'             => '7.0',
    'min_xoops'           => '2.5.10',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '7.0'],
    // ------------------- Admin Menu -------------------
    'system_menu'         => 1,
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // ------------------- Main Menu -------------------
    'hasMain'             => 1,

    // ------------------- Install/Update -------------------
    //    'onInstall'           => 'include/oninstall.php',
    'onUpdate'            => 'include/onupdate.php',
    //  'onUninstall'         => 'include/onuninstall.php',
    // -------------------  PayPal ---------------------------
    'paypal'              => [
        'business'      => 'xoopsfoundation@gmail.com',
        'item_name'     => 'Donation : ' . _MI_SMALLWORLD_MODULE_NAME,
        'amount'        => 0,
        'currency_code' => 'USD',
    ],
    // ------------------- Search ---------------------------
    'hasSearch'           => 1,
    'search'              => [
        'file' => 'include/search.inc.php',
        'func' => 'smallworld_search',
    ],

    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
    'tables'              => [
        $moduleDirName . '_' . 'admin',
        $moduleDirName . '_' . 'comments',
        $moduleDirName . '_' . 'followers',
        $moduleDirName . '_' . 'friends',
        $moduleDirName . '_' . 'images',
        $moduleDirName . '_' . 'messages',
        $moduleDirName . '_' . 'user',
        $moduleDirName . '_' . 'vote',
        $moduleDirName . '_' . 'complaints',
        $moduleDirName . '_' . 'settings',
    ],
];

// ------------------- Help files ------------------- //
$modversion['helpsection'] = [
    ['name' => _MI_SMALLWORLD_OVERVIEW, 'link' => 'page=help'],
    ['name' => _MI_SMALLWORLD_DISCLAIMER, 'link' => 'page=disclaimer'],
    ['name' => _MI_SMALLWORLD_LICENSE, 'link' => 'page=license'],
    ['name' => _MI_SMALLWORLD_SUPPORT, 'link' => 'page=support'],
];

// Templates

$modversion['templates'] = [
    ['file' => 'smallworld_index.tpl', 'description' => ''],
    ['file' => 'smallworld_userprofile_template.tpl', 'description' => ''],
    ['file' => 'smallworld_userprofile_regtemplate.tpl', 'description' => ''],
    ['file' => 'smallworld_userprofile_edittemplate.tpl', 'description' => ''],
    ['file' => 'smallworld_userprofile_imgupload.tpl', 'description' => ''],
    ['file' => 'smallworld_galleryshow.tpl', 'description' => ''],
    ['file' => 'smallworld_images_edittemplate.tpl', 'description' => ''],
    ['file' => 'smallworld_friends_template.tpl', 'description' => ''],
    ['file' => 'smallworld_permalink.tpl', 'description' => ''],
    ['file' => 'smallworld_admin.tpl', 'description' => ''],
    ['file' => 'smallworld_share.tpl', 'description' => ''],
    ['file' => 'smallworld_publicindex.tpl', 'description' => ''],
];

// Blocks
$modversion['blocks'][] = [
    'file'        => 'smallworld_block.php',
    'name'        => 'Block for empty module',
    'description' => 'This is a Block for the empty module',
    'show_func'   => 'smallworld_block',
    'template'    => 'smallworld_block.tpl',
];
//Module Configs

$modversion['config'][] = [
    'name'        => 'msgtoshow',
    'title'       => '_MI_SMALLWORLD_MSGTOSHOW',
    'description' => '_MI_SMALLWORLD_MSGTOSHOW_DESC',
    'formtype'    => 'textbox',
    'valuetype'   => 'int',
    'default'     => 5,
];

$modversion['config'][] = [
    'name'        => 'validationstrength',
    'title'       => '_MI_SMALLWORLD_VALIDATIONSTRENGTH',
    'description' => '_MI_SMALLWORLD_VALIDATIONSTRENGTH_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'takeoveruserlinks',
    'title'       => '_MI_SMALLWORLD_TAKEOVERLINKS',
    'description' => '_MI_SMALLWORLD_TAKEOVERLINKS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'usersownpostscount',
    'title'       => '_MI_SMALLWORLD_USERSOWNMSGCOUNTS',
    'description' => '_MI_SMALLWORLD_USERSOWNMSGCOUNTS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

$modversion['config'][] = [
    'name'        => 'smallworldbookmarkavatar',
    'title'       => '_MI_SMALLWORLD_BOOKMARSAVATARS',
    'description' => '_MI_SMALLWORLD_BOOKMARKSAVATARS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'smallworldusemailnotis',
    'title'       => '_MI_SMALLWORLD_USEMAILNOTIFICATION',
    'description' => '_MI_SMALLWORLD_USEMAILNOTIFICATION_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];

$modversion['config'][] = [
    'name'        => 'smallworldusethesefields',
    'title'       => '_MI_SMALLWORLD_VIEWFIELDS',
    'description' => '_MI_SMALLWORLD_VIEWFIELDS_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'options'     => [
        '_MI_SMALLWORLD_REALNAME'           => 'realname',
        '_MI_SMALLWORLD_GENDER'             => 'gender',
        '_MI_SMALLWORLD_INTERESTEDIN'       => 'interestedin',
        '_MI_SMALLWORLD_RELATIONSHIPSTATUS' => 'relationshipstatus',
        '_MI_SMALLWORLD_PARTNER'            => 'partner',
        '_MI_SMALLWORLD_LOOKINGFOR'         => 'lookingfor',
        '_MI_SMALLWORLD_BIRTHDAY'           => 'birthday',
        '_MI_SMALLWORLD_BIRTHPLACE'         => 'birthplace',
        '_MI_SMALLWORLD_POLITICALVIEWS'     => 'politicalview',
        '_MI_SMALLWORLD_RELIGIOUSVIEWS'     => 'religiousview',
        '_MI_SMALLWORLD_EMAILS'             => 'emails',
        '_MI_SMALLWORLD_SCREENNAMES'        => 'screennames',
        '_MI_SMALLWORLD_MOBILE'             => 'mobile',
        '_MI_SMALLWORLD_LANDPHONE'          => 'landphone',
        '_MI_SMALLWORLD_STREETADRESS'       => 'streetadress',
        '_MI_SMALLWORLD_PRESENTCITY'        => 'presentcity',
        '_MI_SMALLWORLD_COUNTRY'            => 'country',
        '_MI_SMALLWORLD_WEBSITE'            => 'website',
        '_MI_SMALLWORLD_INTERESTS'          => 'interests',
        '_MI_SMALLWORLD_FAVOURITEMUSIC'     => 'favouritemusic',
        '_MI_SMALLWORLD_FAVOURITETVSHOWS'   => 'favouritetvshows',
        '_MI_SMALLWORLD_FAVOURITEMOVIES'    => 'favouritemovies',
        '_MI_SMALLWORLD_FAVOURITEBOOKS'     => 'favouritebooks',
        '_MI_SMALLWORLD_ABOUTME'            => 'aboutme',
        '_MI_SMALLWORLD_EDUCATION'          => 'education',
        '_MI_SMALLWORLD_EMPLOYMENT'         => 'employment',
    ],
    'default'     => [
        'realname',
        'gender',
        'interestedin',
        'relationshipstatus',
        'partner',
        'lookingfor',
        'birthday',
        'birthplace',
        'politicalview',
        'religiousview',
        'emails',
        'screennames',
        'mobile',
        'landphone',
        'streetadress',
        'presentcity',
        'country',
        'website',
        'interests',
        'favouritemusic',
        'favouritetvshows',
        'favouritemovies',
        'favouritebooks',
        'aboutme',
        'education',
        'employment',
    ],
];

// Mandatory fields

$modversion['config'][] = [
    'name'        => 'smallworldmandatoryfields',
    'title'       => '_MI_SMALLWORLD_VERIFYFIELDS',
    'description' => '_MI_SMALLWORLD_VERIFYFIELDS_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'options'     => [
        '_MI_SMALLWORLD_REALNAME'           => 'realname',
        '_MI_SMALLWORLD_GENDER'             => 'gender',
        '_MI_SMALLWORLD_INTERESTEDIN'       => 'interestedin',
        '_MI_SMALLWORLD_RELATIONSHIPSTATUS' => 'relationshipstatus',
        '_MI_SMALLWORLD_PARTNER'            => 'partner',
        '_MI_SMALLWORLD_LOOKINGFOR'         => 'lookingfor',
        '_MI_SMALLWORLD_BIRTHDAY'           => 'birthday',
        '_MI_SMALLWORLD_BIRTHPLACE'         => 'birthplace',
        '_MI_SMALLWORLD_POLITICALVIEWS'     => 'politicalview',
        '_MI_SMALLWORLD_RELIGIOUSVIEWS'     => 'religiousview',
        '_MI_SMALLWORLD_EMAILS'             => 'emails',
        '_MI_SMALLWORLD_SCREENNAMES'        => 'screennames',
        '_MI_SMALLWORLD_MOBILE'             => 'mobile',
        '_MI_SMALLWORLD_LANDPHONE'          => 'landphone',
        '_MI_SMALLWORLD_STREETADRESS'       => 'streetadress',
        '_MI_SMALLWORLD_PRESENTCITY'        => 'presentcity',
        '_MI_SMALLWORLD_COUNTRY'            => 'country',
        '_MI_SMALLWORLD_WEBSITE'            => 'website',
        '_MI_SMALLWORLD_INTERESTS'          => 'interests',
        '_MI_SMALLWORLD_FAVOURITEMUSIC'     => 'favouritemusic',
        '_MI_SMALLWORLD_FAVOURITETVSHOWS'   => 'favouritetvshows',
        '_MI_SMALLWORLD_FAVOURITEMOVIES'    => 'favouritemovies',
        '_MI_SMALLWORLD_FAVOURITEBOOKS'     => 'favouritebooks',
        '_MI_SMALLWORLD_ABOUTME'            => 'aboutme',
        '_MI_SMALLWORLD_EDUCATION'          => 'education',
        '_MI_SMALLWORLD_EMPLOYMENT'         => 'employment',
    ],
    'default'     => [
        'realname',
        'gender',
        'interestedin',
        'relationshipstatus',
        'lookingfor',
        'birthday',
        'birthplace',
        'emails',
        'screennames',
        'streetadress',
        'presentcity',
        'country',
    ],
];

// Module is private or public
$modversion['config'][] = [
    'name'        => 'smallworldprivorpub',
    'title'       => '_MI_SMALLWORLD_PRIVATEMODULE',
    'description' => '_MI_SMALLWORLD_PRIVATEMODULE_DESC',
    'formtype'    => 'hidden',
    'valuetype'   => 'int',
    'default'     => 1,
];

/**
 * Authorized groups to upload
 */
$obj                    = smallworld_xv_getGroupd();
$modversion['config'][] = [
    'name'        => 'smallworldshowPoPubPage',
    'title'       => '_MI_SHOWPUBLICPAGE',
    'description' => '_MI_SHOWPUBLICPAGE_DESC',
    'formtype'    => 'select_multi',
    'valuetype'   => 'array',
    'default'     => 0,
    'options'     => array_flip($obj),
];

$modversion['config'][] = [
    'name'        => 'smallworldUseGoogleMaps',
    'title'       => '_MI_SMALLWORLD_USEGOOGLEMAPS',
    'description' => '_MI_SMALLWORLD_USEGOOGLEMAPS_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 1,
];
