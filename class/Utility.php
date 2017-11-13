<?php namespace Xoopsmodules\smallworld;

use Xmf\Request;
use Xoopsmodules\smallworld\common;

require_once __DIR__ . '/common/VersionChecks.php';
require_once __DIR__ . '/common/ServerStats.php';
require_once __DIR__ . '/common/FilesManagement.php';

require_once __DIR__ . '/../include/common.php';

/**
 * Class Utility
 */
class Utility
{
    use common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use common\ServerStats; // getServerStats Trait

    use common\FilesManagement; // Files Management Trait
}
