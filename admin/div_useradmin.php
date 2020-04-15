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
 * @package      \XoopsModules\SmallWorld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

require_once __DIR__ . '/admin_header.php';
/**
 * Vars defined by inclusion of ./admin_header.php
 *
 * @var \XoopsModules\Smallworld\Admin $admin
 * @var \XoopsModules\Smallworld\DoSync $d
 * @var \XoopsModules\Smallworld\User $check
 * @var \XoopsModules\Smallworld\SwDatabase $swDB
 * @var \XoopsModules\Smallworld\WallUpdates $wall
 * @var \Xmf\Module\Admin $adminObject
 * @var \XoopsModules\Smallworld\Helper $helper
 * @var string $moduleDirName
 * @var string $moduleDirNameUpper
 */


require_once $helper->path('include/functions.php');
$GLOBALS['xoopsLogger']->activated = false;
$tpl                               = new \XoopsTpl();

$allusers_inspect = $admin->getAllUsers('yes');
foreach ($allusers_inspect as $data) {
    $ai['id']                    = $data['id'];
    $ai['userid']                = $data['userid'];
    $ai['username']              = $data['username'];
    $ai['realname']              = $data['realname'];
    $ai['userimage']             = smallworld_getAvatarLink($data['userid'], $data['userimage']);
    $ai['avatar_size']           = smallworld_getImageSize(80, 100, $ai['userimage']);
    $ai['avatar_highwide']       = smallworld_imageResize($ai['avatar_size'][0], $ai['avatar_size'][1], 50);
    $ai['ip']                    = $data['ip'];
    $ai['complaint']             = $data['complaint'];
    $ai['inspect_start']         = $data['inspect_start'];
    $ai['inspect_stop']          = $data['inspect_stop'];
    $ai['userinspect_timetotal'] = ($data['inspect_start'] + $data['inspect_stop']) - time();
    $tpl->append('allusersinspect', $ai);
}
$tpl->assign('allusersinspectcounter', count($ai));

$allusers_noinspect = $admin->getAllUsers('no');
foreach ($allusers_noinspect as $data) {
    $ani['id']                    = $data['id'];
    $ani['userid']                = $data['userid'];
    $ani['username']              = $data['username'];
    $ani['realname']              = $data['realname'];
    $ani['userimage']             = smallworld_getAvatarLink($data['userid'], $data['userimage']);
    $ani['avatar_size']           = smallworld_getImageSize(80, 100, $ani['userimage']);
    $ani['avatar_highwide']       = smallworld_imageResize($ani['avatar_size'][0], $ani['avatar_size'][1], 50);
    $ani['ip']                    = $data['ip'];
    $ani['complaint']             = $data['complaint'];
    $ani['inspect_start']         = '';
    $ani['inspect_stop']          = '';
    $ani['userinspect_timetotal'] = '';
    $tpl->append('allusersnoinspect', $ani);
}
$tpl->assign('allusersnoinspectcounter', count($ani));
$tpl->display($helper->path('templates/smallworld_alluserstodiv.tpl'));
