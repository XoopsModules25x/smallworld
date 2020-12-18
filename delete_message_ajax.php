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

use Xmf\Request;
use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$prevLogger = $GLOBALS['xoopsLogger']->activated;
$GLOBALS['xoopsLogger']->activated = false; // disable logger

$id       = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? $GLOBALS['xoopsUser']->getVar('uid') : 0;
$msgowner = Request::getInt('msgowner', 0, 'POST');

if ($helper->isUserAdmin() || ($id == $msgowner && 0 !== $msgowner)) {
    $swDB = new Smallworld\SwDatabase();
    if (Request::hasVar('smallworld_msg_id', 'POST')) {
        $smallworld_msg_id = Request::getInt('smallworld_msg_id', 0, 'POST');
        $data              = $swDB->deleteWallMsg($id, $smallworld_msg_id);
    }
}
$GLOBALS['xoopsLogger']->activated = $prevLogger; // restore logger to previous state