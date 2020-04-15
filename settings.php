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

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $check    = new Smallworld\User();
    $swDB     = new Smallworld\SwDatabase();
    $id       = $GLOBALS['xoopsUser']->getVar('uid');
    $settings = [];
    if (Request::hasVar('posts', 'POST') && Request::hasVar('comments', 'POST')) {
        //@todo these $_POST vars need to be sanitized
        $post = serialize(
            [
                'posts'    => $_POST['posts'],
                'comments' => $_POST['comments'],
                'notify'   => $_POST['notify'],
            ]
        );

        if (0 < $id) { // this will only fail if \XoopUser->isGuest() is true
            $swDB->saveSettings($id, $post);
        }
        echo json_encode($post);
    } else {
        $posts = stripslashes($swDB->getSettings($id));
        echo json_encode($posts);
    }
} else {
    header('HTTP/1.1 403 Forbidden');
}
