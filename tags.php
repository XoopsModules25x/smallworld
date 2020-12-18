<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

 /**
 * @package    \XoopsModules\Smallworld
 * @license    {@link https://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * @Author     Michael Albertsen (http://culex.dk) <culex@culex.dk>
 * @copyright  2011 Culex
 * @copyright  {@link https://xoops.org 2001-2020 XOOPS Project}
 * @link       https://github.com/XoopsModules25x/smallworld
 **/

use XoopsModules\Smallworld;

require_once __DIR__ . '/header.php';

include XOOPS_ROOT_PATH . '/header.php';
/** @var \XoopsModules\Smallworld\Helper $helper */
include_once $helper->path('include/functions.php');
include_once $helper->path('include/arrays.php');

$GLOBALS['xoopsLogger']->activated = false;
if ($_GET) {
    $q = smallworld_sanitize($_GET['term']);
    //check $q, get results from your database and put them in $arr
    $arr[] = 'Afghanistan';
    $arr[] = 'Albania';
    $arr[] = 'Algeria';
    $arr[] = 'American Samoa';
    $arr[] = 'Andorra';
    $arr[] = 'Angola';
    $arr[] = 'Anguilla';
    $arr[] = 'Antarctica';
    $arr[] = 'Antigua and Barbuda';
    $arr[] = 'Argentina';
    $arr[] = 'Armenia';
    $arr[] = 'Aruba';
    $arr[] = 'Australia';
    $arr[] = 'Austria';
    $arr[] = 'Azerbaijan';
    $arr[] = 'Bahamas';
    $arr[] = 'Bahrain';
    $arr[] = 'Bangladesh';
    $arr[] = 'Barbados';
    $arr[] = 'Belarus';
    $arr[] = 'Belgium';
    $arr[] = 'Belize';
    $arr[] = 'Benin';
    $arr[] = 'Bermuda';
    $arr[] = 'Bhutan';
    $arr[] = 'Bolivia';
    $arr[] = 'Bosnia and Herzegovina';
    $arr[] = 'Botswana';
    $arr[] = 'Bouvet Island';
    $arr[] = 'Brazil';
    $arr[] = 'British Antarctic Territory';
    $arr[] = 'British Indian Ocean Territory';
    $arr[] = 'British Virgin Islands';
    $arr[] = 'Brunei';
    $arr[] = 'Bulgaria';
    $arr[] = 'Burkina Faso';
    $arr[] = 'Burundi';
    $arr[] = 'Cambodia';
    $arr[] = 'Cameroon';
    $arr[] = 'Canada';
    $arr[] = 'Canton and Enderbury Islands';
    $arr[] = 'Cape Verde';
    $arr[] = 'Cayman Islands';
    $arr[] = 'Central African Republic';
    $arr[] = 'Chad';
    $arr[] = 'Chile';
    $arr[] = 'China';
    $arr[] = 'Christmas Island';
    $arr[] = 'Cocos [Keeling] Islands';
    $arr[] = 'Colombia';
    $arr[] = 'Comoros';
    $arr[] = 'Congo - Brazzaville';
    $arr[] = 'Congo - Kinshasa';
    $arr[] = 'Cook Islands';
    $arr[] = 'Costa Rica';
    $arr[] = 'Croatia';
    $arr[] = 'Cuba';
    $arr[] = 'Cyprus';
    $arr[] = 'Czech Republic';
    $arr[] = 'C?te d?Ivoire';
    $arr[] = 'Denmark';
    $arr[] = 'Djibouti';
    $arr[] = 'Dominica';
    $arr[] = 'Dominican Republic';
    $arr[] = 'Dronning Maud Land';
    $arr[] = 'East Germany';
    $arr[] = 'Ecuador';
    $arr[] = 'Egypt';
    $arr[] = 'El Salvador';
    $arr[] = 'Equatorial Guinea';
    $arr[] = 'Eritrea';
    $arr[] = 'Estonia';
    $arr[] = 'Ethiopia';
    $arr[] = 'Falkland Islands';
    $arr[] = 'Faroe Islands';
    $arr[] = 'Fiji';
    $arr[] = 'Finland';
    $arr[] = 'France';
    $arr[] = 'French Guiana';
    $arr[] = 'French Polynesia';
    $arr[] = 'French Southern Territories';
    $arr[] = 'French Southern and Antarctic Territories';
    $arr[] = 'Gabon';
    $arr[] = 'Gambia';
    $arr[] = 'Georgia';
    $arr[] = 'Germany';
    $arr[] = 'Ghana';
    $arr[] = 'Gibraltar';
    $arr[] = 'Greece';
    $arr[] = 'Greenland';
    $arr[] = 'Grenada';
    $arr[] = 'Guadeloupe';
    $arr[] = 'Guam';
    $arr[] = 'Guatemala';
    $arr[] = 'Guernsey';
    $arr[] = 'Guinea';
    $arr[] = 'Guinea-Bissau';
    $arr[] = 'Guyana';
    $arr[] = 'Haiti';
    $arr[] = 'Heard Island and McDonald Islands';
    $arr[] = 'Honduras';
    $arr[] = 'Hong Kong SAR China';
    $arr[] = 'Hungary';
    $arr[] = 'Iceland';
    $arr[] = 'India';
    $arr[] = 'Indonesia';
    $arr[] = 'Iran';
    $arr[] = 'Iraq';
    $arr[] = 'Ireland';
    $arr[] = 'Isle of Man';
    $arr[] = 'Israel';
    $arr[] = 'Italy';
    $arr[] = 'Jamaica';
    $arr[] = 'Japan';
    $arr[] = 'Jersey';
    $arr[] = 'Johnston Island';
    $arr[] = 'Jordan';
    $arr[] = 'Kazakhstan';
    $arr[] = 'Kenya';
    $arr[] = 'Kiribati';
    $arr[] = 'Kuwait';
    $arr[] = 'Kyrgyzstan';
    $arr[] = 'Laos';
    $arr[] = 'Latvia';
    $arr[] = 'Lebanon';
    $arr[] = 'Lesotho';
    $arr[] = 'Liberia';
    $arr[] = 'Libya';
    $arr[] = 'Liechtenstein';
    $arr[] = 'Lithuania';
    $arr[] = 'Luxembourg';
    $arr[] = 'Macau SAR China';
    $arr[] = 'Macedonia';
    $arr[] = 'Madagascar';
    $arr[] = 'Malawi';
    $arr[] = 'Malaysia';
    $arr[] = 'Maldives';
    $arr[] = 'Mali';
    $arr[] = 'Malta';
    $arr[] = 'Marshall Islands';
    $arr[] = 'Martinique';
    $arr[] = 'Mauritania';
    $arr[] = 'Mauritius';
    $arr[] = 'Mayotte';
    $arr[] = 'Metropolitan France';
    $arr[] = 'Mexico';
    $arr[] = 'Micronesia';
    $arr[] = 'Midway Islands';
    $arr[] = 'Moldova';
    $arr[] = 'Monaco';
    $arr[] = 'Mongolia';
    $arr[] = 'Montenegro';
    $arr[] = 'Montserrat';
    $arr[] = 'Morocco';
    $arr[] = 'Mozambique';
    $arr[] = 'Myanmar [Burma]';
    $arr[] = 'Namibia';
    $arr[] = 'Nauru';
    $arr[] = 'Nepal';
    $arr[] = 'Netherlands';
    $arr[] = 'Netherlands Antilles';
    $arr[] = 'Neutral Zone';
    $arr[] = 'New Caledonia';
    $arr[] = 'New Zealand';
    $arr[] = 'Nicaragua';
    $arr[] = 'Niger';
    $arr[] = 'Nigeria';
    $arr[] = 'Niue';
    $arr[] = 'Norfolk Island';
    $arr[] = 'North Korea';
    $arr[] = 'North Vietnam';
    $arr[] = 'Northern Mariana Islands';
    $arr[] = 'Norway';
    $arr[] = 'Oman';
    $arr[] = 'Pacific Islands Trust Territory';
    $arr[] = 'Pakistan';
    $arr[] = 'Palau';
    $arr[] = 'Palestinian Territories';
    $arr[] = 'Panama';
    $arr[] = 'Panama Canal Zone';
    $arr[] = 'Papua New Guinea';
    $arr[] = 'Paraguay';
    $arr[] = 'Peoples Democratic Republic of Yemen';
    $arr[] = 'Peru';
    $arr[] = 'Philippines';
    $arr[] = 'Pitcairn Islands';
    $arr[] = 'Poland';
    $arr[] = 'Portugal';
    $arr[] = 'Puerto Rico';
    $arr[] = 'Qatar';
    $arr[] = 'Romania';
    $arr[] = 'Russia';
    $arr[] = 'Rwanda';
    $arr[] = 'R?union';
    $arr[] = 'Saint Barth?lemy';
    $arr[] = 'Saint Helena';
    $arr[] = 'Saint Kitts and Nevis';
    $arr[] = 'Saint Lucia';
    $arr[] = 'Saint Martin';
    $arr[] = 'Saint Pierre and Miquelon';
    $arr[] = 'Saint Vincent and the Grenadines';
    $arr[] = 'Samoa';
    $arr[] = 'San Marino';
    $arr[] = 'Saudi Arabia';
    $arr[] = 'Senegal';
    $arr[] = 'Serbia';
    $arr[] = 'Serbia and Montenegro';
    $arr[] = 'Seychelles';
    $arr[] = 'Sierra Leone';
    $arr[] = 'Singapore';
    $arr[] = 'Slovakia';
    $arr[] = 'Slovenia';
    $arr[] = 'Solomon Islands';
    $arr[] = 'Somalia';
    $arr[] = 'South Africa';
    $arr[] = 'South Georgia and the South Sandwich Islands';
    $arr[] = 'South Korea';
    $arr[] = 'Spain';
    $arr[] = 'Sri Lanka';
    $arr[] = 'Sudan';
    $arr[] = 'Suriname';
    $arr[] = 'Svalbard and Jan Mayen';
    $arr[] = 'Swaziland';
    $arr[] = 'Sweden';
    $arr[] = 'Switzerland';
    $arr[] = 'Syria';
    $arr[] = 'S?o Tom? and Pr?ncipe';
    $arr[] = 'Taiwan';
    $arr[] = 'Tajikistan';
    $arr[] = 'Tanzania';
    $arr[] = 'Thailand';
    $arr[] = 'Timor-Leste';
    $arr[] = 'Togo';
    $arr[] = 'Tokelau';
    $arr[] = 'Tonga';
    $arr[] = 'Trinidad and Tobago';
    $arr[] = 'Tunisia';
    $arr[] = 'Turkey';
    $arr[] = 'Turkmenistan';
    $arr[] = 'Turks and Caicos Islands';
    $arr[] = 'Tuvalu';
    $arr[] = 'U.S. Minor Outlying Islands';
    $arr[] = 'U.S. Miscellaneous Pacific Islands';
    $arr[] = 'U.S. Virgin Islands';
    $arr[] = 'Uganda';
    $arr[] = 'Ukraine';
    $arr[] = 'Union of Soviet Socialist Republics';
    $arr[] = 'United Arab Emirates';
    $arr[] = 'United Kingdom';
    $arr[] = 'United States';
    $arr[] = 'Unknown or Invalid Region';
    $arr[] = 'Uruguay';
    $arr[] = 'Uzbekistan';
    $arr[] = 'Vanuatu';
    $arr[] = 'Vatican City';
    $arr[] = 'Venezuela';
    $arr[] = 'Vietnam';
    $arr[] = 'Wake Island';
    $arr[] = 'Wallis and Futuna';
    $arr[] = 'Western Sahara';
    $arr[] = 'Yemen';
    $arr[] = 'Zambia';
    $arr[] = 'Zimbabwe';
    $arr[] = '?land Islands';
}

