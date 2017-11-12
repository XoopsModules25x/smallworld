<?php
/*
 * jQuery File Upload Plugin PHP Example 4.2.4
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 * @Last committed:		$Revision: 11216 $
 * @Last changed by:	$Author: djculex $
 * @Last changed date:	$Date: 2013-03-13 13:27:43 +0100 (on, 13 mar 2013) $
 * @ID:					$Id: imgupload.php 11216 2013-03-13 12:27:43Z djculex $
 */
global $xoopsUser, $xoopsLogger;
include_once("../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/class_collector.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/include/functions.php");
include_once(XOOPS_ROOT_PATH."/modules/smallworld/class/UploadHandler.php");
	if ($xoopsUser) {
			$xoopsLogger->activated = false;
			$img = new smallWorldImages;
			$userID = $xoopsUser->getVar('uid');
			$user = new XoopsUser($userID);	
            $upload_handler = new UploadHandler();
	}
?>