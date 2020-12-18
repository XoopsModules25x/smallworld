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

use Xmf\Request;
use XoopsModules\Smallworld;
require_once __DIR__ . '/header.php';

require_once __DIR__ . '/../../mainfile.php';
$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_edittemplate.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/smallworld/include/arrays.php';
//require_once XOOPS_ROOT_PATH . '/modules/smallworld/class/class_collector.php';
global $xoopsUser, $xoopsTpl, $xoopsDB, $xoTheme;

if ($xoopsUser) {
    $id      = $xoopsUser->getVar('uid');
    $check   = new smallworld\SmallWorldUser;
    $profile = $check->CheckIfProfile($id);

    // Check if inspected userid -> redirect to userprofile and show admin countdown
    $inspect = Smallworld_isInspected($id);
    if ('yes' === $inspect['inspect']) {
        redirect_header('userprofile.php?username=' . $xoopsUser->getVar('uname'), 1);
    }

    if (2 == $profile) {
        $xoopsTpl->assign('check', $profile);
        $item = new smallworld\SmallWorldForm;
        $db   = new smallworld\SmallWorldDB;

        $cdb    = 'SELECT * FROM ' . $xoopsDB->prefix('smallworld_user') . " WHERE userid = '" . $id . "'";
        $result = $xoopsDB->queryF($cdb);
        $cnt    = $xoopsDB->getRowsNum($result);
        while ($r = $xoopsDB->fetchArray($result)) {
            // ------------ PERSONAL INFO ------------ //

            // Real name
            if (0 != smallworldGetValfromArray('realname', 'smallworldusethesefields')) {
                $realname = $item->input('realname', 'realname', 'realname', $size = 30, $preset = $r['realname']);
                $xoopsTpl->append('realname', $realname);
            } else {
                $xoopsTpl->assign('show_realname', 'no');
            }

            // Dropdown for gender
            if (0 != smallworldGetValfromArray('gender', 'smallworldusethesefields')) {
                $gender = $item->dropdown('gender', $arr0, $r['gender']);
                $xoopsTpl->append('gender', $gender);
            } else {
                $xoopsTpl->assign('show_gender', 'no');
            }

            // Selectbox for "interested in gender(s)"
            if (0 != smallworldGetValfromArray('interestedin', 'smallworldusethesefields')) {
                $nr          = unserialize($r['intingender']);
                $intInGender = $item->RetrieveRadio('intingender', $arr01, $nr, $selected = null);
                $xoopsTpl->append('intingender', $intInGender);
            } else {
                $xoopsTpl->assign('show_interestedin', 'no');
            }

            // Dropdown for marital status
            if (0 != smallworldGetValfromArray('relationshipstatus', 'smallworldusethesefields')) {
                $relationshipstatus = $item->dropdown('relationship', $arr02, 0);
                $xoopsTpl->append('relationshipstatus', $relationshipstatus);

                // Partner. Only shown if marital status is married, it's complicated, engaged)
                $partner = $item->input('partner', 'partner', 'partner', $size = '30', stripslashes($r['partner']));
                $xoopsTpl->append('partner', $partner);
            } else {
                $xoopsTpl->assign('show_relationshipstatus', 'no');
            }

            if (0 != smallworldGetValfromArray('lookingfor', 'smallworldusethesefields')) {
                $nr1         = unserialize($r['searchrelat']);
                $searchrelat = $item->RetrieveRadio('searchrelat', $arr03, $nr1, $selected = null);
                $xoopsTpl->append('searchrelat', $searchrelat);
            } else {
                $xoopsTpl->assign('show_lookingfor', 'no');
            }

            if (0 != smallworldGetValfromArray('birthday', 'smallworldusethesefields')) {
                // Select Birthday dd-mm-Y
                $birthday = $item->input('birthday', 'birthday', 'birthday', $size = '12', $preset = stripslashes(Smallworld_UsToEuroDate($r['birthday'])));
                $xoopsTpl->append('birthdaydate', $birthday);
            } else {
                $xoopsTpl->assign('show_birthday', 'no');
            }

            // Select Hometown or Enter new
            if (0 != smallworldGetValfromArray('birthplace', 'smallworldusethesefields')) {
                $birthplace = $item->input('birthplace', 'birthplace', 'birthplace', $size = '50', $preset = stripslashes($r['birthplace']));
                $xoopsTpl->append('birthplace', $birthplace);
            } else {
                $xoopsTpl->assign('show_birthplace', 'no');
            }

            // Dropdown politics
            if (0 != smallworldGetValfromArray('politicalview', 'smallworldusethesefields')) {
                $politic = $item->dropdown('politic', $arr04, 0);
                $xoopsTpl->append('politic', $politic);
            } else {
                $xoopsTpl->assign('show_political', 'no');
            }

            // Dropdown Religion
            if (0 != smallworldGetValfromArray('religiousview', 'smallworldusethesefields')) {
                $religion = $item->dropdown('religion', $arr05, 0);
                $xoopsTpl->append('religion', $religion);
            } else {
                $xoopsTpl->assign('show_religion', 'no');
            }

            // ------------ CONTACT INFO ------------ //
            // Add email test
            if (0 != smallworldGetValfromArray('emails', 'smallworldusethesefields')) {
                $nr2       = unserialize($r['emailtype']);
                $emailtext = '';
                foreach ($nr2 as $k => $v) {
                    $nr2id     = 'email-' . $k;
                    $emailtext .= $item->input_add('smallworld_add2', 'email', 'emailtype', '.smallworld_clone2', 20, $addmore = null, $preset = stripslashes($v), $nr2id);
                    // $emailtext .= $item->input_add('smallworld_add2','email','emailtype','.smallworld_clone2',20,$addmore=null,$preset=stripslashes($v));
                    $emailtext .= "<span class='smallworld_remove' id='emailremove'><a href='javascript:void(0);' id='emailremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
                }
                $emailtext .= "<a class='smallworld_addemail' href='javascript:void(0);' id='emailAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
                $xoopsTpl->append('emailtext', $emailtext);
            } else {
                $xoopsTpl->assign('show_emails', 'no');
            }

            //Add screen names (usernames for facebook etc etc)
            if (0 != smallworldGetValfromArray('screennames', 'smallworldusethesefields')) {
                $nr3    = unserialize($r['screenname_type']);
                $nr4    = unserialize($r['screenname']);
                $count1 = count($nr3);
                $cnt1   = 0;

                // Drop down for screen names
                $screenname = '';
                foreach ($nr3 as $k => $v) {
                    if ($cnt1 < $count1 - 1) {
                        $addmore1 = '';
                    } else {
                        $addmore1 = _SMALLWORLD_ADDMORE;
                    }
                    $screenname .= $item->dropdown_add('smallworld_add', 'screenname', 'screenname_type', '.smallworld_clone', $arr06, $addmore1, $selected = stripslashes($nr4[$k]), $preset = stripslashes($v));
                    $screenname .= "<span class='smallworld_remove' id='screennameremove'>";
                    $screenname .= "<a href='javascript:void(0);' id='screennameremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
                    ++$cnt1;
                }
                $screenname .= "<a class='smallworld_addscreenname' href='javascript:void(0);' id='screennameAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
                $xoopsTpl->append('screenname', $screenname);
            } else {
                $xoopsTpl->assign('show_screennames', 'no');
            }

            // Mobilephone
            if (0 != smallworldGetValfromArray('mobile', 'smallworldusethesefields')) {
                $mobile = $item->input('mobile', 'mobile', 'mobile', 12, $preset = stripslashes($r['mobile']));
                $xoopsTpl->append('mobile', $mobile);
            } else {
                $xoopsTpl->assign('show_mobile', 'no');
            }

            // Landphone
            if (0 != smallworldGetValfromArray('landphone', 'smallworldusethesefields')) {
                $phone = $item->input('phone', 'phone', 'phone', 12, $preset = stripslashes($r['phone']));
                $xoopsTpl->append('phone', $phone);
            } else {
                $xoopsTpl->assign('show_landphone', 'no');
            }

            // Adress
            if (0 != smallworldGetValfromArray('streetadress', 'smallworldusethesefields')) {
                $adress = $item->input('adress', 'adress', 'adress', $size = '50', $preset = stripslashes($r['adress']));
                $xoopsTpl->append('adress', $adress);
            } else {
                $xoopsTpl->assign('show_adress', 'no');
            }

            if (0 != smallworldGetValfromArray('presentcity', 'smallworldusethesefields')) {
                $present_city = $item->input('present_city', 'present_city', 'present_city', 50, $preset = stripslashes($r['present_city']));
                $xoopsTpl->append('present_city', $present_city);

                $present_country = $item->input('present_country', 'present_country', 'present_country', $size = '50', $preset = stripslashes($r['present_country']));
                $xoopsTpl->append('present_country', $present_country);
            } else {
                $xoopsTpl->assign('show_city', 'no');
            }

            if (0 == smallworldGetValfromArray('website', 'smallworldusethesefields')) {
                $xoopsTpl->assign('show_website', 'no');
            }

            // ------------ INTERESTS ------------ //

            // Textarea for interests
            //textarea($name, $id, $title, $rows, $cols, $class)
            if (0 != smallworldGetValfromArray('interests', 'smallworldusethesefields')) {
                $interests = $item->textarea('interests', 'interests', _SMALLWORLD_INTERESTS, 1, 20, 'favourites', $preset = stripslashes($r['interests']));
                $xoopsTpl->append('interests', $interests);
            } else {
                $xoopsTpl->assign('show_interests', 'no');
            }

            // Textarea for Music
            if (0 != smallworldGetValfromArray('favouritemusic', 'smallworldusethesefields')) {
                $music = $item->textarea('music', 'music', _SMALLWORLD_MUSIC, 1, 20, 'favourites', $preset = stripslashes($r['music']));
                $xoopsTpl->append('music', $music);
            } else {
                $xoopsTpl->assign('show_music', 'no');
            }

            // Textarea for Tvshow
            if (0 != smallworldGetValfromArray('favouritetvshows', 'smallworldusethesefields')) {
                $tvshow = $item->textarea('tvshow', 'tvshow', _SMALLWORLD_TVSHOW, 1, 20, 'favourites', $preset = stripslashes($r['tvshow']));
                $xoopsTpl->append('tvshow', $tvshow);
            } else {
                $xoopsTpl->assign('show_tv', 'no');
            }

            // Textarea for Movie
            if (0 != smallworldGetValfromArray('favouritemovies', 'smallworldusethesefields')) {
                $movie = $item->textarea('movie', 'movie', _SMALLWORLD_MOVIE, 1, 20, 'favourites', $preset = stripslashes($r['movie']));
                $xoopsTpl->append('movie', $movie);
            } else {
                $xoopsTpl->assign('show_movies', 'no');
            }

            // Textarea for Books
            if (0 != smallworldGetValfromArray('favouritebooks', 'smallworldusethesefields')) {
                $books = $item->textarea('books', 'books', _SMALLWORLD_BOOKS, 1, 20, 'favourites', $preset = stripslashes($r['books']));
                $xoopsTpl->append('books', $books);
            } else {
                $xoopsTpl->assign('show_books', 'no');
            }

            // Textarea for About me
            if (0 != smallworldGetValfromArray('aboutme', 'smallworldusethesefields')) {
                $aboutme = $item->textarea('aboutme', 'aboutme', _SMALLWORLD_ABOUTME, 2, 20, 'favourites', $preset = stripslashes($r['aboutme']));
                $xoopsTpl->append('aboutme', $aboutme);
            } else {
                $xoopsTpl->assign('show_aboutme', 'no');
            }

            // ------------ SCHOOL ------------ //

            //School name
            if (0 != smallworldGetValfromArray('education', 'smallworldusethesefields')) {
                $nr5    = unserialize($r['school_type']);
                $nr6    = unserialize($r['school']);
                $nr7    = unserialize($r['schoolstart']);
                $nr8    = unserialize($r['schoolstop']);
                $school = '';
                foreach ($nr5 as $k => $v) {
                    $school .= $item->school_add('smallworld_add3', 'school', 'school_type', '.smallworld_clone3', $arr7, _SMALLWORLD_ADDMORE, $selected = stripslashes($nr6[$k]), $preset = $v, $selectedstart = $nr7[$k], $selectedstop = $nr8[$k]);
                    $school .= "<span class='smallworld_remove2' id='schoolremove'>";
                    $school .= "<a href='javascript:void(0);' id='schoolremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
                }
                $school .= "<a class='smallworld_addschool' href='javascript:void(0);' id='schoolAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
                $xoopsTpl->append('school', $school);
            } else {
                $xoopsTpl->assign('show_school', 'no');
            }

            //Jobs
            if (0 != smallworldGetValfromArray('employment', 'smallworldusethesefields')) {
                $nr9  = unserialize($r['employer']);
                $nr10 = unserialize($r['position']);
                $nr11 = unserialize($r['jobstart']);
                $nr12 = unserialize($r['jobstop']);
                $nr13 = unserialize(stripslashes($r['description']));
                $job  = '';
                foreach ($nr9 as $k => $v) {
                    $job .= $item->job(
                        'smallworld_add4',
                        'job',
                        'job_type',
                        '.smallworld_clone4',
                        _SMALLWORLD_ADDMORE,
                        $employer = stripslashes($v),
                        $position = stripslashes($nr10[$k]),
                        $selectedstart = ('' != $nr11[$k]) ? date('Y', $nr11[$k]) : '',
                                       $selectedstop = ('' != $nr12[$k]) ? date('Y', $nr12[$k]) : '',
                        $description = $nr13[$k]
                    );
                    $job .= "<span class='smallworld_remove3' id='jobremove'>";
                    $job .= "<a href='javascript:void(0);' id='jobremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
                }
                $job .= "<a class='smallworld_addjob' href='javascript:void(0);' id='jobAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
                $xoopsTpl->append('job', $job);
            } else {
                $xoopsTpl->assign('show_jobs', 'no');
            }

            // Create hidden forms for birthcity
            $birthplace_lat     = $item->hidden('birthplace_lat', 'birthplace_lat', $preset = stripslashes($r['birthplace_lat']));
            $birthplace_lng     = $item->hidden('birthplace_lng', 'birthplace_lng', $preset = stripslashes($r['birthplace_lng']));
            $birthplace_country = $item->hidden('birthplace_country', 'birthplace_country', $preset = stripslashes($r['birthplace_country']));
            $xoopsTpl->append('birthplace_lat', $birthplace_lat);
            $xoopsTpl->append('birthplace_lng', $birthplace_lng);
            $xoopsTpl->append('birthplace_country', $birthplace_country);

            // Create hidden forms for present city
            $present_lat = $item->hidden('present_lat', 'present_lat', $preset = stripslashes($r['present_lat']));
            $present_lng = $item->hidden('present_lng', 'present_lng', $preset = stripslashes($r['present_lng']));
            $xoopsTpl->append('present_lat', $present_lat);
            $xoopsTpl->append('present_lng', $present_lng);

            $xoopsTpl->append('smallworld_register_title', _SMALLWORLD_REGRISTATION_TITLE);
            $xoopsTpl->assign('smallworld_beforesubmit', _SMALLWORLD_TEXTBEFORESUBMIT);
            $xoopsTpl->assign('smallworld_save', _SMALLWORLD_SUBMIT);
            $xoopsTpl->assign('smallworld_user_website', $r['website']);
        }
    } elseif ($profile < 2) {
        redirect_header(XOOPS_URL . '/modules/smallworld/register.php');
    }
} else {
    redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
