<?php
/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Module: xForms
 *
 * @package   \XoopsModules\Smallworld
 * @author    Richard Griffith <richard@geekwright.com>
 * @author    trabis <lusopoemas@gmail.com>
 * @author    XOOPS Module Development Team
 * @copyright Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @license   https://www.gnu.org/licenses/gpl-2.0.html GNU Public License
 * @link      https://github.com/XoopsModules25x/smallworld
 */

use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Helper;
use XoopsModules\Smallworld\Utility;

/**
 * @internal {Make sure you PROTECT THIS FILE}
 */

if ((!defined('XOOPS_ROOT_PATH'))
    || !($GLOBALS['xoopsUser'] instanceof \XoopsUser)
    || !($GLOBALS['xoopsUser']->isAdmin()))
{
    exit('Restricted access' . PHP_EOL);
}
/**
 * Prepares system prior to attempting to update module
 *
 * @param \XoopsModule $module {@link \XoopsModule}
 * @return bool true if ready to install, false if not
 */
function xoops_module_pre_update_smallworld(\XoopsModule $module)
{
    /**
     * @var Smallworld\Helper $helper
     * @var Smallworld\Utility $utility
     */
    $helper       = Smallworld\Helper::getInstance();
    $utility      = new Smallworld\Utility();
    $xoopsSuccess = $utility::checkVerXoops($module);
    $phpSuccess   = $utility::checkVerPhp($module);

    return $xoopsSuccess && $phpSuccess;
}

/**
 * Update Smallworld module
 * 
 * @param \XoopsModule $module
 * @return bool true on succeess, false on failure
 */
function xoops_module_update_smallworld(\XoopsModule $module)
{
    $tables = new \Xmf\Database\Tables();

	$cTable = 'config';
	$criteria = new \Criteria('conf_name', 'smallworldprivorpub', '=');
	if ($success = $tables->useTable($cTable)) {
		$success = $tables->update($cTable, ['conf_value' => 1], $criteria);
	}
	
	$msgTable = 'smallworld_messages';
	$column   = 'message';
    if($successA = $tables->useTable($msgTable)) { // if this returns false, there is no table
		$attributes = $tables->getColumnAttributes($msgTable, $column);
		if (false === strpos($attributes, ' TEXT')) { // assumes it's already been updated if TEXT found
			$successA = $tables->alterColumn($msgTable, $column, ' TEXT ');
		}
	}

	$comTable  = 'smallworld_comments';
	$column   = 'comment';
    if($successB = $tables->useTable($comTable)) { // if this returns false, there is no table
		$attributes = $tables->getColumnAttributes($comTable, $column);
		if (false === strpos($attributes, ' TEXT')) { // assumes it's already been updated if 'TEXT' not found
			$successB =  $tables->alterColumn($comTable, $column, ' TEXT ');
		}
	}
	
	// now create smallworld_settings table if it doesn't exist
	$sTable  = 'smallworld_settings';
    if(false === ($successC = $tables->useTable($sTable))) { // if this returns true then the table exists already
        // Table does not exist -> create
		$successC = $tables->addTable($sTable);
		$successC = $successC && ($tables->setTableOptions($sTable, 'ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'));
		$successC = $successC && ($tables->addColumn($sTable, 'id', 'INT(11) NOT NULL AUTO_INCREMENT'));
		$successC = $successC && ($tables->addColumn($sTable, 'userid', 'INT(11) NOT NULL'));
		$successC = $successC && ($tables->addColumn($sTable, 'value', 'TEXT'));
		$successC = $successC && ($tables->addPrimaryKey($sTable, 'id'));
	}

	$successD = $tables->executeQueue();  // change the tables

	// setup the criteria to update the avatar fields
	$criteria = new \CriteriaCompo();
	$criteria->add(new \Criteria('userimage', 'blank.gif', '='));
	$criteria->add(new \Criteria('userimage', '\.(jpe?g|gif|png|bmp)' , 'NOT REGEXP'), 'OR');
	
	// update the admin avatar
	$aTable = 'smallworld_admin';
	if ($successE = $tables->useTable($aTable)) {
		$successE = $tables->update($aTable, ['userimage' => ''], $criteria);
	}
	// update user table avatar
	$uTable = 'smallworld_user';
	if ($successF = $tables->useTable($uTable)) {
		$successF = $tables->update($uTable, ['userimage' => ''], $criteria);
	}

	return $success && $successA && $successB && $successC && $successD && $successE && $successF;
}
