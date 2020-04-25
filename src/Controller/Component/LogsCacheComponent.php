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
use Cake\Cache\Cache;

/**
 * This component allows to manage the display cache for logs
 * Cache allows faster user rendering for display
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class LogsCacheComponent extends Component
{
    /* Loading another component */
    public $components = ['Times'];

    /**
     * Read DnsLog data from cache
     *
     * @param string $cache_prefix: Cache name to read
     * @return array of data retrieve from cache
     */
    public function ReadCache($cache_prefix)
    {
        $cache = Cache::read($cache_prefix, 'logs');
        return $cache;
    }

    /**
     * Flush display cache of the logs 
     *
     * @param string $cache_prefix: Cache name to read
     * @return array of data retrieve from cache
     */
    public function ClearCache()
    {
        Cache::clear(false, 'logs');
    }

    /**
     * Rebuilt display cache of the logs
     *
     * @return void
     */
    public function WriteCache()
    {

        // We get the system timezone offset
        $sys_tz_name = $this->Times->getSystemTimezone();
        $sys_tz_offset = $this->Times->getSystemTimezoneOffset();

        $this->DnsLog = TableRegistry::getTableLocator()->get('DnsLog');

        $dnslogs = $this->DnsLog->find();
        $dnslogs->select([
                'id',
                // Due to FrozenTime that work only with UTC, we convert date of log that were system time into UTC
                'date_time' => $dnslogs->func()->convert_tz(['date_time' => 'identifier',"$sys_tz_offset",'+00:00']),
                'client_ip',
                'profile_id',
                'keexybox_profiles.profilename',
                'domain',
                'blocked',
                'category',
                ])
            ->join(['table' => 'keexybox_profiles', 'type' => 'LEFT', 'conditions' => 'keexybox_profiles.id = profile_id'])->toArray();

        Cache::write('all_logs', $dnslogs, 'logs');
    }
}
