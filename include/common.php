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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team
 */

use Xoopsmodules\smallworld;

//require_once  __DIR__ . '/../../../mainfile.php';

require_once __DIR__ . '/../class/Helper.php';
require_once __DIR__ . '/../class/Utility.php';

require_once __DIR__ . '/../class/SmallworldAdmin.php';
require_once __DIR__ . '/../class/SmallWorldDB.php';
require_once __DIR__ . '/../class/SmallWorldUser.php';
require_once __DIR__ . '/../class/SmallWorldImages.php';
require_once __DIR__ . '/../class/SmallWorldProfile.php';
require_once __DIR__ . '/../class/WallUpdates.php';
require_once __DIR__ . '/../class/SmallWorldForm.php';
require_once __DIR__ . '/../class/SmallworldUploadHandler.php';
require_once __DIR__ . '/../class/SmallWorldFriends.php';
require_once __DIR__ . '/../class/SmallWorldMail.php';


$db = \XoopsDatabaseFactory::getDatabase();
$helper = smallworld\Helper::getInstance();

/** @var \Xoopsmodules\smallworld\Utility $utility */
$utility = new smallworld\Utility();
///** @var \Xoopsmodules\smallworld\InstructionHandler $instructionHandler */
//$instructionHandler = new smallworld\InstructionHandler($db);
///** @var \Xoopsmodules\smallworld\CategoryHandler $categoryHandler */
//$categoryHandler = new smallworld\CategoryHandler($db);
///** @var \Xoopsmodules\smallworld\PageHandler $pageHandler */
//$pageHandler = new smallworld\PageHandler($db);
$admin = new smallworld\SmallworldAdmin();
$d     = new smallworld\SmallWorldDoSync;
$check = new smallworld\SmallWorldUser;
$db    = new smallworld\SmallWorldDB;
$Wall  = new smallworld\WallUpdates();

$helper->loadLanguage('common');

$pathIcon16    = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = Xmf\Module\Admin::iconUrl('', 32);
$pathModIcon16 = $helper->getModule()->getInfo('modicons16');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$debug = false;

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $xoopsTpl = new \XoopsTpl();
}

$moduleDirName = basename(dirname(__DIR__));
$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);

// Local icons path
$GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
$GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
