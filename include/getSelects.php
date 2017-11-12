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

require_once __DIR__ . '/../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoopsModule, $xoopsLogger;
$xoopsLogger->activated = false;
if ($xoopsUser) {
    $db       = new SmallWorldDB;
    $id       = $xoopsUser->getVar('uid');
    $relat    = $db->getVar($id, 'relationship');
    $partner  = $db->getVar($id, 'partner');
    $gender   = $db->getVar($id, 'gender');
    $politic  = $db->getVar($id, 'politic');
    $religion = $db->getVar($id, 'religion');
    header('Content-type: "application/json"');
    echo "{\"relat\":\"$relat\", \"partner\":\"$partner\", \"gender\":\"$gender\", \"politic\":\"$politic\", \"religion\":\"$religion\"}";
} else {
    header('Content-type: "application/json"');
    echo '{"relat":"", "partner":"", "gender":"", "politic":"", "religion":""}';
}
