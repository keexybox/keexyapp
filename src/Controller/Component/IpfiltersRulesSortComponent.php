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
use Cake\ORM\TableRegistry;

/**
 * This component reorders the firewall rules of a profile 
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class IpfiltersRulesSortComponent extends Component
{
    public function force_sort_rules($profile_id)
    {
        //$this->ProfilesIpfilters = TableRegistry::get('ProfilesIpfilters');
        $this->ProfilesIpfilters = TableRegistry::getTableLocator()->get('ProfilesIpfilters');
        // Force reset ordering before running sorting query
        $ProfilesIpfilters = $this->ProfilesIpfilters
            ->find('all', ['conditions' => ['ProfilesIpfilters.profile_id' => $profile_id]])
            ->order(['rule_number' => 'ASC']);;

        $reset_rule_num = 1;
        foreach($ProfilesIpfilters as $ProfileIpfilter) {
            $rule_data['rule_number'] = $reset_rule_num;
            $ipfilter = $this->ProfilesIpfilters->get($ProfileIpfilter['id']);
            $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $rule_data);
            $this->ProfilesIpfilters->save($ipfilter);
            $reset_rule_num++;
        }

    }
}
