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

require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';

/**
 * @param        $queryarray
 * @param        $andor
 * @param        $limit
 * @param        $offset
 * @param        $userid
 * @param string $sortby
 * @return array
 */
function smallworld_search($queryarray, $andor, $limit, $offset, $userid, $sortby = 'created DESC')
{
    global $xoopsDB, $xoopsConfig, $myts, $xoopsUser;

    if (file_exists(XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/main.php')) {
        require_once XOOPS_ROOT_PATH . '/modules/smallworld/language/' . $xoopsConfig['language'] . '/main.php';
    } else {
        require_once XOOPS_ROOT_PATH . '/modules/smallworld/language/english/main.php';
    }

    $moduleHandler = xoops_getHandler('module');
    $module        = $moduleHandler->getByDirname('smallworld');
    $modid         = $module->getVar('mid');
    $searchparam   = '';
    $highlight     = false;

    $gpermHandler = xoops_getHandler('groupperm');
    if (is_object($xoopsUser)) {
        $groups    =& $xoopsUser->getGroups();
        $id        = $xoopsUser->getVar('uid');
        $Wall      = new WallUpdates();
        $followers = Smallworld_array_flatten($Wall->getFollowers($id), 0);
    } else {
        $id        = 0;
        $groups    = XOOPS_GROUP_ANONYMOUS;
        $followers = [];
    }

    if ($id > 0 && '' != $id) {
        $sql = 'SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM ' . $xoopsDB->prefix('smallworld_messages') . ' M, ' . $xoopsDB->prefix('smallworld_user') . ' U WHERE M.uid_fk=U.userid';
    } else {
        $sql = 'SELECT M.msg_id, M.uid_fk, M.message, M.created, M.priv, U.username FROM ' . $xoopsDB->prefix('smallworld_messages') . ' M, ' . $xoopsDB->prefix('smallworld_user') . ' U WHERE M.uid_fk=U.userid';
    }

    if (0 != $userid) {
        $sql .= ' AND M.uid_fk = ' . $userid . ' ';
    }
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND (M.message LIKE '%$queryarray[0]%' OR M.message LIKE '%$queryarray[0]%' OR U.username LIKE '%$queryarray[0]%'";
        $sql .= ') ';
        // keywords highlighting
        if ($highlight) {
            $searchparam = '&keywords=' . urlencode(trim(implode(' ', $queryarray)));
        }
    }
    $sql    .= 'ORDER BY created DESC';
    $result = $xoopsDB->query($sql, $limit, $offset);
    $ret    = [];
    $i      = 0;
    while ($myrow = $xoopsDB->fetchArray($result)) {
        if (in_array($myrow['uid_fk'], $followers) || $myrow['uid_fk'] == $id) {
            $ret[$i]['image'] = 'images/smallworld_icn.png';
            $ret[$i]['link']  = 'permalink.php?ownerid=' . $myrow['uid_fk'] . '&updid=' . $myrow['msg_id'];
            if (preg_match('/UPLIMAGE/', $myrow['message'])) {
                $ownmsg           = str_replace('UPLIMAGE ', '', $myrow['message']);
                $ret[$i]['title'] = $ownmsg;
                $ret[$i]['title'] = Smallworld_getName($myrow['uid_fk']) . ' -> ' . _SMALLWORLD_GALLERY;
                $ret[$i]['title'] = str_replace(['&lt;', '&gt;'], ['<', '>'], $ret[$i]['title']);
            } else {
                $ret[$i]['title'] = smallworld_shortenText($myrow['message'], 60);
            }
            $ret[$i]['time'] = $myrow['created'];
            $ret[$i]['uid']  = $myrow['uid_fk'];
        } else {
            --$i;
        }
        ++$i;
    }

    return $ret;
}
