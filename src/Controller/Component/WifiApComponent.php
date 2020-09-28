<?php
/**
 * @author Benoit Saglietto <bsaglietto[AT]keexybox.org>
 *
 * @copyright Copyright (c) 2020, Benoit SAGLIETTO
 * @license GPLv3
 *
 * This file is part of Keexybox project.

 * Keexybox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Keexybox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Keexybox. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * This component rewrites the type of MAC address notation to the Keexybox accepted MAC address notation
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class WifiApComponent extends Component
{
    public function CountryList()
    {
        $country_codes = [
            'DZ' => 'Algeria',
            'AR' => 'Argentina',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'BH' => 'Bahrain',
            'BM' => 'Bermuda',
            'BO' => 'Bolivia',
            'BR' => 'Brazil',
            'BG' => 'Bulgaria',
            'CA' => 'Canada',
            'CL' => 'Chile',
            'CN' => 'China',
            'CO' => 'Colombia',
            'CR' => 'Costa Rica',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'EE' => 'Estonia',
            'FI' => 'Finland',
            'FR' => 'France',
            'DE' => 'Germany',
            'GR' => 'Greece',
            'GT' => 'Guatemala',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IE' => 'Ireland',
            'PK' => 'Islamic Republic of Pakistan',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP3' => 'Japan',
            'JO' => 'Jordan',
            'KE' => 'Kenya',
            'KW' => 'Kuwait',
            'KW' => 'Kuwait',
            'LB' => 'Lebanon',
            'LI' => 'Liechtenstein',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MU' => 'Mauritius',
            'MX' => 'Mexico',
            'MX' => 'Mexico',
            'MA' => 'Morocco',
            'MA' => 'Morocco',
            'NL' => 'Netherlands',
            'NZ' => 'New Zealand',
            'NZ' => 'New Zealand',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PA' => 'Panama',
            'PA' => 'Panama',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PL' => 'Poland',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'KR' => 'Republic of Korea (South Korea)',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RU' => 'Russia',
            'SA' => 'Saudi Arabia',
            'CS' => 'Serbia and Montenegro',
            'SG' => 'Singapore',
            'SK' => 'Slovak Republic',
            'SK' => 'Slovak Republic',
            'SI' => 'Slovenia',
            'SI' => 'Slovenia',
            'ZA' => 'South Africa',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'CH' => 'Switzerland',
            'TW' => 'Taiwan',
            'TH' => 'Thailand',
            'TH' => 'Thailand',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates',
            'GB' => 'United Kingdom',
            'US' => 'United States',
            'UY' => 'Uruguay',
            'UY' => 'Uruguay',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
        ];
        return $country_codes;
    }

    public function HwModeList()
    {
        $hw_mod_list = [
            'a' => 'IEEE 802.11a (5 GHz)',
            'b' => 'IEEE 802.11b (2.4 GHz)',
            'g' => 'IEEE 802.11g (2.4 GHz)',
            'ad' => 'IEEE 802.11ad (60 GHz)',
        ];
        return $hw_mod_list;
    }

    public function ChannelList()
    {
        $channel_list = [
            //0 => __('automatic'),
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5',
            6 => '6',
            7 => '7',
            8 => '8',
            9 => '9',
            10 => '10',
            11 => '11',
            12 => '12',
            13 => '13',
            ];
        return $channel_list;
    }

    public function WpaKeyMgmtList()
    {
        return null;
    }

    public function WpaPairwiseList()
    {
        return null;
    }
}

?>
