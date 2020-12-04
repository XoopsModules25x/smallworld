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
$GLOBALS['xoopsLogger']->activated = false;

$id       = $GLOBALS['xoopsUser']->getVar('uid');
$msgowner = Request::getInt('msgowner', 0, 'POST');

if ($helper->isUserAdmin() || $id == $msgowner) {
    if (isset($_POST['smallworld_com_id'])) {
        $smallworld_com_id = Request::getInt('smallworld_com_id', 0, 'POST');
        if (0 !== $smallworld_com_id) {
            $swDB = new Smallworld\SwDatabase();
            $data = $swDB->deleteWallComment($smallworld_com_id);
        }
    }
}
