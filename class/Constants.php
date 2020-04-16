<?php

namespace XoopsModules\Smallworld;

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

//defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Constants Interface
 */
interface Constants
{
    /**#@+
     * Constant definition
     */

    /**
     * Do not allow
     */
    const DISALLOW = 0;
    /**
     * confirm not ok to take action
     */
    const CONFIRM_NOT_OK = 0;
    /**
     * confirm ok to take action
     */
    const CONFIRM_OK = 1;
    /**
     * no delay XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_NONE = 0;
    /**
     * short XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_SHORT = 1;
    /**
     * medium XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_MEDIUM = 3;
    /**
     * long XOOPS redirect delay (in seconds)
     */
    const REDIRECT_DELAY_LONG = 7;
    /**
     * default user Id
     */
    const DEFAULT_UID = 0;
    /**
     * default message Id
     */
    const DEFAULT_MESSAGEID = 0;
    /**
     * no date found
     */
    const NO_DATE = false;
    /**
     * female gender
     */
    const FEMALE = 1;
    /**
     * male gender
     */
    const MALE = 2;
    /**
     * gender unknown
     */
    const GENDER_UNKNOWN = 0;
    /**
     * User status not XOOPS user, has no SW profile
     */
    const PROFILE_NONE = 0;
    /**
     * User status is XOOPS user, has no SW profile
     */
    const PROFILE_XOOPS_ONLY = 1;
    /**
     * User status is XOOPS user, has SW profile
     */
    const PROFILE_HAS_BOTH = 2;
    /**
    * Relationship - Married
    */
    const RELATIONSHIP_MARRIED = 0;
    /**
     * Relationship - Engaged
     */
    const RELATIONSHIP_ENGAGED = 1;
    /**
     * Relationship - Single
     */
    const RELATIONSHIP_SINGLE = 2;
    /**
     * Relationship - In a
     */
    const RELATIONSHIP_ISIN = 3;
    /**
     * Relationship - Open
     */
    const RELATIONSHIP_OPEN = 4;
    /**
     * Relationship - Complicated
     */
    const RELATIONSHIP_COMPLICATED = 5;
    /**
     * is not User - used for both SW and XOOPS
     */
    const IS_NOT_USER = 0;
    /**
     * is user - used for both SW and XOOPS
     */
    const IS_USER = 1;
    /**
     * No access
     */
    const NO_ACCESS = 0;
    /**
     * Has access
     */
    const HAS_ACCESS = 1;
    /**
     * friend status approved
     */
    const FRIEND_STATUS_APPROVED = 2;
    /**
     * friend status pending
     */
    const FRIEND_STATUS_PENDING = 1;
    /**
     * active user limit (for dB search)
     */
    const USER_LIMIT = 20;
    /**#@-*/
}
