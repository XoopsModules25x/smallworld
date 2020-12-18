/**
 * jQuery configs, do maps for Xoops Smallworld
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * copyright       {@link https://xoops.org 2001-2017 XOOPS Project}
 * license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * author 2020     Culex - homepage.: http://culex.dk & email.: culex@culex.dk
 */
 
xoops_smallworld(document).ready(function(){
	doMapBirth(smallworld_birthlatt, smallworld_birthlng);
	doMapNow(smallworld_currlatt, smallworld_currlng);
});