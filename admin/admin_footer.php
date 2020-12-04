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

echo "<div class='adminfooter'>\n"
   . "  <div class='center'>\n"
       . "    <a href='https://xoops.org' target='_blank'><img src='" . \Xmf\Module\Admin::iconUrl('xoopsmicrobutton.gif') . "' alt='XOOPS' title='XOOPS'></a>\n"
   . "  </div>\n"
   . "  " . _AM_SMALLWORLD_FOOTER . "\n"
   . "</div>\n"
   . "<div class='center middle' style='height: 3em;'>" . _AM_SMALLWORLD_SP . "</div>\n";

xoops_cp_footer();
