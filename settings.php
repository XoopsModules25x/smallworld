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

global $xoopsUser;
require_once __DIR__ . '/../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;

if ($xoopsUser) {
    $check    = new smallworld\SmallWorldUser;
    $db       = new smallworld\SmallWorldDB;
    $id       = $xoopsUser->getVar('uid');
    $settings = [];
    if (isset($_POST['posts']) && isset($_POST['comments'])) {
        $post = serialize([
                              'posts'    => $_POST['posts'],
                              'comments' => $_POST['comments'],
                              'notify'   => $_POST['notify']
                          ]);

        if ($id >= 1) {
            $db->saveSettings($id, $post);
        }
        echo json_encode($post);
    } else {
        $posts = stripslashes($db->GetSettings($id));
        echo json_encode($posts);
    }
} else {
    header('HTTP/1.1 403 Forbidden');
}
