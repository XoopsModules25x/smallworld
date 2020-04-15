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
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_share.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');

$GLOBALS['xoopsLogger']->activated = false;
/*error_reporting(E_ALL);*/

if (Request::hasVar('updid', 'GET') && Request::hasVar('ownerid', 'GET')) {
    $updID   = Request::getInt('updid', 0, 'GET');
    $ownerID = Request::getInt('ownerid', 0, 'GET');
    $wm      = [];
} else {
    $helper->redirect('index.php', Constants::REDIRECT_DELAY_MEDIUM, _SMALLWORLD_UPDATEID_NOT_EXIST);
}

$perm                         = smallworldCheckPriv($updID);
$GLOBALS['xoopsTpl']->caching = 0;

if ($perm <= 0) {
    // Things to do with wall
    $wall         = new Smallworld\WallUpdates();
    $updatesarray = $wall->updatesSharelink($updID, $ownerID);
    //Srinivas Tamada http://9lessons.info
    //Loading Comments link with load_updates.php
    foreach ($updatesarray as $data) {
        $wm['msg_id']     = $data['msg_id'];
        $wm['orimessage'] = str_replace(["\r", "\n"], '', smallworld_stripWordsKeepUrl($data['message']));
        $wm['message']    = smallworld_cleanup($wm['message']);
        $wm['created']    = smallworld_time_stamp($data['created']);
        $wm['username']   = $data['username'];
        $wm['uid_fk']     = $data['uid_fk'];
        $wm['priv']       = $data['priv'];
        $wm['avatar']     = $wall->Gravatar($data['uid_fk']);
        if (1 !== $helper->getConfig('smallworldbookmarkavatar')) {
            $wm['avatar_link'] = $helper->url('assets/images/smallworld.png');
        } else {
            $wm['avatar_link'] = smallworld_getAvatarLink($data['uid_fk'], $wm['avatar']);
        }
        $wm['avatar_size']     = smallworld_getImageSize(80, 100, $wm['avatar_link']);
        $wm['avatar_highwide'] = smallworld_imageResize($wm['avatar_size'][0], $wm['avatar_size'][1], 50);
        $wm['usernameTitle']   = $wm['username'] . _SMALLWORLD_UPDATEONSITEMETA . $GLOBALS['xoopsConfig']['sitename'];
        $desc                  = str_replace('UPLIMAGE', '', html_entity_decode($data['message'], ENT_QUOTES));
        $GLOBALS['xoopsTpl']->assign('xoops_pagetitle', $wm['usernameTitle']);
        if ($GLOBALS['xoTheme'] instanceof \xos_opal_Theme) {
            $GLOBALS['xoTheme']->addMeta('meta', 'description', $desc);
            $GLOBALS['xoTheme']->addMeta('meta', 'title', $wm['usernameTitle']);
            $GLOBALS['xoTheme']->addMeta('meta', 'author', $wm['username']);
            $GLOBALS['xoTheme']->addMeta('meta', 'og:title', $wm['usernameTitle']);
            $GLOBALS['xoTheme']->addMeta('meta', 'og:url', $helper->url('index.php'));
            $GLOBALS['xoTheme']->addMeta('meta', 'og:site_name', $GLOBALS['xoopsConfig']['sitename']);
            $GLOBALS['xoTheme']->addMeta('meta', 'og:description', $desc);
            $GLOBALS['xoTheme']->addMeta('meta', 'og:image', $wm['avatar_link']);
            $GLOBALS['xoTheme']->addMeta('meta', 'og:updated_time', $data['created']);
        }
        $GLOBALS['xoopsTpl']->append('walldata', $wm);
    }
} else {
    $GLOBALS['xoTheme']->addMeta('meta', 'og:image', $helper->url('assets/images/smallworld.png'));
}

require_once XOOPS_ROOT_PATH . '/footer.php';
