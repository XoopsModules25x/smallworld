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

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$GLOBALS['xoopsLogger']->activated = false;
$swDB = new Smallworld\SwDatabase();
$mail = new Smallworld\Mail();

$swDB->handlePosts();

// Create user albums etc
if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    $img    = new Smallworld\SmallWorldImages();
    $userID = $GLOBALS['xoopsUser']->uid();
    if ('edit' !== $_POST['function']) {
        $img->createAlbum($userID);
        if (1 == $helper->getConfig('smallworldusemailnotis')) {
            $mail->sendMails($userID, $userID, 'register', $link = null, []);
        }
    }
}
