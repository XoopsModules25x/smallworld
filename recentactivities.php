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

use Xmf\Request;
use XoopsModules\Smallworld;
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

/** @var \XoopsModules\Smallworld\Helper $helper
 * @var \MyTextSanitizer $myts
 */
require_once $helper->path('include/functions.php');
require_once XOOPS_ROOT_PATH . '/class/template.php';
$GLOBALS['xoopsLogger']->activated = false;

$tpl = new \XoopsTpl();
$helper->loadLanguage('user');

$username = Request::getString('username', '', 'GET');
$id       = $helper->getHandler('SwUser')->getByName($username); // get id of user which profile you want to see
//$id       = smallworld_isset_or(Request::getString('username', '', 'GET')); // get id of user which profile you want to see
//$id       = (int)$id; // smallworld_isset_or doesn't always return an integer
$thisUser = new \XoopsUser($id);

/** @todo refactor using Smallworld\Helper permissions helper */
$moduleHandler = xoops_getHandler('module');
$gpermHandler  = xoops_getHandler('groupperm');
$groups        = ($GLOBALS['xoopsUser'] instanceof \XoopsUser) ? $GLOBALS['xoopsUser']->getGroups() : 0;

$criteria = new \CriteriaCompo(new \Criteria('hassearch', 1));
$criteria->add(new \Criteria('isactive', 1));
$mids = array_keys($moduleHandler->getList($criteria));

foreach ($mids as $mid) {
    if ($gpermHandler->checkRight('module_read', $mid, $groups)) {
        $module  = $moduleHandler->get($mid);
        $results = $module->search('', '', 5, 0, $id);
        $count   = count($results);
        if (is_array($results) && $count > 0) {
            for ($i = 0; $i < $count; ++$i) {
                if (isset($results[$i]['image']) && '' !== $results[$i]['image']) {
                    $results[$i]['image'] = $helper->url($results[$i]['image']);
                } else {
                    $results[$i]['image'] = XOOPS_URL . '/images/icons/posticon2.gif';
                }

                if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                    $results[$i]['link'] = $helper->url($results[$i]['link']);
                }

                $results[$i]['title'] = $myts->htmlSpecialChars($results[$i]['title']);
                $results[$i]['time']  = $results[$i]['time'] ? formatTimestamp($results[$i]['time']) : '';
            }
            if (5 == $count) {
                $showall_link = '<a href="' . XOOPS_URL . '/search.php?action=showallbyuser&amp;mid=' . $mid . '&amp;uid=' . $id . '">' . _US_SHOWALL . '</a>';
            } else {
                $showall_link = '';
            }
            $tpl->assign('lang_allaboutuser', sprintf(_US_ALLABOUT, $thisUser->getVar('uname')));
            $tpl->append(
                'modules',
                [
                    'name'         => $module->getVar('name'),
                    'results'      => $results,
                    'showall_link' => $showall_link,
                ]
            );
        }
        unset($module);
    }
}
$tpl->display($helper->path('templates/smallworld_userinfo.tpl'));
