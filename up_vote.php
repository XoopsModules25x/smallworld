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

global $xoopsUser, $xoTheme, $xoopsTpl, $xoopsLogger, $xoopsDB;
$xoopsLogger->activated = false;
$Wall                   = new smallworld\WallUpdates();
if ($xoopsUser) {
    if ($_POST['id']) {
        $id       = (int)$_POST['id'];
        $type     = $GLOBALS['xoopsDB']->escape($_POST['type']);
        $type2    = $GLOBALS['xoopsDB']->escape($_POST['type2']);
        $owner    = $GLOBALS['xoopsDB']->escape($_POST['owner']);
        $userid   = $xoopsUser->getVar('uid');
        $hasvoted = $Wall->HasVoted($userid, $type, $type2, $id);
        if ('msg' === $type) {
            if ($hasvoted > 0) {
                echo "<script type='text/javascript'>";
                echo "alert('" . _SMALLWORLD_JS_ALREADYVOTED . "');";
                echo '</script>';
            } else {
                $sql    = 'INSERT INTO ' . $xoopsDB->prefix('smallworld_vote') . " (vote_id,msg_id,com_id,user_id,owner,up,down) VALUES ('', '" . $id . "', '0', '" . $userid . "', '" . $owner . "', '1', '0')";
                $result = $xoopsDB->queryF($sql);
            }
            $newvote = $Wall->countVotes($type, 'up', $id);
        }

        if ('com' === $type) {
            if ($hasvoted > 0) {
                echo "<script type='text/javascript'>alert('" . _SMALLWORLD_JS_ALREADYVOTED . "');</script>";
            } else {
                $sql    = 'INSERT INTO ' . $xoopsDB->prefix('smallworld_vote') . " (vote_id,msg_id,com_id,user_id,owner,up,down) VALUES ('', '" . $id . "', '" . $type2 . "', '" . $userid . "', '" . $owner . "', '1', '0')";
                $result = $xoopsDB->queryF($sql);
            }
            $newvote = $Wall->countVotesCom($type, 'up', $type2, $id);
        }
    }

    $link = '<span id ="smallworld_votenum">' . $newvote . '</span> <a href="javascript:void(0)" name="up" class="smallworld_stcomment_vote"';
    $link .= ' id="' . $id . '" type="' . $type . '" owner="' . $owner . '" type2="' . $type2 . '">';
    $link .= '<img class="smallworld_voteimg" src = "images/like.png" ></a>';
    echo $link;
}
