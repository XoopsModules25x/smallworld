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
require_once XOOPS_ROOT_PATH . '/class/template.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';

global $xoopsUser, $xoopsLogger;
$xoopsLogger->activated = false;

$db = new smallworld\SmallWorldDB;

if ($xoopsUser) {
    if ($_POST['byuser']) {
        $by_userid = $xoopsUser->getVar('uid');
        $a_user    = addslashes($_POST['a_user']);
        $auserid   = (int)$_POST['auserid'];
        $byuser    = addslashes($_POST['byuser']);
        $id        = addslashes($_POST['id']);
        $name      = addslashes($_POST['name']);
        $time      = time();
        $data      = ['time' => $time, 'a_user' => $a_user, 'byuser' => $byuser, 'link' => $id, 'a_userid' => $auserid];
        $already   = $db->alreadycomplaint($id, $by_userid, $auserid);

        if (1 != $already) {
            $mail = new SmallWorldMail;
            if (0 != smallworld_GetModuleOption('smallworldusemailnotis', $repmodule = 'smallworld')) {
                $mail->sendMails($by_userid, '', 'complaint', $link = null, $data);
            }
            $db->updateComplaint($auserid);
            echo _SMALLWORLD_JS_COMPLAINTSENT;
        } else {
            echo _SMALLWORLD_JS_COMPLAINT_ALREADY_SENT;
        }
    }
}
