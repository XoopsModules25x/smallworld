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

use Xmf\Request;
use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

$GLOBALS['xoopsLogger']->activated = false;
if (isset($_GET) && !empty($_GET)) {
    $swUserHandler = $helper->getHandler('SwUser');
    $q      = smallworld_sanitize(Request::getString('term', '', 'GET'));
    $sql    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE realname LIKE '%" . $q . "%' OR username LIKE '%" . $q . "%' ORDER BY userid LIMIT 5";
    $result = $GLOBALS['xoopsDB']->query($sql);
    $data   = [];

    while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
        $user  = new \XoopsUser($row['userid']);
        $image = '<img src="' . $swUserHandler->getAvatarLink($row['userid'], $row['userimage']) . '" height="20" >';

        //@todo figure out where the $imageSize[] array comes from
        $imageHw = smallworld_imageResize($imageSize[0], $imageSize[1], 30);
        $data[]  = [
            'label' => $image . ' ' . '<span class="searchusername">' . $row['realname'] . ' (' . $row['username'] . ')</span>',
            'value' => $user->uname(),
        ];
    }
    // jQuery wants JSON data
    echo json_encode($data);

    flush();
}
