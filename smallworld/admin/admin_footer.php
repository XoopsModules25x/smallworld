<?php
/**
* You may not change or alter any portion of this comment or credits
* of supporting developers from this source code or any supporting source code
* which is considered copyrighted (c) material of the original comment or credit authors.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* @copyright:			The XOOPS Project http://sourceforge.net/projects/xoops/
* @license:				http://www.fsf.org/copyleft/gpl.html GNU public license
* @module:				Smallworld
* @Author:				Michael Albertsen (http://culex.dk) <culex@culex.dk>
* @copyright:			2011 Culex
* @Repository path:		$HeadURL: https://svn.code.sf.net/p/xoops/svn/XoopsModules/smallworld/trunk/smallworld/admin/admin_footer.php $
* @Last committed:		$Revision: 8905 $
* @Last changed by:		$Author: djculex $
* @Last changed date:	$Date: 2012-02-07 16:57:57 -0500 (Tue, 07 Feb 2012) $
* @ID:					$Id: admin_footer.php 8905 2012-02-07 21:57:57Z djculex $
**/

echo "<div class='adminfooter'>\n"
    ."  <div style='text-align: center;'>\n"
    ."    <a href='http://www.xoops.org' target='_blank'><img src='{$pathIcon32}/xoopsmicrobutton.gif' alt='XOOPS' title='XOOPS'></a>\n"
    ."  </div>\n"
    ."  " . _AM_SMALLWORLD_FOOTER . "\n"
    ."</div>";

echo "<span style='margin: 27%; height: 50px; position: relative;'>"._AM_SMALLWORLD_SP."</span>";


xoops_cp_footer();