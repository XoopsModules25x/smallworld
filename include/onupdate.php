<?php

/**
 * @return bool
 */
function xoops_module_update_smallworld()
{
    $db  = XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'UPDATE ' . $db->prefix('config') . " SET conf_value = 1 WHERE conf_name = 'smallworldprivorpub'";
    $db->queryF($sql);
    return true;
}
