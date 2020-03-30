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

use XoopsModules\Smallworld;

$moduleDirName = basename(dirname(__DIR__));

require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../include/common.php';

require_once __DIR__ . '/upgrade.php';

// Check upgrade files
smallworld_doUpgrade();

$myts = \MyTextSanitizer::getInstance();
$db   = \XoopsDatabaseFactory::getDatabase();

//if (($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
if ($GLOBALS['xoopsUser'] instanceof \XoopsUser) {
    if (!$helper->isUserAdmin()) {
        $helper->redirect(XOOPS_URL . '/', 3, _NOPERM);
    }
} else {
    $helper->redirect(XOOPS_URL . '/user.php', 1, _NOPERM);
}

/** @var Xmf\Module\Admin $adminObject */
$adminObject = \Xmf\Module\Admin::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}

$pathIcon16    = \Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = \Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

// Load language files
$helper->loadLanguage('admin');
$helper->loadLanguage('modinfo');
$helper->loadLanguage('main');
