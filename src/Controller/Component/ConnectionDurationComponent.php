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
 * This component build a list of connection durations proposed to users when connecting
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ConnectionDurationComponent extends Component
{
    public function GetDurationList()
    {
        $durations = [
            '15' =>  __('15 minutes'),
            '30' =>  __('30 minutes'),
            '60' =>  __('1 hour'),
            '120' => __('2 hours'),
            '240' => __('4 hours'),
            '480' => __('8 hours'),
            '720' => __('12 hours'),
            '1440' => __('1 day'),
            '10080' => __('1 week'),
            '20160' => __('2 weeks'),
            '44640' => __('1 month'),
            ];
        return $durations;
    }
}
