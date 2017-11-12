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

xoops_cp_header();
global $xoTheme;

$adminObject->displayNavigation(basename(__FILE__));
$adminObject->setPaypal('xoopsfoundation@gmail.com');
$adminObject->displayAbout(false);

$xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/assets/css/SmallworldAdmin.css');
$xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/adminsmallworld.js');
require_once __DIR__ . '/admin_footer.php';
