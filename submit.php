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
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;
$db                     = new smallworld\SmallWorldDB;
$mail                   = new SmallWorldMail;

$db->handlePosts();

// Create user albums etc
$img    = new smallworld\SmallWorldImages;
$userID = $xoopsUser->getVar('uid');
if ('edit' !== $_POST['function']) {
    $img->createAlbum($userID);
    if (0 != smallworld_GetModuleOption('smallworldusemailnotis', $repmodule = 'smallworld')) {
        $mail->sendMails($userID, $userID, 'register', $link = null, []);
    }
}
