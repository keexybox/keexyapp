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
 * This component allows to manage the display cache for blacklist
 * Cache allows faster user rendering for display
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class BlacklistCacheComponent extends Component
{
    /**
     * Read Blacklist data from cache
     *
     * @param string $cache_prefix: Cache name to read
     * @return array of data retrieve from cache
     */
    public function ReadCache($cache_prefix)
    {
        $cache = Cache::read($cache_prefix, 'bl');
        return $cache;
    }

    /**
     * Flush display cache of the blacklist 
     *
     * @param string $cache_prefix: Cache name to read
     * @return array of data retrieve from cache
     */
    public function ClearCache()
    {
        Cache::clear(false, 'bl');
    }

    /**
     * Rebuilt display cache of the blacklist
     *
     * @return void
     */
    public function WriteCache()
    {
        $this->Blacklist = TableRegistry::getTableLocator()->get('Blacklist');
        $catresults = $this->Blacklist->find('all');
        $categories = $catresults->select([
                'category',
                'websites' => $catresults->func()->count('zone')
                ])
            ->group('category')
            ->order(['category' => 'ASC'])
            ->toArray();

        foreach($catresults as $catresult) {
            //$categories[] = $catresult;
            $categories_list[$catresult['category']] = $catresult['category'];
        }

        if(isset($categories_list)) {
            foreach($categories_list as $category) {
                $profile_categories[] = ['category' => $category];

            }

            Cache::write('bl_websites_count', $categories, 'bl');
            Cache::write('bl_categories_list', $categories_list, 'bl');
            Cache::write('bl_profile_categories', $profile_categories, 'bl');
        }
    }
}
