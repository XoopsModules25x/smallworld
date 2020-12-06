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

use Xmf\Request;
use Xoopsmodules\smallworld;
require_once __DIR__ . '/header.php';

require_once __DIR__ . '/../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_edittemplate.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoopsDB, $xoopsLogger;
$xoopsLogger->activated = false;
if ($_GET) {
    $q      = Smallworld_sanitize($_GET['term']);
    $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE realname LIKE '%" . $q . "%' OR username LIKE '%" . $q . "%' ORDER BY userid LIMIT 5";
    $result = $xoopsDB->query($sql);
    $data   = [];

    while ($row = $xoopsDB->fetchArray($result)) {
        $user   = new xoopsUser($row['userid']);
        $image  = '<img src="' . smallworld_getAvatarLink($row['userid'], $row['userimage']) . '" height="20">';
        $data[] = [
            'label' => $image . ' ' . '<span class="searchusername">' . $row['realname'] . ' (' . $row['username'] . ')</span>',
            'value' => $user->uname()
        ];
    }
    // jQuery wants JSON data
    header('Content-Type: text/javascript; charset=utf8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 3628800');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo json_encode($data);
    flush();
} else {
}
