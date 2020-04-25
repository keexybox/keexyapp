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
class MacComponent extends Component
{
    public function rewrite($mac)
    {
        $short_mac = preg_replace("/[^a-zA-Z0-9]+/", "", $mac);
        $s_short_mac = str_split($short_mac, 2);
        $final_mac = strtoupper(implode(':', $s_short_mac));

        return $final_mac;
    }
}

?>
