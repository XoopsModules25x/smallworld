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

$moduleDirName = basename(dirname(__DIR__));

if (false !== ($moduleHelper = Xmf\Module\Helper::getHelper($moduleDirName))) {
} else {
    $moduleHelper = Xmf\Module\Helper::getHelper('system');
}


$pathIcon32    = \Xmf\Module\Admin::menuIconPath('');
//$pathModIcon32 = $moduleHelper->getModule()->getInfo('modicons32');

// Load language files
$moduleHelper->loadLanguage('admin');
$moduleHelper->loadLanguage('modinfo');
$moduleHelper->loadLanguage('main');

$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_INDEX,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png'
];

//$adminmenu[] = [
//    'title' => _MI_SMALLWORLD_ADMENU,
//    'link'  => 'admin/moduleinfo.php',
//    'icon'  => $pathIcon32 . '/update.png'
//];

$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_USERADMIN,
    'link'  => 'admin/useradmin.php',
    'icon'  => $pathIcon32 . '/administration.png'
];

$adminmenu[] = [
    'title' => _MI_SMALLWORLDMENU_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png'
];

