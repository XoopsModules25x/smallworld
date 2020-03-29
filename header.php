<?php

use XoopsModules\Smallworld;

include __DIR__ . '/preloads/autoloader.php';

require_once dirname(dirname(__DIR__)) . '/mainfile.php';
$moduleDirName = basename(__DIR__);

require XOOPS_ROOT_PATH . '/header.php';

$moduleDirName = basename(__DIR__);

$helper = Smallworld\Helper::getInstance();

$modulePath = XOOPS_ROOT_PATH . '/modules/' . $moduleDirName;

$myts = \MyTextSanitizer::getInstance();

if(!$GLOBALS['xoopsTpl'] instanceof \xos_opal_Theme) {
    require $GLOBALS['xoops']->path('class/theme.php');
    $GLOBALS['xoTheme'] = new \xos_opal_Theme();
}

// Load language files
$helper->loadLanguage('main');

if (!$GLOBALS['xoopsTpl'] instanceof \XoopsTpl) {
    require $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}
