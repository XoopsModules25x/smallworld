<?php

namespace XoopsModules\Smallworld;

/*
 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Smallworld
 *
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @author       XOOPS Module Development Team
 * @copyright    Copyright (c) 2001-2020 {@link https://xoops.org XOOPS Project}
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.16
 */

/**
 * Smallworld User Object Handler
 *
 */
class SwUserHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Class constructor
     *
     * @param \XoopsDatabase $db
     * @return void
     */
    function __construct(&$db = null)
    {
        parent::__construct($db, 'smallworld_user', SwUser::class, 'id', 'username');
    }
    /**
     * Check if user has profile
     *
     * Returns profile type:
     *  Constants::PROFILE_NONE - no profile (XOOPS or SW),
     *  || Constants::PROFILE_XOOPS_ONLY - XOOPS user but no SW profile,
     *  || Constants::PROFILE_HAS_BOTH - has both
     *
     * @param int $userId  XOOPS user id
     * @return int
     */
    public function checkIfProfile($userId)
    {
        $userId = (int)$userId;
        $type   = Constants::PROFILE_NONE; // init profile type
        if (Constants::DEFAULT_UID < $userId) {
            // now check to see if it's a real XOOPS user
            $xUser = new \XoopsUser($userId);
            if ($xUser instanceof \XoopsUser) {
                // valid XOOPS user, see if there's a SW profile for them
                $userCount = $this->getCount(new \Criteria('userid', $userId));
                // If \XoopsUser but no smallworld profile set to XOOPS only, otherwise they have a SW profile too
                $type = (0 == $userCount) ? Constants::PROFILE_XOOPS_ONLY : Constants::PROFILE_HAS_BOTH;
            }
        }
        return $type;
    }
    /**
     * Get SwUser name from userid
     *
     * @param int $userId
     * @return string
     */
    public function getName($userId)
    {
        $name  = '';
        $criteria = new \Criteria('userid', (int)$userId);
        $criteria->setLimit(1);
        $swUserArray = $this->getAll($criteria, ['username'], false);
        if (0 < count($swUserArray)) {
            $swUser = array_pop($swUserArray);
            $name = $swUser['username'];
        }

        return $name;
    }
}
