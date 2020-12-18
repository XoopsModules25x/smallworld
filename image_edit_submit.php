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

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$GLOBALS['xoopsLogger']->activated = false;
$swDB                   = new Smallworld\SwDatabase();
$upd                    = new Smallworld\WallUpdates();
$userID                 = $GLOBALS['xoopsUser']->uid();
$ri                     = smallworld_getRndImg($userID);
if ('' != $ri) {
    $riUrl = XOOPS_URL . '/uploads/albums_smallworld/' . $userID . '/' . $ri;
}

$update = 'UPLIMAGE' . ' ' . $riUrl;

$upd->insertUpdate($userID, $update);
$swDB->handleImageEdit();
