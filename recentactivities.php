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
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
require_once XOOPS_ROOT_PATH . '/class/template.php';
global $xoopsUser, $xoTheme, $xoopsConfig, $xoopsTpl, $xoopsLogger;
$xoopsLogger->activated = false;

$tpl = new XoopsTpl();

$id   = smallworld_isset_or($_GET['username']); // Id of user wich profile you want to see
$user = new XoopsUser($id);

$uid = $user->getVar('uid');

$myts = MyTextSanitizer::getInstance();

xoops_loadLanguage('user');

$moduleHandler = xoops_getHandler('module');
$configHandler = xoops_getHandler('config');
$thisUser      = $memberHandler->getUser($id);

$gpermHandler = xoops_getHandler('groupperm');
$groups        = is_object($xoopsUser) ? $xoopsUser->getGroups() : 0;

$criteria = new CriteriaCompo(new Criteria('hassearch', 1));
$criteria->add(new Criteria('isactive', 1));
$mids = array_keys($moduleHandler->getList($criteria));

foreach ($mids as $mid) {
    if ($gpermHandler->checkRight('module_read', $mid, $groups)) {
        $module  = $moduleHandler->get($mid);
        $results = $module->search('', '', 5, 0, $thisUser->getVar('uid'));
        $count   = count($results);
        if (is_array($results) && $count > 0) {
            for ($i = 0; $i < $count; ++$i) {
                if (isset($results[$i]['image']) && '' != $results[$i]['image']) {
                    $results[$i]['image'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $results[$i]['image'];
                } else {
                    $results[$i]['image'] = XOOPS_URL . '/images/icons/posticon2.gif';
                }

                if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                    $results[$i]['link'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/' . $results[$i]['link'];
                }

                $results[$i]['title'] = $myts->htmlspecialchars($results[$i]['title']);
                $results[$i]['time']  = $results[$i]['time'] ? formatTimestamp($results[$i]['time']) : '';
            }
            if (5 == $count) {
                $showall_link = '<a href="' . XOOPS_URL . '/search.php?action=showallbyuser&amp;mid=' . $mid . '&amp;uid=' . $thisUser->getVar('uid') . '">' . _US_SHOWALL . '</a>';
            } else {
                $showall_link = '';
            }
            $tpl->assign('lang_allaboutuser', sprintf(_US_ALLABOUT, $thisUser->getVar('uname')));
            $tpl->append('modules', [
                'name'         => $module->getVar('name'),
                'results'      => $results,
                'showall_link' => $showall_link
            ]);
        }
        unset($module);
    }
}
$tpl->display(XOOPS_ROOT_PATH . '/modules/smallworld/templates/smallworld_userinfo.tpl');
