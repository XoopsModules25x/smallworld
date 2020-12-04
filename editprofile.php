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
use XoopsModules\Smallworld\Constants;

require_once __DIR__ . '/header.php';

$GLOBALS['xoopsOption']['template_main'] = 'smallworld_userprofile_edittemplate.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/** @var \XoopsModules\Smallworld\Helper $helper */
require_once $helper->path('include/functions.php');
require_once $helper->path('include/arrays.php');

/**
 * Defined via inclusion of ./include/arrays.php
 *
 * @var array $arr0,
 * @var array $arr01
 * @var array $arr02
 * @var array $arr03
 * @var array $arr04
 * @var array $arr05
 * @var array $arr06
 * @var array $arr7
 */

if (!$GLOBALS['xoopsUser'] || (!$GLOBALS['xoopsUser'] instanceof \XoopsUser)) {
    redirect_header(XOOPS_URL . '/user.php', Constants::REDIRECT_DELAY_SHORT, _NOPERM);
}

$id            = $GLOBALS['xoopsUser']->uid();
$swUserHandler = $helper->getHandler('SwUser');
$profile       = $swUserHandler->checkIfProfile($id);

// Check if inspected userid -> redirect to userprofile and show admin countdown
$inspect = smallworld_isInspected($id);
if ('yes' === $inspect['inspect']) {
    $helper->redirect('userprofile.php?username=' . $GLOBALS['xoopsUser']->getVar('uname'), Constants::REDIRECT_DELAY_NONE);
}

if (Constants::PROFILE_HAS_BOTH > $profile) {
    $helper->redirect('register.php');
}

