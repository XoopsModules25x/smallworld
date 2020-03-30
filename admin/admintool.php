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
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @package      \XoopsModules\SmallWorld
 * @since        1.0
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 */

use \Xmf\Request;
use XoopsModules\Smallworld;

require_once __DIR__ . '/admin_header.php';
$GLOBALS['xoopsLogger']->activated = false;

/** @var \XoopsModules\SmallWorld\Helper $helper */
require_once $helper->path('include/functions.php');

$type = Request::getCmd('type', '', 'POST');
if ((false === $helper->isUserAdmin()) || ('' == $type)) {
	// @todo move hard coded language string to language file
	$helper->redirect('admin/index.php', 3, 'Invalid Entry');
}

switch ($type) {
	case 'addtime':
		$userid = Request::getInt('userid', 0, 'POST');
		$amount = Request::getInt('amount', 0, 'POST');
		$test   = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " WHERE userid = '" . $userid . "' AND (inspect_start+inspect_stop) > " . time() . '';
		$result = $GLOBALS['xoopsDB']->queryF($test);
		if ($GLOBALS['xoopsDB']->getRowsNum($result) < 1) {
			$sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET inspect_start = '" . time() . "', inspect_stop = '" . $amount . "' WHERE userid='" . $userid . "'";
			$result = $GLOBALS['xoopsDB']->queryF($sql);
		} else {
			$sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . ' SET inspect_stop = (inspect_stop + ' . $amount . ") WHERE userid='" . $userid . "'";
			$result = $GLOBALS['xoopsDB']->queryF($sql);
		}
		break;
	case 'deletetime':
		$deluserid = Request::getInt('deluserid', 0, 'POST');
		$sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET inspect_start = '', inspect_stop = '' WHERE userid='" . $deluserid . "'";
		$result = $GLOBALS['xoopsDB']->queryF($sql);
		break;
	case 'deleteuser':
		$db = new Smallworld\SwDatabase();
		$userid = Request::getInt('deluserid', 0, 'POST');
		$db->deleteAccount($userid);
		break;
}
