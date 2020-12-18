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

use XoopsModules\Smallworld\Constants;

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
    public function __construct(&$db = null)
    {
        /** {@internal - note use of userid for \XoopsPersistableObjectHandler::identifierName
         *  This was done so that {@see \XoopsHandler::getList() will return the userids since it's not the primary key in
         *  the dB table but is unique and more useful than returning the `username` column. }}
         */
        parent::__construct($db, 'smallworld_user', SwUser::class, 'id', 'userid');
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
     * Get SwUser userid
     *
     * @param int $userId
     * @return bool|\XoopsModules\Smallworld\SwUser false if not exists
     */
    public function getByUserId($userId)
    {
        $swUser  = false;
        $criteria = new \Criteria('userid', (int)$userId);
        $criteria->setLimit(1);
        $swUserObjArray = $this->getAll($criteria);
        if (0 < count($swUserObjArray)) {
            $swUser = array_pop($swUserObjArray);
        }
        return $swUser;
    }
    /**
     * Get SwUser name from userid
     *
     * @param int $userId
     * @return string
     */
    public function getName($userId)
    {
        $swUser = $this->getByUserId($userId);
        $username = (false !== $swUser) ? $swUser->getVar('username') : '';

        return $username;
    }
    /**
     * Get SwUser userid from username
     *
     * @param string $userName
     * @return int   SW userid, 0 if not found
     */
    public function getByName($username = '')
    {
        $userId = 0;
        if (!empty($username)) {
            $criteria = new \Criteria('username', $username);
            $criteria->setLimit(1);
            $swUserArray = $this->getAll($criteria, ['userid'], false);
            if (0 < count($swUserArray)) {
                $swUser = array_pop($swUserArray);
                $userId = (int)$swUser['userid'];
            }
        }

        return $userId;
    }
    /**
     * Does partner/spouse exist as a SwUser
     *
     * @param string $name
     * @return bool
     */
    public function spouseExists($username)
    {
        $exists = $this->getByName($username);
        return (0 < $exists) ? true : false;
    }
    /**
     * Get array of `userid`s
     *
     * @return array
     */
    public function allUsers()
    {
        $r = $this->getList();
        return count($r) ? smallworld_array_flatten($r, 0) : [];
        /*
        $retVal = [];
        $sql    = 'SELECT userid FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . ' ORDER BY userid';
        $result = $GLOBALS['xoopsDB']->queryF($sql);
        $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
        $criteria = new \Criteria('');
        $criteria->setSort('userid');
        $criteria->order = 'ASC';
        $users  = $this->getAll($criteria, ['userid'], false);

        if (0 !== $i) {
            $data = [];
            while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $data[] = $r;
            }
            $retVal = smallworld_array_flatten($data, 0);
        }

        return $retVal;
        //redirect_header(XOOPS_URL . "/modules/smallworld/register.php");
         */
    }

    /**
     * Get user image based on uid
     *
     * @param int $uid
     * @return string
     */
    public function gravatar($userId)
    {
        $avatar = '';
        $swUser = $this->getByUserId($userId);
        $image  = (false !== $swUser) ? $swUser->getVar('userimage') : '';
        //        $image  = ('' === $image || 'blank.gif' === $image) ? $this->getAvatarLink($userId, $image) : $image;
        $image  = (empty($image) || 'blank.gif' === $image) ? $this->getAvatarLink($userId, $image) : $image;
        $type   = [
            Constants::IMAGE_TYPE_JPG  => 'jpg',
            Constants::IMAGE_TYPE_JPEG => 'jpeg',
            Constants::IMAGE_TYPE_PNG  => 'png',
            Constants::IMAGE_TYPE_GIF  => 'gif'
        ];

        $parts  = explode('.', $image);
        $ext    = (is_array($parts) && 0 < count($parts)) ? array_pop($parts) : '';
        $avatar = in_array(mb_strtolower($ext), $type) ? $image : '';
        return $avatar;
    }
    /**
     * Check image extension and users gender.
     *
     * If image is legal image extension return avatar, else return default gender based image
     * @param int    $userId
     * @param string $image
     * @return string
     */
    public function getAvatarLink($userId, $image)
    {
        $swUser = $this->getByUserId($userId);
        $gender = (false !== $swUser) ? $swUser->getVar('gender') : Constants::GENDER_UNKNOWN;
        $link   = (preg_match('/avatars/i', $image)) ? XOOPS_UPLOAD_URL . '/' . $image : $image;
        $ext    = pathinfo(mb_strtolower($image), PATHINFO_EXTENSION);

        if (!in_array($ext, ['jpg', 'bmp', 'gif', 'png', 'jpeg']) || "" == $image || 'avatars/blank.gif' == $image) {
            switch ($gender) {
                case Constants::FEMALE:
                    $pict = 'ano_woman.png';
                    break;
                case Constants::MALE:
                    $pict = 'ano_man.png';
                    break;
                case Constants::GENDER_UNKNOWN:
                default:
                    $pict = 'genderless.png';
                    break;
            }
            $link = Helper::getInstance()->url("assets/images/{$pict}");
        }
        return $link;
    }
}
