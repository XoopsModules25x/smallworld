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

//require_once __DIR__ . '/header.php';

require_once (dirname(dirname(__DIR__))) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';

$GLOBALS['xoopsLogger']->activated  = false;
$GLOBALS['xoopsTpl']->caching       = 0;
$_COOKIE[session_name()] = session_id();
if ($GLOBALS['xoopsUser)'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)
    && ($_GET['SmallworldGetUserMsgCount']))
{
    $counts = smallworld_getCountFriendMessagesEtc();
    header('Content-type: application/json');
    echo "{\"NewUserMsgCount\":$counts}";
}
