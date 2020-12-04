<?php

namespace XoopsModules\Smallworld;

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
 * @package      \XoopsModules\Smallworld
 * @license      GNU GPL (https://www.gnu.org/licenses/gpl-2.0.html/)
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @link         https://github.com/XoopsModules25x/smallworld
 * @since        1.0
 */

 /**
 * Form Class
 *
 * creates HTML entities to display in forms
 *
 */
class Form
{
    /**
     * Create a dropdown select
     *
     * @param string $name
     * @param array  $options
     * @param string $selected (optional)
     * @param string $sep (optional)
     * @return string
     */
    public function dropdown($name, array $options, $selected = null, $sep = '<br>')
    {
        $dropdown = "<select name='{$name}' id='{$name}'>\n";
        foreach ($options as $key => $option) {
            $select    = $selected == $key ? ' selected' : '';
            $dropdown .= "<option value='{$key}'{$selected}>{$option}</option>\n";
        }
        $dropdown .= "</select>{$sep}";

        return $dropdown;
    }

    /**
     * Create a radio select
	 *
     * @param string $name
     * @param array  $options
     * @param string $selected (optional)
     * @param string $sep (optional)
     * @return string
     */
    public function radio($name, array $options, $selected = null, $sep = '<br>')
    {
        $selected = $selected;
        $form     = '';
        $i = 0;
        foreach ($options as $value => $option) {
            $select = $selected == $value ? ' checked' : '';
            $form   .= "<label for='{$name}-{$i}'><input type='radio' name='{$name}[]' id='{$name}-{$i}' value='{$value}'{$select}> "
            . "{$option}</label>{$sep}\n";
//            $form   .= "<input type='checkbox' name='{$name}[]' id='{$name}-{$i}' value='{$value}'{$select}> "
//            . "<label for='{$name}-{$i}'>{$option}</label>{$sep}\n";
            $i++;
        }

        return $form;
    }

    /**
	 * Create a HTML checkbox element
	 *
     * @param string $name
     * @param array  $options
     * @param array  $valuearray
     * @param string $selected (optional)
     * @param string $sep (optional)
     * @return string
     */
    public function retrieveRadio($name, $options, $valuearray, $selected = null, $sep = '<br>')
    {
        $form = '';
        $a    = count($options) - 1;
        for ($i = 0; $i <= $a; ++$i) {
            if (in_array($i, $valuearray)) {
                $form .= "<input type='checkbox' id='{$name}-{$i}' name='{$name}[]' value='{$i}' checked> "
                . "<label for='{$name}-{$i}'>{$options[$i]}</label>{$sep}";
            } else {
                $form .= "<input type='checkbox' id='{$name}-{$i}' name='{$name}[]' value='{$i}'> "
                . "<label for='{$name}-{$i}'>{$options[$i]}</label>{$sep}";
            }
        }

        return $form;
    }

    /**
	 * Create a HTML text input element
	 *
     * @param string $name
     * @param string $id
     * @param string $class
     * @param int    $size   (optional)
     * @param string $preset (optional)
     * @return string
     */
    public function input($name, $id, $class, $size = null, $preset = null)
    {
        $s    = $size ?: '50px';
        $data = "<input type='text' size='" . $s . "' name='" . $name . "' id='" . $id . "' class='" . $class . "' value='" . $preset . "'>";

        return $data;
    }

    /**
	 * Create a HTML hidden element
	 *
     * @param string $name
     * @param string $id
     * @param string $preset (optional)
     * @return string
     */
    public function hidden($name, $id, $preset = null)
    {
        $data = "<input type='hidden' name='" . $name . "' value='" . $preset . "' >";

        return $data;
    }

    /**
	 * Returns simple text string
	 *
     * @param string $value
     * @return string
     */
    public function simpleText($value)
    {
        $data = $value;

        return $data;
    }

    /**
	 * Create a HTML select (dropdown) element
	 *
     * @param string  $class
     * @param string  $name
     * @param         $name2
     * @param string  $rel
     * @param array   $options
     * @param string  $textmore
     * @param string  $selected (optional)
     * @param string  $preset   (optional)
     * @return string
     */
    public function dropdown_add($class, $name, $name2, $rel, array $options, $textmore, $selected = null, $preset = null)
    {
        $dropdown = '<span id="' . $name . '"><input type="text" name="' . $name2 . '[]" value="' . $preset . '">';
        $dropdown .= '<select class="smallworld_select" name="' . $name . '[]" id="' . $name . '">' . '<br>';
        foreach ($options as $key => $option) {
            $select   = $selected == $key ? ' selected' : null;
            $dropdown .= '<option value="' . $key . '"' . $select . '>' . $option . '</option>' . '<br>';
        }
        $dropdown .= '</select></span>';

        return $dropdown;
    }

    /**
     * @param string  $class
     * @param string  $name
     * @param         $name2
     * @param string  $rel
     * @param int     $size
     * @param string  $textmore
     * @param string  $preset (optional)
     * @param string  $id     (optional)
     * @return string
     */
    public function input_add($class, $name, $name2, $rel, $size, $textmore, $preset = null, $id = null)
    {
        $s    = $size ?: '50px';
        $i    = $id ?: '';
        $data = "<span id='" . $name . "'><input type='text' size='" . $s . "' name='" . $name2 . "[]' value='" . $preset . "' id='" . $i . "'></span>";

        return $data;
    }

    /**
     * @param string $name
     * @param string $id
     * @param string $title
     * @param string $rows
     * @param string $cols
     * @param string $class
     * @param string $preset (optional)
     * @return string
     * @return string
     */
    public function textarea($name, $id, $title, $rows, $cols, $class, $preset = null)
    {
        return "<textarea name='" . $name . "' id='" . $id . "'  title='" . $title . "' rows='" . $rows . "' cols='" . $cols . "' class='" . $class . "'>" . $preset . '</textarea>';
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $name2
     * @param string $rel
     * @param array  $options
     * @param string $textmore
     * @param string $selected      (optional)
     * @param string $preset        (optional)
     * @param string $selectedstart (optional)
     * @param string $selectedstop  (optional)
     * @return string
     */
    public function school_add(
        $class,
        $name,
        $name2,
        $rel,
        array $options,
        $textmore,
        $selected = null,
        $preset = null,
        $selectedstart = null,
        $selectedstop = null
    ) {
        $dropdown = '<div id="' . $name . '"><p class="smallworld_clonebreaker">' . _SMALLWORLD_SCHOOLNAME . '<input class="school" type="text" value="' . $preset . '" name="' . $name2 . '[]">'
                  . '<br><br>' . _SMALLWORLD_SCHOOLTYPE . '<select class="school" name="' . $name . '[]" id="' . $name . '"">' . '<br>';
        foreach ($options as $key => $option) {
            $select   = $selected == $key ? ' selected="selected"' : null;
            $dropdown .= '<option  class="school" value="' . $key . '"' . $select . '>' . $option . '</option>' . '<br>';
        }
        $dropdown .= '</select>'
                   . '<br><br>'
                   . _SMALLWORLD_START . '<select class="schooltime" name="schoolstart[]" id="schoolstart">';
        $array    = SmallworldGetTimestampsToForm();
        foreach ($array as $key => $option) {
            $selectstart = $selectedstart == $key ? ' selected="selected"' : null;
            $dropdown    .= '<option value="' . $key . '"' . $selectstart . '>' . $option . '</option>' . '<br>';
        }
        $dropdown .= '</select>'
                   . '<br><br>'
                   . _SMALLWORLD_STOP . '<select class="schooltime" name="schoolstop[]" id="schoolstop">';
        $array    = SmallworldGetTimestampsToForm();
        foreach ($array as $key => $option) {
            $selectstop = $selectedstop == $key ? ' selected="selected"' : null;
            $dropdown   .= '<option value="' . $key . '"' . $selectstop . '>' . $option . '</option>' . '<br>';
        }
        $dropdown .= '</select><br></p></div>';

        return $dropdown;
    }

    /**
     * Create HTML form elements for Job fields
     *
     * @param string  $class - not used
     * @param string  $name
     * @param string  $name2 - not used
     * @param string  $rel   - not used
     * @param string  $textmore - not used
     * @param string  $employer      (optional)
     * @param string  $position      (optional)
     * @param string  $selectedstart (optional)
     * @param string  $selectedstop  (optional)
     * @param string  $description   (optional)
     * @return string
     */
    public function job(
        $class,
        $name,
        $name2,
        $rel,
        $textmore,
        $employer = null,
        $position = null,
        $selectedstart = null,
        $selectedstop = null,
        $description = null
    ) {
        $text = '<div id="' . $name . '"><p class="smallworld_clonebreaker">' . _SMALLWORLD_EMPLOYER . '<input class="job" id="job" value="' . $employer . '" type="text" name="employer[]">'
              . '<br><br>' . _SMALLWORLD_POSITION . '<input class="job" type="text" value="' . $position . '" name="position[]">'
              . '<br><br>' . _SMALLWORLD_JOBSTART . '<input class="jobstart" type="text" value="' . $selectedstart . '" name="jobstart[]">'
              . '<br><br>' . _SMALLWORLD_JOBSTOP . '<input class="jobstop" value="' . $selectedstop . '" type="text" name="jobstop[]">'
              . '<br><br><span class="jobdescText">' . _SMALLWORLD_DESCRIPTION . '</span><textarea class="jobdesc" name="description[]" rows="20" cols="20">' . $description . '</textarea><br></p></div>'
              . '' . '<br>';

        return $text;
    }

    /**
     * Create a HTML upload form
     *
     * @param int $userID - not used
     * @return string
     */
    public function uploadform($userID)
    {
        $text = '<form action="' . Helper::getInstance()->url('imgupload.php') . '" method="POST" enctype="multipart/form-data">'
              . '<input type="file" name="file[]" multiple>'
              . '<button type="submit">' . _SMALLWORLD_UPLOADTEXT . '</button>'
              . '<span class="file_upload_label">' . _SMALLWORLD_UPLOADFILESTEXT . '</span>'
              . '</form>';

        return $text;
    }

    /**
     * @param int    $userID - not used
     * @param string $imgurl
     * @param string $imgdesc
     * @param string $id
     * @return string
     */
    public function edit_images($userID, $imgurl, $imgdesc, $id)
    {
        $text = '<p class="smallworld_clonebreaker"><br>'
              . '<table class="smallworld_table" border="0" cellspacing="0" cellpadding="0">'
              . '<tr>'
              . '<td><img class="smallworld_edit_image" src="' . $imgurl . '" height="100px" width="80px;"></td>'
              . '<td><span class="smallworld_editTextSpan">' . _SMALLWORLD_UPLOADDESC . '</span><br><br><textarea class="smallworld_edit_desc" name="imgdesc[]" rows="1" cols="1">' . stripslashes($imgdesc) . '</textarea><br><br></td>'
              . '<input value="' . $id . '" type="hidden" name="id[]">'
              . '</tr></table></p>';

        return $text;
    }

    /**
     * Create HTML radio selects for user settings
     *
     * @param int $userid
     * @return string
     */
    public function usersettings($userid)
    {
        $form = "<div style='display:none'><div class='smallworld_usersetings'>";
        $form .= '<fieldset><legend>' . _SMALLWORLD_SHOWIFPUBLICORPRIVATE . '</legend>';
        $form .= "<form id='perset'>";
        if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser'] instanceof \XoopsUser) {
            $sql    = 'SELECT value FROM ' . $GLOBALS['xoopsDB']->prefix('smallworld_settings') . ' WHERE userid = ' . (int)$userid;
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            $i      = $GLOBALS['xoopsDB']->getRowsNum($result);
            $v      = [];
            if ($i >= 1) {
                while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                    $v    = unserialize(stripslashes($row['value']));
                    $pv   = ('1' == $v['posts']) ? ' checked' : '';
                    $cv   = ('1' == $v['comments']) ? ' checked' : '';
                    $nv   = ('1' == $v['notify']) ? ' checked' : '';
                    $form .= '<input type="checkbox" name="usersettings[]" id="posts" value="' . $v['posts'] . '" ' . $pv . '> ' . _SMALLWORLD_SHOWMYPOSTS . '<br>';
                    $form .= '<input type="checkbox" name="usersettings[]" id="comments" value="' . $v['comments'] . '" ' . $cv . '> ' . _SMALLWORLD_SHOWMYCOMMENTS . '<br>';
                    $form .= '<input type="checkbox" name="usersettings[]" id="notify" value="' . $v['notify'] . '" ' . $nv . '> ' . _SMALLWORLD_NOTIFYME . '<br>';
                }
            } else {
                $form .= '<input type="checkbox" name="usersettings[]" id="posts" value="0"> ' . _SMALLWORLD_SHOWMYPOSTS . '<br>';
                $form .= '<input type="checkbox" name="usersettings[]" id="comments" value="0"> ' . _SMALLWORLD_SHOWMYCOMMENTS . '<br>';
                $form .= '<input type="checkbox" name="usersettings[]" id="notify" value="0"> ' . _SMALLWORLD_NOTIFYME . '<br>';
            }
        }
        $form .= "<br><input type='submit' id='smallworld_privsave' value='" . _SMALLWORLD_SUBMIT . "' class='smallworld_finish'>";
        $form .= '</form></fieldset></div></div>';

        return $form;
    }
}
