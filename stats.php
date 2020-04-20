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

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');
require_once XOOPS_ROOT_PATH . '/class/template.php';

$GLOBALS['xoopsLogger']->activated = false;
//error_reporting(E_ALL);
if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    $tpl = new \XoopsTpl();

    //$userid    = $GLOBALS['xoopsUser']->uid();
    //$newUsers  = smallworld_Stats_newest();
/*************************************/
    $newUsers        = [];
    $swUserHandler   = $helper->getHandler('SwUser');
    $criteria        = new \Criteria('');
    $criteria->setSort('regdate');
    $criteria->setLimit(3);
    $criteria->order = 'DESC';
    $swUserArray     = $swUserHandler->getAll($criteria, null, false);
    foreach($swUserArray as $swUser) {
        //$regdate    = date('d-m-Y', $swUser['regdate']);
        $regdate    = date(_SHORTDATESTRING, $swUser['regdate']);
        $newUsers[] = [
            'userid'         => $swUser['userid'],
            'username'       => $swUser['username'],
            'regdate'        => $regdate,
            'username_link'  => "<a href='" . $helper->url('userprofile.php?username=' . $swUser['username']) . "'>"
                             . "{$swUser['username']} ({$swUser['realname']}) [{$regdate}]</a>",
            'userimage'      => $swUser['userimage'],
            'userimage_link' => $swUserHandler->getAvatarLink($swUser['userid'], $swUserHandler->gravatar($swUser['userid']))
        ];
    }
/*************************************/
    $m_a_users = smallworld_mostactiveusers_allround();
    $br_users  = smallworld_topratedusers();
    $wo_users  = smallworld_worstratedusers();
    $birth     = smallworld_nextBirthdays();
    $bday      = !empty($birth) ? $birth : 0;
    $sp        = smallworld_sp();
    $tpl->assign([
        'newusers'    => $newUsers,
        'mostactiveU' => $m_a_users,
        'bestratedU'  => $br_users,
        'worstratedU' => $wo_users,
        'sp'          => $sp,
        'birthdays'   => $bday
    ]);
    //readfile(XOOPS_ROOT_PATH .'/modules/smallworld/templates/getStat.tpl');
    $tpl->display($helper->path('templates/getStat.tpl'));
}
