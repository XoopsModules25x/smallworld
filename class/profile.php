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

use XoopsModules\Smallworld\Constants;

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
class Profile
{
    /**
     * @Show user
     * @param int $id
     */
    public function showUser($id)
    {
        global $arr04, $arr05;
        $id = (int)$id;
        if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser'] instanceof \XoopsUser) {
            $myName = $GLOBALS['xoopsUser']->uname(); // My name
            $swDB   = new SwDatabase();
            $wall   = new WallUpdates();
			$GLOBALS['xoopsLogger']->activated = true;
            /**
             * @var \XoopsModules\Smallworld\Helper $helper
             * @var \XoopsModules\Smallworld\SwUserHandler $swUserHandler
             */
            $helper        = Helper::getInstance();
            $swUserHandler = $helper->getHandler('SwUser');

            $rArray = $swUserHandler->getAll(new \Criteria('userid', $id), null, false);
            foreach ($rArray as $r) {
            //$cdb    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . $id . "'";
            //$result = $GLOBALS['xoopsDB']->queryF($cdb);
            //while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
                $uname           = $r['username'];
                $realname        = $r['realname'];
                $membersince     = date('d-m-Y', $GLOBALS['xoopsUser']->user_regdate());;
                $birthday        = smallworld_UsToEuroDate($r['birthday']);
                $cnt_bday        = smallworldNextBDaySecs($r['birthday']);
                $birthcity       = $r['birthplace'];
                $email           = $GLOBALS['xoopsUser']->email();
                $country         = $GLOBALS['xoopsUser']->user_from();
                $signature       = $GLOBALS['xoopsUser']->user_sig();
                $messenger       = $GLOBALS['xoopsUser']->user_msnm();
                $totalposts      = $wall->countMsges($id);
                $membersince     = date('m-d-Y', $GLOBALS['xoopsUser']->user_regdate());
                $usersratedplus  = $swDB->countUsersRates($id, 'up');
                $usersratedminus = $swDB->countUsersRates($id, 'down');
                $workfull        = $swDB->getJobsToDiv($id);
                //$workArray       = unserialize($r['employer']);
                //$work            = "<a href='javascript:void(0)' id='_smallworld_workmore'>" . $workArray[0] . ' (' . _SMALLWORLD_MORE . ')</a>';
                $work            = "<a href='javascript:void(0)' id='_smallworld_workmore'>" . $r['employer'][0] . ' (' . _SMALLWORLD_MORE . ')</a>';
                $educationfull   = $swDB->getSchoolToDiv($id);
                //$educationArray  = unserialize($r['school_type']);
                //$education       = "<a href='javascript:void(0)' id='_smallworld_educationmore'>" . $educationArray[0] . ' (' . _SMALLWORLD_MORE . ')</a>';
                $education       = "<a href='javascript:void(0)' id='_smallworld_educationmore'>" . $r['school_type'][0] . ' (' . _SMALLWORLD_MORE . ')</a>';
                $lng             = $r['birthplace_lng'];
                $latt            = $r['birthplace_lat'];
                $country         = $r['birthplace_country'];
                $rank            = $GLOBALS['xoopsUser']->rank();
                $rank_title      = $rank['title'];
                if (isset($rank['image'])) {
                    $rank_image = "<img class='center' src='" . XOOPS_UPLOAD_URL . '/' . $rank['image'] . "'>";
                } else {
                    $rank_image = '';
                }
                $commentsrating = "<img src='" . $helper->url('assets/images/like.png') . "' height='10px' width='10px'" . '> ' . $usersratedplus
                                . " <img src='" . $helper->url('assets/images/dislike.png') . "' height='10px' width='10px'" . '> ' . $usersratedminus;
                $lastlogin      = $GLOBALS['xoopsUser']->getVar('last_login');

                $gender = $r['gender'];
                if (Constants::MALE == $gender) {
                    $heorshe  = _SMALLWORLD_HE;
                    $hisorher = _SMALLWORLD_HIS;
                }
                if (Constants::FEMALE == $gender) {
                    $heorshe  = _SMALLWORLD_SHE;
                    $hisorher = _SMALLWORLD_HER;
                }
                if (Constants::GENDER_UNKNOWN == (int)$gender) {
                    $heorshe  = _SMALLWORLD_HEORSHE;
                    $hisorher = _SMALLWORLD_HISHER;
                }
                $avatar          = $swUserHandler->gravatar($id);
                $avatar_size     = smallworld_getImageSize(80, 100, $swUserHandler->getAvatarLink($id, $avatar));
                $avatar_highwide = smallworld_imageResize($avatar_size[0], $avatar_size[1], 100);
                $user_img        = "<img src='" . $swUserHandler->getAvatarLink($id, $avatar) . "' id='smallworld_user_img' " . $avatar_highwide . '>';

                $currentcity = $r['present_city'];
                $currlng     = $r['present_lng'];
                $currlatt    = $r['present_lat'];
                $currcountry = $r['present_country'];
				$cityname	 = $r['birthplace'];

                // experimental. Set javascript var using php getVar()
                $js = "<script type='text/javascript'>";
                $js .= 'var smallworld_currlng = ' . $currlng . "\n";
                $js .= 'var smallworld_currlatt = ' . $currlatt . "\n";
				//$js .= 'var smallworld_currcity = ' . $currentcity . "\n";
                $js .= 'var smallworld_birthlng = ' . $lng . "\n";
                $js .= 'var smallworld_birthlatt = ' . $latt . "\n";
				$js .= "var cityname_birth = '" . $cityname . "'\n";
                $js .= '</script>';
                echo $js;

                $relationship = $r['relationship'];
                $spouseExists = $swUserHandler->spouseExists($r['partner']);

                switch ((int)$relationship) {
                    case Constants::RELATIONSHIP_MARRIED:
                        $status = _SMALLWORLD_ISMARRIED;
                        $spouse = ($spouseExists > 0) ? "<a href='" . $helper->url('userprofile.php?username=' . $r['partner']) . "' target='_self'>" . $r['partner'] . '</a>' : $r['partner'];
                        break;
                    case Constants::RELATIONSHIP_ENGAGED:
                        $status = _SMALLWORLD_ISENGAGED;
                        $spouse = ($spouseExists > 0) ? "<a href='" . $helper->url('userprofile.php?username=' . $r['partner']) . "' target='_self'>" . $r['partner'] . '</a>' : $r['partner'];
                        break;
                    case Constants::RELATIONSHIP_SINGLE:
                        $status = _SMALLWORLD_ISSINGLE;
                        $spouse = '';
                        break;
                    case Constants::RELATIONSHIP_ISIN:
                        $status = _SMALLWORLD_INRELATIONSHIP;
                        $spouse = ($spouseExists > 0) ? "<a href='" . $helper->url('userprofile.php?username=' . $r['partner']) . "' target='_self'>" . $r['partner'] . '</a>' : $r['partner'];
                        break;
                    case Constants::RELATIONSHIP_OPEN:
                        $status = _SMALLWORLD_OPENRELATIONSHIP;
                        $spouse = ($spouseExists > 0) ? "<a href='" . $helper->url('userprofile.php?username=' . $r['partner']) . "' target='_self'>" . $r['partner'] . '</a>' : $r['partner'];
                        break;
                    case Constants::RELATIONSHIP_COMPLICATED:
                        $status = _SMALLWORLD_ISCOMPLICATED;
                        $spouse = ($spouseExists > 0) ? "<a href='" . $helper->url('userprofile.php?username=' . $r['partner']) . "' target='_self'>" . $r['partner'] . '</a>' : $r['partner'];
                        break;
                }
                //Personal info
                $aboutme  = $r['aboutme'];
                $religion = $arr05[$r['religion']];
                $politic  = $arr04[$r['politic']];

                //Interests
                $favbook      = $r['books'];
                $favmusic     = $r['music'];
                $favmovie     = $r['movie'];
                $favtvshow    = $r['tvshow'];
                $favinterests = $r['interests'];

                // Contact and adresses
                //$email      = unserialize($r['emailtype']);
                $email      = $r['emailtype'];
                $screenname = $swDB->getScreennamesToDiv($id);
                $phone      = ('' == $r['phone'] || 0 == $r['phone']) ? 'xxx-xxx-xxxx' : $r['phone'];
                $gsm        = ('' == $r['mobile'] || 0 == $r['mobile']) ? 'xxx-xxx-xxxx' : $r['mobile'];
                $adress     = $r['adress'];
                $website    = $r['website'];
                $age        = smallworld_Birthday($r['birthday']);
            }

            //SW_CheckIfUser ($userid);;
            $GLOBALS['xoopsTpl']->assign([
                'userid'        => $id,
            // ----- LANG DEFINES ------
                'username'      => $uname,
                'MyUserName'    => $myName,
                'avatar'        => $user_img,
                'realname'      => $realname,
                'birthday'      => $birthday,
                'nextBDay'      => $cnt_bday,
            //
                'usersratinf'   => $commentsrating,
            //
                'age'           => $age,
                'birthcity'     => $birthcity,
                'country'       => $country,
                'heorshe'       => $heorshe,
                'hisorher'      => $hisorher,
            //
                'membersince'   => $membersince,
                'msn'           => $messenger,
                'website'       => $website,
                'totalposts'    => $totalposts,
                'ranktitle'     => $rank_title,
                'rankimage'     => $rank_image,
                'lastlogin'     => date('d-m-Y', $lastlogin),
                'signature'     => $signature,
                'currentcity'   => $currentcity,
                'currcity'      => $currentcity,
                'currcountry'   => $currcountry,
            //
                'education'     => $education,
                'educationfull' => $educationfull,
            //
                'work'          => $work,
                'workfull'      => $workfull,
            //
                'relationship'  => $status,
                'status'        => $status,
                'spouse'        => $spouse,
                'aboutme'       => $aboutme,
                'lang.avatar'   => _SMALLWORLD_AVATAR,
            // Pers info language define
                'politic'       => $politic,
                'religion'      => $religion,
            //
                'favbook'       => $favbook,
                'favmusic'      => $favmusic,
                'favmovie'      => $favmovie,
                'favtvshow'     => $favtvshow,
                'favinterests'  => $favinterests,

                'email'         => $email,
                'screenname'    => $screenname,
                'phone'         => $phone,
                'mobile'        => $gsm,
                'adress'        => $adress,
            //
                'website'       => $website,
                'addsomeinfo'   => _SMALLWORLD_ADDSOMEINFO,
                'pagename'      => 'profile'
            ]);
        }
    }
}
