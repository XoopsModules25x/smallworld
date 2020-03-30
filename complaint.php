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
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @package      |XoopsModules\SmallWorld
 * @since        1.0
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 */

use Xmf\Request;
use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

$GLOBALS['xoopsLogger']->activated = false;

$db = new Smallworld\SwDatabase();

if ($GLOBALS['xoopsUser']) {
    if (Request::hasVar('byuser', 'POST')) {
        $by_userid = $GLOBALS['xoopsUser']->getVar('uid');
        $a_user    = addslashes(Request::getString('a_user', '', 'POST'));
        $auserid   = Request::getInt('auserid', 0, 'POST');
        $byuser    = Request::getInt('byuser', 0, 'POST');
        $id        = Request::getInt('id', 0, 'POST');
        $name      = addslashes(Request::getString('name', '', 'POST'));
        $time      = time();
        $data      = ['time' => $time, 'a_user' => $a_user, 'byuser' => $byuser, 'link' => $id, 'a_userid' => $auserid];
        $already   = $db->alreadycomplaint($id, $by_userid, $auserid);

        if (1 != $already) {
            $mail = new Smallworld\Mail();
            if (0 !== $helper->getConfig('smallworldusemailnotis')) {
                $mail->sendMails($by_userid, '', 'complaint', $link = null, $data);
            }
            $db->updateComplaint($auserid);
            echo _SMALLWORLD_JS_COMPLAINTSENT;
        } else {
            echo _SMALLWORLD_JS_COMPLAINT_ALREADY_SENT;
        }
    }
}

