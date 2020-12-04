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

$moduleDirName = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

if (false === ($helper = Xmf\Module\Helper::getHelper($moduleDirName))) {
    $helper = Xmf\Module\Helper::getHelper('system');
}

$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');
$helper->loadLanguage('common');
$helper->loadLanguage('feedback');


$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_INDEX,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

//$adminmenu[] = [
//    'title' => _MI_SMALLWORLD_ADMENU,
//    'link'  => 'admin/moduleinfo.php',
//    'icon'  => $pathIcon32 . '/update.png'
//];

$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_USERADMIN,
    'link'  => 'admin/useradmin.php',
    'icon'  => $pathIcon32 . '/administration.png',
];

//Feedback
$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_FEEDBACK,
    'link'  => 'admin/feedback.php',
    'icon'  => $pathIcon32 . '/mail_foward.png',
];

$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
