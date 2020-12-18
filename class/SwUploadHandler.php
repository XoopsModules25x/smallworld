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
 * @copyright    The XOOPS Project (https://xoops.org)
 * @copyright    2011 Culex
 * @license      GNU GPL (http://www.gnu.org/licenses/gpl-2.0.html/)
 * @package      SmallWorld
 * @since        1.0
 * @author       Michael Albertsen (http://culex.dk) <culex@culex.dk>
 */
class SwUploadHandler
{
    private $upload_dir;
    private $upload_url;
    private $thumbnails_dir;
    private $thumbnails_url;
    private $thumbnail_max_width;
    private $thumbnail_max_height;
    private $field_name;

    /**
     * SwUploadHandler constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->upload_dir           = $options['upload_dir'];
        $this->upload_url           = $options['upload_url'];
        $this->thumbnails_dir       = $options['thumbnails_dir'];
        $this->thumbnails_url       = $options['thumbnails_url'];
        $this->thumbnail_max_width  = $options['thumbnail_max_width'];
        $this->thumbnail_max_height = $options['thumbnail_max_height'];
        $this->field_name           = $options['field_name'];
    }

    /**
     * @param $file_name
     * @return null|\stdClass
     */
    private function get_file_object($file_name)
    {
        $file_path = $this->upload_dir . $file_name;
        if (is_file($file_path) && '.' !== $file_name[0] && 'index.html' !== $file_name && 'Thumbs.db' !== $file_name) {
            $file            = new \stdClass();
            $file->name      = $file_name;
            $file->size      = filesize($file_path);
            $file->url       = $this->upload_url . rawurlencode($file->name);
            $file->thumbnail = is_file($this->thumbnails_dir . $file_name) ? $this->thumbnails_url . rawurlencode($file->name) : null;

            return $file;
        }

        return null;
    }

    /**
     * @param $file_name
     * @return bool
     */
    private function create_thumbnail($file_name)
    {
        $file_path      = $this->upload_dir . $file_name;
        $thumbnail_path = $this->thumbnails_dir . $file_name;
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        $scale = min($this->thumbnail_max_width / $img_width, $this->thumbnail_max_height / $img_height);
        if ($scale > 1) {
            $scale = 1;
        }
        $thumbnail_width  = $img_width * $scale;
        $thumbnail_height = $img_height * $scale;
        $thumbnail_img    = @imagecreatetruecolor($thumbnail_width, $thumbnail_height);
        switch (mb_strtolower(mb_substr(mb_strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img         = @imagecreatefromjpeg($file_path);
                $write_thumbnail = 'imagejpeg';
                break;
            case 'gif':
                $src_img         = @imagecreatefromgif($file_path);
                $write_thumbnail = 'imagegif';
                break;
            case 'png':
                $src_img         = @imagecreatefrompng($file_path);
                $write_thumbnail = 'imagepng';
                break;
            default:
                $src_img = $write_thumbnail = null;
        }
        $success = $src_img && @imagecopyresampled($thumbnail_img, $src_img, 0, 0, 0, 0, $thumbnail_width, $thumbnail_height, $img_width, $img_height)
                   && $write_thumbnail($thumbnail_img, $thumbnail_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($thumbnail_img);

        return $success;
    }

    //function to return file extension from a path or file name

    /**
     * @param $path
     * @return mixed
     */
    public function getFileExtension($path)
    {
        $parts = pathinfo($path);

        return $parts['extension'];
    }

    /**
     * @param $uploaded_file
     * @param $name
     * @param $size
     * @param $type
     * @param $error
     * @return \stdClass|bool  false if not XOOPS user
     */
    private function handle_file_upload($uploaded_file, $name, $size, $type, $error)
    {
        $file   = new \stdClass();
        $swDB   = new SwDatabase();
        $userid = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? $GLOBALS['xoopsUser']->uid() : 0;

        if (0 == $userid) {
            return false;
        }
        // Generate new name for file
        //$file->name = basename(stripslashes($name));
        $file->name = time() . mt_rand(0, 99999) . '.' . $this->getFileExtension($name);
        $file->size = (int)$size;
        $file->type = $type;
        $img        = XOOPS_URL . '/uploads/albums_smallworld/' . $userid . '/' . $file->name;

        // Save to database for later use
        $swDB->saveImage("null, '" . $userid . "', '" . $file->name . "', '" . addslashes($img) . "', '" . time() . "', ''");

        if (!$error && $file->name) {
            if ('.' === $file->name[0]) {
                $file->name = mb_substr($file->name, 1);
            }
            $file_path   = $this->upload_dir . $file->name;
            $append_file = is_file($file_path) && $file->size > filesize($file_path);
            clearstatcache();
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                if ($append_file) {
                    file_put_contents($file_path, fopen($uploaded_file, 'rb'), FILE_APPEND);
                } else {
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents($file_path, fopen('php://input', 'rb'), $append_file ? FILE_APPEND : 0);
            }
            $file_size = filesize($file_path);
            if ($file_size === $file->size) {
                $file->url       = $this->upload_url . rawurlencode($file->name);
                $file->thumbnail = $this->create_thumbnail($file->name) ? $this->thumbnails_url . rawurlencode($file->name) : null;
            }
            $file->size = $file_size;
        } else {
            $file->error = $error;
        }

        return $file;
    }

    public function get()
    {
        $file_name = isset($_REQUEST['file']) ? basename(stripslashes($_REQUEST['file'])) : null;
        if (null !== $file_name) {
            $info = $this->get_file_object($file_name);
        } else {
            $info = array_values(array_filter(array_map([$this, 'get_file_object'], scandir($this->upload_dir, SCANDIR_SORT_NONE))));
        }
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');
        echo json_encode($info);
    }

    public function post()
    {
        $upload = isset($_FILES[$this->field_name]) ? $_FILES[$this->field_name] : [
            'tmp_name' => null,
            'name'     => null,
            'size'     => null,
            'type'     => null,
            'error'    => null,
        ];
        if (is_array($upload['tmp_name']) && count($upload['tmp_name']) > 1) {
            $info = [];
            foreach ($upload['tmp_name'] as $index => $value) {
                $info[] = $this->handle_file_upload($upload['tmp_name'][$index], $upload['name'][$index], $upload['size'][$index], $upload['type'][$index], $upload['error'][$index]);
            }
        } else {
            if (is_array($upload['tmp_name'])) {
                $upload = [
                    'tmp_name' => $upload['tmp_name'][0],
                    'name'     => $upload['name'][0],
                    'size'     => $upload['size'][0],
                    'type'     => $upload['type'][0],
                    'error'    => $upload['error'][0],
                ];
            }
            $info = $this->handle_file_upload(
                $upload['tmp_name'],
                isset($_SERVER['HTTP_X_FILE_NAME']) ? $_SERVER['HTTP_X_FILE_NAME'] : $upload['name'],
                isset($_SERVER['HTTP_X_FILE_SIZE']) ? $_SERVER['HTTP_X_FILE_SIZE'] : $upload['size'],
                isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'],
                $upload['error']
            );
        }
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH']) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        echo json_encode($info);
    }

    public function delete()
    {
        $userid = ($GLOBALS['xoopsUser'] && ($GLOBALS['xoopsUser'] instanceof \XoopsUser)) ? $GLOBALS['xoopsUser']->uid() : 0;

        if (0 == $userid) {
            return false;
        }
        $swDB      = new SwDatabase();
        $file_name = isset($_REQUEST['file']) ? basename(stripslashes($_REQUEST['file'])) : null;
        $file_path = $this->upload_dir . $file_name;
        //$img       = XOOPS_URL . '/uploads/albums_smallworld/' . $userid . '/' . $file_name;

        // Delete file based on user and filename
        $swDB->deleteImage($userid, $file_name);
        $swDB->deleteImage($userid, 'Thumbs.db');
        $thumbnail_path = $this->thumbnails_dir . $file_name;
        $success        = is_file($file_path) && '.' !== $file_name[0] && unlink($file_path);
        if ($success && is_file($thumbnail_path)) {
            unlink($thumbnail_path);
        }
        header('Content-type: application/json');
        echo json_encode($success);
    }
}
