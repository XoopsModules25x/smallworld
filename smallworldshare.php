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
 * @Repository path:        $HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/smallworldshare.php $
 * @Last       committed:        $Revision: 11659 $
 * @Last       changed by:        $Author: djculex $
 * @Last       changed date:    $Date: 2013-06-12 19:42:10 +0200 (on, 12 jun 2013) $
 * @ID         :                    $Id: smallworldshare.php 11659 2013-06-12 17:42:10Z djculex $
 **/
include_once('../../mainfile.php');
$xoopsOption['template_main'] = 'smallworld_share.html';
include_once(XOOPS_ROOT_PATH . '/header.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php');
include_once(XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php');
global $xoopsUser, $xoTheme, $xoopsConfig, $xoopsTpl, $xoopsLogger;

$xoopsLogger->activated = false;
/*error_reporting(E_ALL);*/

if (isset($_GET['updid']) && isset($_GET['ownerid'])) {
    $updID   = $_GET['updid'];
    $ownerID = $_GET['ownerid'];
    $wm      = array();
} else {
    redirect_header(XOOPS_URL . '/modules/smallworld/index.php', 5, _SMALLWORLD_UPDATEID_NOT_EXIST);
}

$perm              = smallworldCheckPriv($updID);
$xoopsTpl->caching = 0;

if ($perm <= 0) {
    // Things to do with wall
    $Wall         = new Wall_Updates();
    $updatesarray = $Wall->UpdatesSharelink($updID, $ownerID);
    //Srinivas Tamada http://9lessons.info
    //Loading Comments link with load_updates.php
    foreach ($updatesarray as $data) {
        $wm['msg_id']     = $data['msg_id'];
        $wm['orimessage'] = str_replace(array("\r", "\n"), '', Smallworld_stripWordsKeepUrl($data['message']));
        $wm['message']    = Smallworld_cleanup($wm['message']);
        $wm['created']    = smallworld_time_stamp($data['created']);
        $wm['username']   = $data['username'];
        $wm['uid_fk']     = $data['uid_fk'];
        $wm['priv']       = $data['priv'];
        $wm['avatar']     = $Wall->Gravatar($data['uid_fk']);
        if (smallworld_GetModuleOption('smallworldbookmarkavatar', $repmodule = 'smallworld' != 1)) {
            $wm['avatar_link'] = XOOPS_URL . '/modules/smallworld/images/smallworld.png';
        } else {
            $wm['avatar_link'] = smallworld_getAvatarLink($data['uid_fk'], $wm['avatar']);
        }
        $wm['avatar_size']     = smallworld_getImageSize(80, 100, $wm['avatar_link']);
        $wm['avatar_highwide'] = smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);
        $wm['usernameTitle']   = $wm['username'] . _SMALLWORLD_UPDATEONSITEMETA . $xoopsConfig['sitename'];
        $desc                  = str_replace('UPLIMAGE', '', html_entity_decode($data['message'], ENT_QUOTES));
        $xoopsTpl->assign('xoops_pagetitle', $wm['usernameTitle']);
        if (isset($xoTheme) && is_object($xoTheme)) {
            $xoTheme->addMeta('meta', 'description', $desc);
            $xoTheme->addMeta('meta', 'title', $wm['usernameTitle']);
            $xoTheme->addMeta('meta', 'author', $wm['username']);
            $xoTheme->addMeta('meta', 'og:title', $wm['usernameTitle']);
            $xoTheme->addMeta('meta', 'og:url', XOOPS_URL . '/modules/smallworld/index.php');
            $xoTheme->addMeta('meta', 'og:site_name', $xoopsConfig['sitename']);
            $xoTheme->addMeta('meta', 'og:description', $desc);
            $xoTheme->addMeta('meta', 'og:image', $wm['avatar_link']);
            $xoTheme->addMeta('meta', 'og:updated_time', $data['created']);
        }
        $xoopsTpl->append('walldata', $wm);

    }
} else {
    $xoTheme->addMeta('meta', 'og:image', XOOPS_URL . '/modules/smallworld/images/smallworld.png');
}

include(XOOPS_ROOT_PATH . '/footer.php');
?>
