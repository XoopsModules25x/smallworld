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

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_edittemplate.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$GLOBALS['xoopsLogger']->activated = false;
if ($_GET) {
    $swUserHandler = $helper->getHandler('SwUser');
    $name   = smallworld_sanitize($_GET['term']);
    $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE realname LIKE '%{$name}%' OR username LIKE '%{$name}%' ORDER BY userid LIMIT 5";
    $result = $GLOBALS['xoopsDB']->query($sql);
    $data   = [];

    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $user   = new \XoopsUser($row['userid']);
        $image  = '<img src="' . $swUserHandler->getAvatarLink($row['userid'], $row['userimage']) . '" height="20">';
        $data[] = [
            'label' => $image . ' ' . '<span class="searchusername">' . $row['realname'] . ' (' . $row['username'] . ')</span>',
            'value' => $user->uname(),
        ];
    }
    // jQuery wants JSON data
    header('Content-Type: text/javascript; charset=utf8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 3628800');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo json_encode($data);
    flush();
}
