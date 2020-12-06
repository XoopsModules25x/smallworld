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
global $xoopsDB, $xoTheme, $xoopsLogger;
$xoopsLogger->activated = false;
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';

if ('addtime' === $_POST['type']) {
    $userid = (int)$_POST['userid'];
    $amount = (int)$_POST['amount'];
    $test = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_admin') . " WHERE userid = '" . $userid . "' AND (inspect_start+inspect_stop) > " . time() . '';
    $result = $xoopsDB->queryF($test);
    if ($xoopsDB->getRowsNum($result) < 1) {
        $sql = 'UPDATE ' . $xoopsDB->prefix('smallworld_admin') . " SET inspect_start = '" . time() . "', inspect_stop = '" . $amount . "' WHERE userid='" . $userid . "'";
        $result = $xoopsDB->queryF($sql);
    } else {
        $sql = 'UPDATE ' . $xoopsDB->prefix('smallworld_admin') . ' SET inspect_stop = (inspect_stop + ' . $amount . ") WHERE userid='" . $userid . "'";
        $result = $xoopsDB->queryF($sql);
    }
}
if ('deletetime' === $_POST['type']) {
    $sql = 'UPDATE ' . $xoopsDB->prefix('smallworld_admin') . " SET inspect_start = '', inspect_stop = '' WHERE userid='" . (int)$_POST['deluserid'] . "'";
    $result = $xoopsDB->queryF($sql);
}

if ('deleteUser' === $_POST['type']) {
    $db = new SmallWorldDB;

    $userid = (int)$_POST['deluserid'];
    $db->deleteAccount($userid);
}
