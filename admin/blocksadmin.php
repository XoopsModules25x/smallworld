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
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @copyright    XOOPS Project https://xoops.org/
 * @author       XOOPS Development Team
 * @link         https://github.com/XoopsModules25x/smallworld
 */

use Xmf\Request;
use XoopsModules\Smallworld\Constants;

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
 * @var array $block
 */

if (!$helper->isUserAdmin() || (!$GLOBALS['xoopsModule'] instanceof \XoopsModule)) {
    exit(constant('CO_' . $moduleDirNameUpper . '_' . 'ERROR403'));
}

require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

$op = Request::getCmd('op', 'list');

switch ($op) {
    case ('list'):
        xoops_cp_header();
        //        mpu_adm_menu();
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $moduleDirName      = basename(dirname(__DIR__));
        $moduleDirNameUpper = mb_strtoupper($moduleDirName); //$capsDirName
        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/blocksadmin', 'system');
        xoops_loadLanguage('admin/groups', 'system');

        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        /** @var \XoopsMemberHandler $memberHandler */
        $memberHandler = xoops_getHandler('member');
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');
        $groups           = $memberHandler->getGroups();
        $criteria         = new \CriteriaCompo(new \Criteria('hasmain', 1));
        $criteria->add(new \Criteria('isactive', 1));
        $module_list     = $moduleHandler->getList($criteria);
        $module_list[-1] = _AM_SYSTEM_BLOCKS_TOPPAGE;
        $module_list[0]  = _AM_SYSTEM_BLOCKS_ALLPAGES;
        ksort($module_list);
        echo "<h4 class='left'>" . constant('CO_' . $moduleDirNameUpper . '_' . 'BADMIN') . '</h4>';
        $moduleHandler = xoops_getHandler('module');
        echo "<form action='" . $_SERVER['SCRIPT_NAME'] . "' name='blockadmin' method='post'>";
        echo $GLOBALS['xoopsSecurity']->getTokenHTML();
        echo "<table class='outer width100' cellpadding='4' cellspacing='1'>\n"
           . "    <tr class='middle'>\n"
           . "        <th class='center'>" . constant('CO_' . $moduleDirNameUpper . '_' . 'TITLE') . "</th>\n"
           . "        <th class='center' nowrap='nowrap'>\n"
           . "            " . constant('CO_' . $moduleDirNameUpper . '_' . 'SIDE') . "<br>\n"
           . "            " . _LEFT  . '-' . _CENTER . '-' . _RIGHT . "\n"
           . "        </th>\n"
           . "        <th class='center'>" . constant('CO_' . $moduleDirNameUpper . '_' . 'WEIGHT') . "</th>\n"
           . "        <th class='center'>" . constant('CO_' . $moduleDirNameUpper . '_' . 'VISIBLE') . "</th>\n"
           . "        <th class='center'>" . _AM_SYSTEM_BLOCKS_VISIBLEIN . "</th>\n"
           . "        <th class='center'>" . _AM_SYSTEM_ADGS . "</th>\n"
           . "        <th class='center'>" . _AM_SYSTEM_BLOCKS_BCACHETIME . "</th>\n"
           . "        <th class='center'>" . constant('CO_' . $moduleDirNameUpper . '_' . 'ACTION') . "</th>\n"
           . "    </tr>\n";

       $block_arr   = \XoopsBlock::getByModule($helper->getModule()->mid());
       //$block_arr   = \XoopsBlock::getByModule($GLOBALS['xoopsModule']->mid());
        $class       = 'even';
        $cachetimes  = [
            '0'       => _NOCACHE,
            '30'      => sprintf(_SECONDS, 30),
            '60'      => _MINUTE,
            '300'     => sprintf(_MINUTES, 5),
            '1800'    => sprintf(_MINUTES, 30),
            '3600'    => _HOUR,
            '18000'   => sprintf(_HOURS, 5),
            '86400'   => _DAY,
            '259200'  => sprintf(_DAYS, 3),
            '604800'  => _WEEK,
            '2592000' => _MONTH,
        ];
        foreach ($block_arr as $i) {
            $groups_perms = $grouppermHandler->getGroupIds('block_read', $i->getVar('bid'));
            $sql          = 'SELECT module_id FROM ' . $GLOBALS['xoopsDB']->prefix('block_module_link') . ' WHERE block_id=' . $i->getVar('bid');
            $result       = $GLOBALS['xoopsDB']->query($sql);
            $modules      = [];
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $modules[] = (int)$row['module_id'];
            }

            $cachetime_options = '';
            foreach ($cachetimes as $cachetime => $cachetime_name) {
                if ($i->getVar('bcachetime') == $cachetime) {
                    $cachetime_options .= "<option value='$cachetime' selected>$cachetime_name</option>\n";
                } else {
                    $cachetime_options .= "<option value='$cachetime'>$cachetime_name</option>\n";
                }
            }

            $sel0 = $sel1 = $ssel0 = $ssel1 = $ssel2 = $ssel3 = $ssel4 = $ssel5 = $ssel6 = $ssel7 = '';
            if (1 === $i->getVar('visible')) {
                $sel1 = ' checked';
            } else {
                $sel0 = ' checked';
            }
            if (XOOPS_SIDEBLOCK_LEFT === $i->getVar('side')) {
                $ssel0 = ' checked';
            } elseif (XOOPS_SIDEBLOCK_RIGHT === $i->getVar('side')) {
                $ssel1 = ' checked';
            } elseif (XOOPS_CENTERBLOCK_LEFT === $i->getVar('side')) {
                $ssel2 = ' checked';
            } elseif (XOOPS_CENTERBLOCK_RIGHT === $i->getVar('side')) {
                $ssel4 = ' checked';
            } elseif (XOOPS_CENTERBLOCK_CENTER === $i->getVar('side')) {
                $ssel3 = ' checked';
            } elseif (XOOPS_CENTERBLOCK_BOTTOMLEFT === $i->getVar('side')) {
                $ssel5 = ' checked';
            } elseif (XOOPS_CENTERBLOCK_BOTTOMRIGHT === $i->getVar('side')) {
                $ssel6 = ' checked';
            } elseif (XOOPS_CENTERBLOCK_BOTTOM === $i->getVar('side')) {
                $ssel7 = ' checked';
            }
            if ('' === $i->getVar('title')) {
                $title = '&nbsp;';
            } else {
                $title = $i->getVar('title');
            }
            //$name = $i->getVar('name');
            echo "    <tr class='top'>\n"
               . "        <td class='{$class} center'>\n"
               . "            <input type='text' name='title[" . $i->getVar('bid') . "]' value='{$title}'>\n"
               . "        </td>\n"
               . "        <td class='{$class} center' nowrap='nowrap'>\n"
               . "            <div class='center'>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_CENTERBLOCK_LEFT . "'$ssel2>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_CENTERBLOCK_CENTER . "'$ssel3>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_CENTERBLOCK_RIGHT . "'$ssel4>\n"
               . "            </div>\n"
               . "        <div>\n"
               . "                <span style='float:right;'>\n"
               . "                    <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_SIDEBLOCK_RIGHT . "'$ssel1>\n"
               . "                </span>\n"
               . "            <div class='left'>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_SIDEBLOCK_LEFT . "'$ssel0></div>\n"
               . "            </div>\n"
               . "            <div class='center'>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_CENTERBLOCK_BOTTOMLEFT . "'$ssel5>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_CENTERBLOCK_BOTTOM . "'$ssel7>\n"
               . "                <input type='radio' name='side[" . $i->getVar('bid') . "]' value='" . XOOPS_CENTERBLOCK_BOTTOMRIGHT . "'$ssel6>\n"
               . "            </div>\n"
               . "        </td>\n"
               . "        <td class='{$class} center'>\n"
               . "            <input type='text' name='weight[" . $i->getVar('bid') . "]' value='" . $i->getVar('weight') . "' size='5' maxlength='5'>\n"
               . "        </td>\n"
               . "        <td class='{$class} center' nowrap>\n"
               . "            <input type='radio' name='visible[" . $i->getVar('bid') . "]' value='1'$sel1>" . _YES . "&nbsp\n"
               . "            <input type='radio' name='visible[" . $i->getVar('bid') . "]' value='0'$sel0>" . _NO . "\n"
               . "        </td>\n"
               . "        <td class='{$class} center'>\n"
               . "            <select size='5' name='bmodule[" . $i->getVar('bid') . "][]' id='bmodule[" . $i->getVar('bid') . "][]' multiple>";
            foreach ($module_list as $k => $v) {
                echo "                <option value='{$k}'" . (in_array($k, $modules) ? 'selected' : '') . ">{$v}</option>\n";
            }
            echo "            </select>\n"
               . "        </td>\n"
               . "        <td class='{$class} center'>\n"
               . "            <select size='5' name='groups[" . $i->getVar('bid') . "][]' id='groups[" . $i->getVar('bid') . "][]' multiple>\n";
            foreach ($groups as $grp) {
                echo "            <option value='" . $grp->getVar('groupid') . "' " . (in_array($grp->getVar('groupid'), $groups_perms) ? 'selected' : '') . '>' . $grp->getVar('name') . '</option>\n';
            }
            echo "            </select>\n"
               . "        </td>\n";

            // Cache lifetime
            echo "        <td class='{$class} center'><select name='bcachetime[" . $i->getVar('bid') . "]' size='1'>{$cachetime_options}</select></td>\n";

            // Actions
            echo "        <td class='{$class} center'><a href='blocksadmin.php?op=edit&amp;bid=" . $i->getVar('bid') . "'><img src='" . $pathIcon16 . '/edit.png' . "' alt='" . _EDIT . "' title='" . _EDIT . "'></a>&nbsp;\n"
               . "            <a href='blocksadmin.php?op=clone&amp;bid=" . $i->getVar('bid') . "'><img src='" . $pathIcon16 . '/editcopy.png' . "' alt='" . _CLONE . "' title='" . _CLONE . "'></a>\n";
            if ('S' !== $i->getVar('block_type') && 'M' !== $i->getVar('block_type')) {
                echo "            &nbsp;<a href='" . XOOPS_URL . '/modules/system/admin.php?fct=blocksadmin&amp;op=delete&amp;bid=' . $i->getVar('bid') . "'><img src=" . $pathIcon16 . '/delete.png' . " alt='" . _DELETE . "' title='" . _DELETE . "'></a>\n";
            }
            echo "            <input type='hidden' name='oldtitle[" . $i->getVar('bid') . "]' value='" . $i->getVar('title') . "'>\n"
               . "            <input type='hidden' name='oldside[" . $i->getVar('bid') . "]' value='" . $i->getVar('side') . "'>\n"
               . "            <input type='hidden' name='oldweight[" . $i->getVar('bid') . "]' value='" . $i->getVar('weight') . "'>\n"
               . "            <input type='hidden' name='oldvisible[" . $i->getVar('bid') . "]' value='" . $i->getVar('visible') . "'>\n"
               . "            <input type='hidden' name='oldgroups[" . $i->getVar('groups') . "]' value='" . $i->getVar('groups') . "'>\n"
               . "            <input type='hidden' name='oldbcachetime[" . $i->getVar('bid') . "]' value='" . $i->getVar('bcachetime') . "'>\n"
               . "            <input type='hidden' name='bid[" . $i->getVar('bid') . "]' value='" . $i->getVar('bid') . "'>\n"
               . "        </td>\n"
               . "    </tr>\n";
            $class = ('even' === $class) ? 'odd' : 'even';
        }
        echo "    <tr>\n"
           . "        <td class='foot center' colspan='8'>\n"
           . "            <input type='hidden' name='op' value='order'>\n"
           . "            " . $GLOBALS['xoopsSecurity']->getTokenHTML() . "\n"
           . "            <input type='submit' name='submit' value='" . _SUBMIT . "'>\n"
           . "        </td>\n"
           . "    </tr>\n"
           . "</table>\n"
           . "</form>\n"
           . "<br><br>\n";
        break;
    case ('order'):
        if (!$GLOBALS['xoopsSecurity']->check()) {
            $helper->redirect('admin/blocksadmin.php', Constants::REDIRECT_DELAY_MEDIUM, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $bid           = Request::getArray('bid', [], 'POST');
        $oldtitle      = Request::getArray('oldtitle', [], 'POST');
        $title         = Request::getArray('title', [], 'POST');
        $oldweight     = Request::getArray('oldweight', [], 'POST');
        $weight        = Request::getArray('weight', [], 'POST');
        $oldvisible    = Request::getArray('oldvisible', [], 'POST');
        $visible       = Request::getArray('visible', [], 'POST');
        $oldside       = Request::getArray('oldside', [], 'POST');
        $side          = Request::getArray('side', [], 'POST');
        $oldbcachetime = Request::getArray('oldbcachetime', [], 'POST');
        $bcachetime    = Request::getArray('bcachetime', [], 'POST');

        foreach ($bid as $i => $val) {
            if ($oldtitle[$i] != $title[$i] || $oldweight[$i] != $weight[$i]
                || $oldvisible[$i] != $visible[$i]
                || $oldside[$i] != $side[$i]
                || $oldbcachetime[$i] != $bcachetime[$i])
            {
                $myblock = new \XoopsBlock($bid);
                $myblock->setVar('title', $title[$i]);
                $myblock->setVar('weight', $weight[$i]);
                $myblock->setVar('visible', $visible[$i]);
                $myblock->setVar('side', $side[$i]);
                $myblock->setVar('bcachetime', $bcachetime[$i]);
                $myblock->store();
            }
            if (!empty($bmodule[$i]) && count($bmodule[$i]) > 0) {
                $sql = sprintf('DELETE FROM `%s` WHERE block_id = %u', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid[$i]);
                $GLOBALS['xoopsDB']->query($sql);
                if (in_array(0, $bmodule[$i])) {
                    $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid[$i], 0);
                    $GLOBALS['xoopsDB']->query($sql);
                } else {
                    foreach ($bmodule[$i] as $bmid) {
                        $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid[$i], (int)$bmid);
                        $GLOBALS['xoopsDB']->query($sql);
                    }
                }
            }
            $sql = sprintf('DELETE FROM `%s` WHERE gperm_itemid = %u', $GLOBALS['xoopsDB']->prefix('group_permission'), $bid[$i]);
            $GLOBALS['xoopsDB']->query($sql);
            if (!empty($groups[$i])) {
                foreach ($groups[$i] as $grp) {
                    $sql = sprintf("INSERT INTO `%s` (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, 1, 'block_read')", $GLOBALS['xoopsDB']->prefix('group_permission'), $grp, $bid[$i]);
                    $GLOBALS['xoopsDB']->query($sql);
                }
            }
        }
        $helper->redirect('admin/blocksadmin.php', Constants::REDIRECT_DELAY_MEDIUM, constant('CO_' . $moduleDirNameUpper . '_' . 'UPDATE_SUCCESS'));
        break;
    case ('clone'):
        $bid = Request::getInt('bid', 0);
        xoops_cp_header();

        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/blocksadmin', 'system');
        xoops_loadLanguage('admin/groups', 'system');

        //        mpu_adm_menu();
        $myblock = new \XoopsBlock($bid);
        $sql     = 'SELECT module_id FROM ' . $GLOBALS['xoopsDB']->prefix('block_module_link') . ' WHERE block_id=' . (int)$bid;
        $result  = $GLOBALS['xoopsDB']->query($sql);
        $modules = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $modules[] = (int)$row['module_id'];
        }
        $is_custom = ('C' === $myblock->getVar('block_type') || 'E' === $myblock->getVar('block_type'));
        $block     = [
            'title'      => $myblock->getVar('title') . ' Clone',
            'form_title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS_CLONEBLOCK'),
            'name'       => $myblock->getVar('name'),
            'side'       => $myblock->getVar('side'),
            'weight'     => $myblock->getVar('weight'),
            'visible'    => $myblock->getVar('visible'),
            'content'    => $myblock->getVar('content', 'N'),
            'modules'    => $modules,
            'is_custom'  => $is_custom,
            'ctype'      => $myblock->getVar('c_type'),
            'bcachetime' => $myblock->getVar('bcachetime'),
            'op'         => 'clone_ok',
            'bid'        => $myblock->getVar('bid'),
            'edit_form'  => $myblock->getOptions(),
            'template'   => $myblock->getVar('template'),
            'options'    => $myblock->getVar('options'),
        ];
        echo "<a href='" . $helper->url('admin/blocksadmin.php') . "'>" . _AM_BADMIN . "</a>&nbsp;<span class='bold'>&raquo;&raquo;</span>&nbsp;" . _AM_SYSTEM_BLOCKS_CLONEBLOCK . "<br><br>\n";
        require_once __DIR__ . '/blockform.php';
        $form->display();
        break;
    case ('edit'):
        $bid = Request::getInt('bid', 0, 'POST');
        xoops_cp_header();

        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/blocksadmin', 'system');
        xoops_loadLanguage('admin/groups', 'system');
        //        mpu_adm_menu();
        $myblock = new \XoopsBlock($bid);
        $sql     = 'SELECT module_id FROM ' . $GLOBALS['xoopsDB']->prefix('block_module_link') . ' WHERE block_id=' . $bid;
        $result  = $GLOBALS['xoopsDB']->query($sql);
        $modules = [];
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $modules[] = (int)$row['module_id'];
        }
        $is_custom = ('C' === $myblock->getVar('block_type') || 'E' === $myblock->getVar('block_type'));
        $block     = [
            'title'      => $myblock->getVar('title'),
            'form_title' => constant('CO_' . $moduleDirNameUpper . '_' . 'BLOCKS_EDITBLOCK'),
            //        'name'       => $myblock->getVar('name'),
            'side'       => $myblock->getVar('side'),
            'weight'     => $myblock->getVar('weight'),
            'visible'    => $myblock->getVar('visible'),
            'content'    => $myblock->getVar('content', 'N'),
            'modules'    => $modules,
            'is_custom'  => $is_custom,
            'ctype'      => $myblock->getVar('c_type'),
            'bcachetime' => $myblock->getVar('bcachetime'),
            'op'         => 'edit_ok',
            'bid'        => $myblock->getVar('bid'),
            'edit_form'  => $myblock->getOptions(),
            'template'   => $myblock->getVar('template'),
            'options'    => $myblock->getVar('options'),
        ];
        echo "<a href='" . $helper->url('admin/blocksadmin.php') . "'>" . _AM_BADMIN . "</a>&nbsp;<span class='bold'>&raquo;&raquo;</span>&nbsp;" . _AM_SYSTEM_BLOCKS_EDITBLOCK . "<br><br>\n";
        require_once __DIR__ . '/blockform.php';
        $form->display();
        break;
    case('edit_ok'):
        $bid        = Request::getInt('bid', 0);
        $btitle     = Request::getString('btitle');
        $bside      = Request::getInt('bside', 0);
        $bweight    = Request::getInt('bweight', 0);
        $bvisible   = Request::getInt('bvisible', 0);
        $bcachetime = Request::getInt('bcachetime', 0);
        $bmodule    = Request::getArray('bmodule', []); // array of integers
        $options    = Request::getArray('options', []);
        $groups     = Request::getArray('groups', []);

        $myblock = new \XoopsBlock($bid);
        $myblock->setVar('title', $btitle);
        $myblock->setVar('weight', $bweight);
        $myblock->setVar('visible', $bvisible);
        $myblock->setVar('side', $bside);
        $myblock->setVar('bcachetime', $bcachetime);
        $myblock->store();

        if (!empty($bmodule) && count($bmodule) > 0) {
            $sql = sprintf('DELETE FROM `%s` WHERE block_id = %u', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid);
            $GLOBALS['xoopsDB']->query($sql);
            if (in_array(0, $bmodule)) {
                $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid, 0);
                $GLOBALS['xoopsDB']->query($sql);
            } else {
                foreach ($bmodule as $bmid) {
                    $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $GLOBALS['xoopsDB']->prefix('block_module_link'), $bid, (int)$bmid);
                    $GLOBALS['xoopsDB']->query($sql);
                }
            }
        }
        $sql = sprintf('DELETE FROM `%s` WHERE gperm_itemid = %u', $GLOBALS['xoopsDB']->prefix('group_permission'), $bid);
        $GLOBALS['xoopsDB']->query($sql);
        if (!empty($groups)) {
            foreach ($groups as $grp) {
                $sql = sprintf("INSERT INTO `%s` (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (%u, %u, 1, 'block_read')", $GLOBALS['xoopsDB']->prefix('group_permission'), $grp, $bid);
                $GLOBALS['xoopsDB']->query($sql);
            }
        }
        $helper->redirect('admin/blocksadmin.php', Constants::REDIRECT_DELAY_MEDIUM, constant('CO_' . $moduleDirNameUpper . '_' . 'UPDATE_SUCCESS'));
        break;
    case ('clone_ok'):

        $bid        = Request::getInt('bid', 0);
        $bside      = Request::getInt('bside', 0);
        $bweight    = Request::getInt('weight', 0);
        $bvisible   = Request::getInt('visible', 0);
        $bcachetime = Request::getInt('bcachetime', 0);
        $bmodule    = Request::getInt('bmodule', $helper->getModule()->mid());
        $options    = Request::getArray('options', []);

        xoops_loadLanguage('admin', 'system');
        xoops_loadLanguage('admin/blocksadmin', 'system');
        xoops_loadLanguage('admin/groups', 'system');

        $block = new \XoopsBlock($bid);
        $clone = $block->xoopsClone();
        if (empty($bmodule)) {
            xoops_cp_header();
            xoops_error(sprintf(_AM_NOTSELNG, _AM_VISIBLEIN));
            xoops_cp_footer();
            exit();
        }
        $clone->setVar('side', $bside);
        $clone->setVar('weight', $bweight);
        $clone->setVar('visible', $bvisible);
        //$clone->setVar('content', $_POST['bcontent']);
        $clone->setVar('title', Request::getString('btitle', '', 'POST'));
        $clone->setVar('bcachetime', $bcachetime);
        if (isset($options) && (count($options) > 0)) {
            $options = implode('|', $options);
            $clone->setVar('options', $options);
        }
        $clone->setVar('bid', 0);
        if ('C' === $block->getVar('block_type') || 'E' === $block->getVar('block_type')) {
            $clone->setVar('block_type', 'E');
        } else {
            $clone->setVar('block_type', 'D');
        }
        $newid = $clone->store();
        if (!$newid) {
            xoops_cp_header();
            $clone->getHtmlErrors();
            xoops_cp_footer();
            exit();
        }
        if ('' !== $clone->getVar('template')) {
            /** @var \XoopsTplfileHandler $tplfileHandler */
            $tplfileHandler = xoops_getHandler('tplfile');
            $btemplate      = $tplfileHandler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $bid);
            if (count($btemplate) > 0) {
                $tplclone = $btemplate[0]->xoopsClone();
                $tplclone->setVar('tpl_id', 0);
                $tplclone->setVar('tpl_refid', $newid);
                $tplfileHandler->insert($tplclone);
            }
        }
        foreach ($bmodule as $bmid) {
            $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('block_module_link') . ' (block_id, module_id) VALUES (' . $newid . ', ' . $bmid . ')';
            $GLOBALS['xoopsDB']->query($sql);
        }
        $groups = &$GLOBALS['xoopsUser']->getGroups();
        $count  = count($groups);
        for ($i = 0; $i < $count; ++$i) {
            $sql = 'INSERT INTO ' . $GLOBALS['xoopsDB']->prefix('group_permission') . ' (gperm_groupid, gperm_itemid, gperm_modid, gperm_name) VALUES (' . $groups[$i] . ', ' . $newid . ", 1, 'block_read')";
            $GLOBALS['xoopsDB']->query($sql);
        }
        $helper->redirect('admin/blocksadmin.php?op=listar', Constants::REDIRECT_DELAY_MEDIUM, _AM_DBUPDATED);
        break;
}
require_once __DIR__ . '/admin_footer.php';
