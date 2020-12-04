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

use XoopsModules\Smallworld;

require_once dirname(dirname(dirname(__DIR__))) . ('/mainfile.php');
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';

$GLOBALS['xoopsLogger']->activated = false;
//$userString = '{"relat":"", "partner":"", "gender":"", "politic":"", "religion":""}';
$userString = '{"relat":"0", "partner":"", "gender":"0", "politic":"0", "religion":"0"}';

if ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    $swUserHandler = Smallworld\Helper::getInstance()->getHandler('SwUser');
    $user          = $swUserHandler->get($GLOBALS['xoopsUser']->uid()); // get the user
    if ($user instanceof \XoopsModules\Smallworld\SwUser) {
        $userVals      = $user->getValues();
        $userString = "{\"relat\":\"{$userVals['relationship']}\", \"partner\":\"{$userVals['partner']}\", \"gender\":\"{$userVals['gender']}\", \"politic\":\"{$userVals['politic']}\", \"religion\":\"{$userVals['religion']}\"}";
    }
/*
    $swDB     = new Smallworld\SwDatabase();
    $id       = $GLOBALS['xoopsUser']->uid();
    $relat    = $swDB->getVar($id, 'relationship');
    $partner  = $swDB->getVar($id, 'partner');
    $gender   = $swDB->getVar($id, 'gender');
    $politic  = $swDB->getVar($id, 'politic');
    $religion = $swDB->getVar($id, 'religion');
    header('Content-type: "application/json"');
    echo "{\"relat\":\"{$relat}\", \"partner\":\"{$partner}\", \"gender\":\"{$gender}\", \"politic\":\"{$politic}\", \"religion\":\"{$religion}\"}";
*/
}

header('Content-type: "application/json"');
echo $userString;