/**
 * Convert array to json string
 *
 * @todo consider moving this to ./include/functions.php
 * @param $array
 * @return bool|string
 */
function array_to_json($array)
{
    if (!is_array($array)) {
        return false;
    }

    $associative = count(array_diff(array_keys($array), array_keys(array_keys($array))));
    if ($associative) {
        $construct = [];
        foreach ($array as $key => $value) {
            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if (is_numeric($key)) {
                $key = "key_$key";
            }
            $key = '"' . addslashes($key) . '"';

            // Format the value:
            if (is_array($value)) {
                $value = array_to_json($value);
            } elseif (!is_numeric($value) || is_string($value)) {
                $value = '"' . addslashes($value) . '"';
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = '{ ' . implode(', ', $construct) . ' }';
    } else { // If the array is a vector (not associative):
        $construct = [];
        foreach ($array as $value) {
            // Format the value:
            if (is_array($value)) {
                $value = array_to_json($value);
            } elseif (!is_numeric($value) || is_string($value)) {
                $value = "'" . addslashes($value) . "'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = '[ ' . implode(', ', $construct) . ' ]';
    }

    return $result;
}

$result = [];
//@todo - check this. $key is always numeric so strip_tags is unnecessary, perhaps strip_tags on $value was intended?
foreach ($arr as $key => $value) {
    if (false !== mb_stripos($key, $q)) {
        $result[] = ['id' => $value, 'label' => $key, 'value' => strip_tags($key)];
    }
    if (count($result) > 11) {
        break;
    }
}
echo array_to_json($result);
