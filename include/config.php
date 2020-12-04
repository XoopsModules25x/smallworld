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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

if (!defined($moduleDirNameUpper . '_DIRNAME')) {
    //if (!defined(constant($moduleDirNameUpper . '_DIRNAME'))) {
    define($moduleDirNameUpper . '_DIRNAME', $GLOBALS['xoopsModule']->dirname());
    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($moduleDirNameUpper . '_DIRNAME'));
    define($moduleDirNameUpper . '_URL', XOOPS_URL . '/modules/' . constant($moduleDirNameUpper . '_DIRNAME'));
    define($moduleDirNameUpper . '_ADMIN', constant($moduleDirNameUpper . '_URL') . '/admin/index.php');
    define($moduleDirNameUpper . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . constant($moduleDirNameUpper . '_DIRNAME'));
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', constant($moduleDirNameUpper . '_URL') . '/assets/images/logoModule.png');
    define($moduleDirNameUpper . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
}

//Configurator
return (object)[
    'name'          => mb_strtoupper($moduleDirName) . ' Module Configurator',
    'paths'         => [
        'dirname'    => $moduleDirName,
        'admin'      => XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/admin',
        //'path'       => XOOPS_ROOT_PATH . '/modules/' . $moduleDirName,
        //'url'        => XOOPS_URL . '/modules/' . $moduleDirName,
        'uploadPath' => XOOPS_UPLOAD_PATH . '/' . $moduleDirName,
        'uploadUrl'  => XOOPS_UPLOAD_URL . '/' . $moduleDirName,
    ],
    'uploadFolders' => [
        constant($moduleDirNameUpper . '_UPLOAD_PATH'),
        constant($moduleDirNameUpper . '_UPLOAD_PATH') . '/category',
        constant($moduleDirNameUpper . '_UPLOAD_PATH') . '/screenshots',
        XOOPS_UPLOAD_PATH . '/flags',
    ],
    'blankFiles'    => [
        constant($moduleDirNameUpper . '_UPLOAD_PATH'),
        constant($moduleDirNameUpper . '_UPLOAD_PATH') . '/category',
        constant($moduleDirNameUpper . '_UPLOAD_PATH') . '/screenshots',
        XOOPS_UPLOAD_PATH . '/flags',
    ],

    'templateFolders' => [
        '/templates/',
        '/templates/blocks/',
        '/templates/admin/',
    ],
    'oldFiles'        => [
        /*
        '/sql/wflinks.sql',
        '/class/wfl_lists.php',
        '/class/class_thumbnail.php',
        '/vcard.php',
        */
    ],
    'oldFolders'      => [
        '/images',
        '/css',
        '/js',
        //'/tcpdf',
    ],
    'modCopyright'    => "<a href='https://xoops.org' title='XOOPS Project' target='_blank'>
                     <img src='" . constant($moduleDirNameUpper . '_AUTHOR_LOGOIMG') . '\' alt=\'XOOPS Project\' /></a>',
];
