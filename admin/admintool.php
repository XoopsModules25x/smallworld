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
 * @package      \XoopsModules\SmallWorld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

use Xmf\Request;
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/admin_header.php';
$GLOBALS['xoopsLogger']->activated = false;

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

$type = Request::getCmd('type', '', 'POST');
if ('' == $type) {
    $helper->redirect('admin/index.php', Constants::REDIRECT_DELAY_MEDIUM, sprintf(_AM_SMALLWORLD_ERROR_INVALID_ENTRY, '(type)'));
}

switch ($type) {
    case 'addtime':
        $userId = Request::getInt('userid', Constants::DEFAULT_UID, 'POST');
        $amount = Request::getInt('amount', 0, 'POST');
        $test   = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " WHERE userid = '" . $userId . "' AND (inspect_start+inspect_stop) > " . time() . '';
        $result = $GLOBALS['xoopsDB']->queryF($test);
        if ($GLOBALS['xoopsDB']->getRowsNum($result) < 1) {
            $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET inspect_start = '" . time() . "', inspect_stop = '" . $amount . "' WHERE userid='" . $userId . "'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
        } else {
            $sql    = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . ' SET inspect_stop = (inspect_stop + ' . $amount . ") WHERE userid='" . $userId . "'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
        }
        break;
    case 'deletetime':
        $userId = Request::getInt('deluserid', Constants::DEFAULT_UID, 'POST');
        $sql       = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('smallworld_admin') . " SET inspect_start = '', inspect_stop = '' WHERE userid='" . $userId . "'";
        $result    = $GLOBALS['xoopsDB']->queryF($sql);
        break;
    case 'deleteuser':
        $userId = Request::getInt('deluserid', Constants::DEFAULT_UID, 'POST');
        $swDB->deleteAccount($userId);
        break;
}
