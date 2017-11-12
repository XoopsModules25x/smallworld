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

$dirname = basename(dirname(__DIR__));
$moduleHandler = xoops_getHandler('module');
$module = $moduleHandler->getByDirname($dirname);
$pathIcon32 = $module->getInfo('icons32');

$i = 1;
$adminmenu[$i]['title'] = _MI_SMALLWORLDMENU_INDEX;
$adminmenu[$i]['link'] = 'admin/index.php';
$adminmenu[$i]['icon'] = $pathIcon32 . '/home.png';

//++$i;
//$adminmenu[$i]['title'] = _MI_SMALLWORLD_ADMENU;
//$adminmenu[$i]['link'] = 'admin/moduleinfo.php';
//$adminmenu[$i]['icon'] = $pathIcon32."/update.png";

++$i;
$adminmenu[$i]['title'] = _MI_SMALLWORLDMENU_USERADMIN;
$adminmenu[$i]['link'] = 'admin/useradmin.php';
$adminmenu[$i]['icon'] = $pathIcon32 . '/administration.png';

++$i;
$adminmenu[$i]['title'] = _MI_SMALLWORLDMENU_ABOUT;
$adminmenu[$i]['link'] = 'admin/about.php';
$adminmenu[$i]['icon'] = $pathIcon32 . '/about.png';