$GLOBALS['xoopsTpl']->assign('check', $profile);
$item = new Smallworld\Form();
//$swDB = new Smallworld\SwDatabase();
$swUser = $swUserHandler->get($id);
if ($swUser instanceof \XoopsModules\Smallworld\SwUser) {
    $r = $swUser->getValues();
/*
    $cdb    = 'SELECT * FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_user') . " WHERE userid = '" . $id . "'";
    $result = $GLOBALS['xoopsDB']->queryF($cdb);
    while (false !== ($r = $GLOBALS['xoopsDB']->fetchArray($result))) {
*/
    // ------------ PERSONAL INFO ------------ //

    // Real name
    if (0 != smallworldGetValfromArray('realname', 'smallworldusethesefields')) {
        $realname = $item->input('realname', 'realname', 'realname', $size = 30, $preset = $r['realname']);
        $GLOBALS['xoopsTpl']->append('realname', $realname);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_realname', 'no');
    }

    // Dropdown for gender
    if (0 != smallworldGetValfromArray('gender', 'smallworldusethesefields')) {
        $gender = $item->dropdown('gender', $arr0, $r['gender']);
        $GLOBALS['xoopsTpl']->append('gender', $gender);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_gender', 'no');
    }

    // Selectbox for "interested in gender(s)"
    if (0 != smallworldGetValfromArray('interestedin', 'smallworldusethesefields')) {
        $nr          = $r['intingender'];
        //$nr          = unserialize($r['intingender']);
        $intInGender = $item->retrieveRadio('intingender', $arr01, $nr);
        $GLOBALS['xoopsTpl']->append('intingender', $intInGender);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_interestedin', 'no');
    }

    // Dropdown for marital status
    if (0 != smallworldGetValfromArray('relationshipstatus', 'smallworldusethesefields')) {
        $relationshipstatus = $item->dropdown('relationship', $arr02, $r['relationship']);
        $GLOBALS['xoopsTpl']->append('relationshipstatus', $relationshipstatus);

        // Partner. Only shown if marital status is married, it's complicated, engaged)
        $partner = $item->input('partner', 'partner', 'partner', $size = '30', stripslashes($r['partner']));
        $GLOBALS['xoopsTpl']->append('partner', $partner);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_relationshipstatus', 'no');
    }

    if (0 != smallworldGetValfromArray('lookingfor', 'smallworldusethesefields')) {
        $nr1         = $r['searchrelat'];
        //$nr1         = unserialize($r['searchrelat']);
        $searchrelat = $item->retrieveRadio('searchrelat', $arr03, $nr1);
        $GLOBALS['xoopsTpl']->append('searchrelat', $searchrelat);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_lookingfor', 'no');
    }

    if (0 != smallworldGetValfromArray('birthday', 'smallworldusethesefields')) {
        // Select Birthday dd-mm-Y
        $birthday = $item->input('birthday', 'birthday', 'birthday', $size = '12', $preset = stripslashes(smallworld_UsToEuroDate($r['birthday'])));
        $GLOBALS['xoopsTpl']->append('birthdaydate', $birthday);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_birthday', 'no');
    }

    // Select Hometown or Enter new
    if (0 != smallworldGetValfromArray('birthplace', 'smallworldusethesefields')) {
        $birthplace = $item->input('birthplace', 'birthplace', 'birthplace', $size = '50', $preset = stripslashes($r['birthplace']));
        $GLOBALS['xoopsTpl']->append('birthplace', $birthplace);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_birthplace', 'no');
    }

    // Dropdown politics
    if (0 != smallworldGetValfromArray('politicalview', 'smallworldusethesefields')) {
        $politic = $item->dropdown('politic', $arr04, $r['politic']);
        $GLOBALS['xoopsTpl']->append('politic', $politic);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_political', 'no');
    }

    // Dropdown Religion
    if (0 != smallworldGetValfromArray('religiousview', 'smallworldusethesefields')) {
        $religion = $item->dropdown('religion', $arr05, $r['religion']);
        $GLOBALS['xoopsTpl']->append('religion', $religion);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_religion', 'no');
    }

    // ------------ CONTACT INFO ------------ //
    // Add email test
    if (0 != smallworldGetValfromArray('emails', 'smallworldusethesefields')) {
        $nr2       = $r['emailtype'];
        //$nr2       = unserialize($r['emailtype']);
        $emailtext = '';
        foreach ($nr2 as $k => $v) {
            $nr2id     = 'email-' . $k;
            $emailtext .= $item->input_add('smallworld_add2', 'email', 'emailtype', '.smallworld_clone2', 20, $addmore = null, $preset = stripslashes($v), $nr2id);
            // $emailtext .= $item->input_add('smallworld_add2','email','emailtype','.smallworld_clone2',20,$addmore=null,$preset=stripslashes($v));
            $emailtext .= "<span class='smallworld_remove' id='emailremove'><a href='javascript:void(0);' id='emailremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
        }
        $emailtext .= "<a class='smallworld_addemail' href='javascript:void(0);' id='emailAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
        $GLOBALS['xoopsTpl']->append('emailtext', $emailtext);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_emails', 'no');
    }

    //Add screen names (usernames for facebook etc etc)
    if (0 != smallworldGetValfromArray('screennames', 'smallworldusethesefields')) {
        $nr3    = $r['screenname_type'];
        $nr4    = $r['screenname'];
        //$nr3    = unserialize($r['screenname_type']);
        //$nr4    = unserialize($r['screenname']);
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
        $GLOBALS['xoopsTpl']->append('screenname', $screenname);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_screennames', 'no');
    }

    // Mobilephone
    if (0 != smallworldGetValfromArray('mobile', 'smallworldusethesefields')) {
        $mobile = $item->input('mobile', 'mobile', 'mobile', 12, $preset = stripslashes($r['mobile']));
        $GLOBALS['xoopsTpl']->append('mobile', $mobile);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_mobile', 'no');
    }

    // Landphone
    if (0 != smallworldGetValfromArray('landphone', 'smallworldusethesefields')) {
        $phone = $item->input('phone', 'phone', 'phone', 12, $preset = stripslashes($r['phone']));
        $GLOBALS['xoopsTpl']->append('phone', $phone);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_landphone', 'no');
    }

    // Adress
    if (0 != smallworldGetValfromArray('streetadress', 'smallworldusethesefields')) {
        $adress = $item->input('adress', 'adress', 'adress', $size = '50', $preset = stripslashes($r['adress']));
        $GLOBALS['xoopsTpl']->append('adress', $adress);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_adress', 'no');
    }

    if (0 != smallworldGetValfromArray('presentcity', 'smallworldusethesefields')) {
        $present_city = $item->input('present_city', 'present_city', 'present_city', 50, $preset = stripslashes($r['present_city']));
        $GLOBALS['xoopsTpl']->append('present_city', $present_city);

        $present_country = $item->input('present_country', 'present_country', 'present_country', $size = '50', $preset = stripslashes($r['present_country']));
        $GLOBALS['xoopsTpl']->append('present_country', $present_country);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_city', 'no');
    }

    if (0 == smallworldGetValfromArray('website', 'smallworldusethesefields')) {
        $GLOBALS['xoopsTpl']->assign('show_website', 'no');
    }

    // ------------ INTERESTS ------------ //

    // Textarea for interests
    //textarea($name, $id, $title, $rows, $cols, $class)
    if (0 != smallworldGetValfromArray('interests', 'smallworldusethesefields')) {
        $interests = $item->textarea('interests', 'interests', _SMALLWORLD_INTERESTS, 1, 20, 'favourites', $preset = stripslashes($r['interests']));
        $GLOBALS['xoopsTpl']->append('interests', $interests);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_interests', 'no');
    }

    // Textarea for Music
    if (0 != smallworldGetValfromArray('favouritemusic', 'smallworldusethesefields')) {
        $music = $item->textarea('music', 'music', _SMALLWORLD_MUSIC, 1, 20, 'favourites', $preset = stripslashes($r['music']));
        $GLOBALS['xoopsTpl']->append('music', $music);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_music', 'no');
    }

    // Textarea for Tvshow
    if (0 != smallworldGetValfromArray('favouritetvshows', 'smallworldusethesefields')) {
        $tvshow = $item->textarea('tvshow', 'tvshow', _SMALLWORLD_TVSHOW, 1, 20, 'favourites', $preset = stripslashes($r['tvshow']));
        $GLOBALS['xoopsTpl']->append('tvshow', $tvshow);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_tv', 'no');
    }

    // Textarea for Movie
    if (0 != smallworldGetValfromArray('favouritemovies', 'smallworldusethesefields')) {
        $movie = $item->textarea('movie', 'movie', _SMALLWORLD_MOVIE, 1, 20, 'favourites', $preset = stripslashes($r['movie']));
        $GLOBALS['xoopsTpl']->append('movie', $movie);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_movies', 'no');
    }

    // Textarea for Books
    if (0 != smallworldGetValfromArray('favouritebooks', 'smallworldusethesefields')) {
        $books = $item->textarea('books', 'books', _SMALLWORLD_BOOKS, 1, 20, 'favourites', $preset = stripslashes($r['books']));
        $GLOBALS['xoopsTpl']->append('books', $books);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_books', 'no');
    }

    // Textarea for About me
    if (0 != smallworldGetValfromArray('aboutme', 'smallworldusethesefields')) {
        $aboutme = $item->textarea('aboutme', 'aboutme', _SMALLWORLD_ABOUTME, 2, 20, 'favourites', $preset = stripslashes($r['aboutme']));
        $GLOBALS['xoopsTpl']->append('aboutme', $aboutme);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_aboutme', 'no');
    }

    // ------------ SCHOOL ------------ //

    //School name
    if (0 != smallworldGetValfromArray('education', 'smallworldusethesefields')) {
        $nr5    = $r['school_type'];
        $nr6    = $r['school'];
        $nr7    = $r['schoolstart'];
        $nr8    = $r['schoolstop'];
        //$nr5    = unserialize($r['school_type']);
        //$nr6    = unserialize($r['school']);
        //$nr7    = unserialize($r['schoolstart']);
        //$nr8    = unserialize($r['schoolstop']);
        $school = '';
        foreach ($nr5 as $k => $v) {
            $school .= $item->school_add('smallworld_add3', 'school', 'school_type', '.smallworld_clone3', $arr7, _SMALLWORLD_ADDMORE, $selected = stripslashes($nr6[$k]), $preset = $v, $selectedstart = $nr7[$k], $selectedstop = $nr8[$k]);
            $school .= "<span class='smallworld_remove2' id='schoolremove'>";
            $school .= "<a href='javascript:void(0);' id='schoolremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
        }
        $school .= "<a class='smallworld_addschool' href='javascript:void(0);' id='schoolAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
        $GLOBALS['xoopsTpl']->append('school', $school);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_school', 'no');
    }

    //Jobs
    if (0 != smallworldGetValfromArray('employment', 'smallworldusethesefields')) {
        $nr9  = $r['employer'];
        $nr10 = $r['position'];
        $nr11 = $r['jobstart'];
        $nr12 = $r['jobstop'];
        $nr13 = array_map('stripslashes', $r['description']);
        //$nr9  = unserialize($r['employer']);
        //$nr10 = unserialize($r['position']);
        //$nr11 = unserialize($r['jobstart']);
        //$nr12 = unserialize($r['jobstop']);
        //$nr13 = unserialize(stripslashes($r['description']));
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
                $selectedstart = (!empty($nr11[$k])) ? date('Y', $nr11[$k]) : '',
                $selectedstop = (!empty($nr12[$k])) ? date('Y', $nr12[$k]) : '',
                $description = $nr13[$k]
            );
            $job .= "<span class='smallworld_remove3' id='jobremove'>";
            $job .= "<a href='javascript:void(0);' id='jobremovelnk'>" . _SMALLWORLD_REMOVE . '</a><br></span>';
        }
        $job .= "<a class='smallworld_addjob' href='javascript:void(0);' id='jobAdd'>" . _SMALLWORLD_ADDMORE . '</a><br><br>';
        $GLOBALS['xoopsTpl']->append('job', $job);
    } else {
        $GLOBALS['xoopsTpl']->assign('show_jobs', 'no');
    }

    // Create hidden forms for birthcity
    $birthplace_lat     = $item->hidden('birthplace_lat', 'birthplace_lat', $preset = stripslashes($r['birthplace_lat']));
    $birthplace_lng     = $item->hidden('birthplace_lng', 'birthplace_lng', $preset = stripslashes($r['birthplace_lng']));
    $birthplace_country = $item->hidden('birthplace_country', 'birthplace_country', $preset = stripslashes($r['birthplace_country']));
    $GLOBALS['xoopsTpl']->append('birthplace_lat', $birthplace_lat);
    $GLOBALS['xoopsTpl']->append('birthplace_lng', $birthplace_lng);
    $GLOBALS['xoopsTpl']->append('birthplace_country', $birthplace_country);

    // Create hidden forms for present city
    $present_lat = $item->hidden('present_lat', 'present_lat', $preset = stripslashes($r['present_lat']));
    $present_lng = $item->hidden('present_lng', 'present_lng', $preset = stripslashes($r['present_lng']));
    $GLOBALS['xoopsTpl']->append('present_lat', $present_lat);
    $GLOBALS['xoopsTpl']->append('present_lng', $present_lng);

    $GLOBALS['xoopsTpl']->append('smallworld_register_title', _SMALLWORLD_REGRISTATION_TITLE);
    $GLOBALS['xoopsTpl']->assign('smallworld_beforesubmit', _SMALLWORLD_TEXTBEFORESUBMIT);
    $GLOBALS['xoopsTpl']->assign('smallworld_save', _SMALLWORLD_SUBMIT);
    $GLOBALS['xoopsTpl']->assign('smallworld_user_website', $r['website']);
}
require_once XOOPS_ROOT_PATH . '/footer.php';
