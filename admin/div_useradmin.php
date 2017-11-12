<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright  :            {@link https://xoops.org 2001-2017 XOOPS Project}
 * @license    :                {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * @module     :                Smallworld
 * @Author     :                Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @copyright  :            2011 Culex
 * @Repository path:        $HeadURL: https://xoops.svn.sourceforge.net/svnroot/xoops/XoopsModules/smallworld/trunk/smallworld/admin/div_useradmin.php $
 * @Last       committed:        $Revision: 8924 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2012-02-09 21:13:23 +0100 (to, 09 feb 2012) $
 * @ID         :                    $Id: div_useradmin.php 8924 2012-02-09 20:13:23Z djculex $
 **/

require_once 'admin_header.php';
global $xoopsDB, $xoTheme, $xoopsLogger;
$xoopsLogger->activated = false;
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php');

$Tpl   = new XoopsTpl();
$admin = new SmallworldAdmin();

$allusers_inspect = $admin->getAllUsers('yes');
if (!empty($allusers_inspect)) {
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
        $Tpl->append('allusersinspect', $ai);
    }
}
$Tpl->assign('allusersinspectcounter', count($ai));

$allusers_noinspect = $admin->getAllUsers('no');
if (!empty($allusers_noinspect)) {
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
        $Tpl->append('allusersnoinspect', $ani);
    }
}
$Tpl->assign('allusersnoinspectcounter', count($ani));

$Tpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/smallworld_alluserstodiv.html');
?>
