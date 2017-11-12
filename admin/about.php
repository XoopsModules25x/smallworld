<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  :            {@link https://xoops.org 2001-2017 XOOPS Project}
 * @license    :              {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * @module     :               Smallworld
 * @Author     :               Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @copyright  :            2011 Culex
 * @Repository path:      $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/admin/about.php $
 * @Last       committed:       $Revision: 11573 $
 * @Last       changed by:      $Author: djculex $
 * @Last       changed date:    $Date: 2013-05-22 15:01:51 +0200 (on, 22 maj 2013) $
 * @ID         :                   $Id: about.php 11573 2013-05-22 13:01:51Z djculex $
 **/

include_once dirname(__FILE__) . '/admin_header.php';

xoops_cp_header();
global $xoTheme;

$aboutAdmin = new ModuleAdmin();

echo $aboutAdmin->addNavigation('about.php');
echo $aboutAdmin->renderAbout('', false);

$xoTheme->addStylesheet(XOOPS_URL . '/modules/smallworld/css/SmallworldAdmin.css');
$xoTheme->addScript(XOOPS_URL . '/modules/smallworld/js/adminsmallworld.js');
include 'admin_footer.php';
