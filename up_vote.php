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

require_once XOOPS_ROOT_PATH . '/class/template.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

$GLOBALS['xoopsLogger']->activated = false;

$wall = new Smallworld\WallUpdates();
if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    if (Request::hasVar('id', 'POST')) {
        $id       = Request::getInt('id', 0, 'POST');
        $type     = $GLOBALS['xoopsDB']->escape($_POST['type']);
        $type2    = $GLOBALS['xoopsDB']->escape($_POST['type2']);
        $owner    = $GLOBALS['xoopsDB']->escape($_POST['owner']);
        $userid   = $GLOBALS['xoopsUser']->getVar('uid');
        $hasvoted = $wall->hasVoted($userid, $type, $type2, $id);
        if ('msg' === $type) {
            if ($hasvoted > 0) {
                echo "<script type='text/javascript'>";
                echo "alert('" . _SMALLWORLD_JS_ALREADYVOTED . "');";
                echo '</script>';
            } else {
                $sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " (vote_id,msg_id,com_id,user_id,owner,up,down) VALUES (null, '" . $id . "', '0', '" . $userid . "', '" . $owner . "', '1', '0')";
                $result = $GLOBALS['xoopsDB']->queryF($sql);
            }
            $newvote = $wall->countVotes($type, 'up', $id);
        }

        if ('com' === $type) {
            if ($hasvoted > 0) {
                echo "<script type='text/javascript'>alert('" . _SMALLWORLD_JS_ALREADYVOTED . "');</script>";
            } else {
                $sql    = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('smallworld_vote') . " (vote_id,msg_id,com_id,user_id,owner,up,down) VALUES (null, '" . $id . "', '" . $type2 . "', '" . $userid . "', '" . $owner . "', '1', '0')";
                $result = $GLOBALS['xoopsDB']->queryF($sql);
            }
            $newvote = $wall->countVotesCom($type, 'up', $type2, $id);
        }
    }

    $link = '<span id ="smallworld_votenum">' . $newvote . '</span> <a href="javascript:void(0)" name="up" class="smallworld_stcomment_vote"';
    $link .= ' id="' . $id . '" type="' . $type . '" owner="' . $owner . '" type2="' . $type2 . '">';
    $link .= '<img class="smallworld_voteimg" src = "assets/images/like.png" ></a>';
    echo $link;
}
