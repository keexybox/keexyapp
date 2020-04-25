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

Namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

/*
 * This class allow to run python scripts that import or export blacklist
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class BlacklistShell extends BoxShell
{

    /**
     * This function import domains to blacklist
     * 
     * @param $tarfile : tar.gz file that contains domains to import
     * @param $domains_per_sql_query : number of domains to import per SQL query
     *
     * @return integer as exit status
     */
    public function import ($tarfile, $domains_per_sql_query = null)
    {
        $import_blacklist_script = $this->scripts_dir . "/blacklist-import.py";
        if($domains_per_sql_query == null) {
            $domains_per_sql_query = 10000;
        }

        exec("$this->bin_python $import_blacklist_script -n $domains_per_sql_query -f $tarfile", $output, $rc);
        exit($rc);
    }

    /**
     * This function import domains to blacklist
     * 
     * @param $url_list : URL of the list to import
     * @param $category : Category to set for this list to import
     * @param $domains_per_sql_query : number of domains to import per SQL query
     *
     * @return integer as exit status
     */
    public function webimport ($url_list, $category = null, $domains_per_sql_query = null)
    {
        $import_blacklist_script = $this->scripts_dir . "/blacklist-import-web.py";
        if($domains_per_sql_query == null) {
            $domains_per_sql_query = 10000;
        }

        if($category == null) {
            $category = 'default';
        }

        exec("$this->bin_python $import_blacklist_script -n $domains_per_sql_query -u $url_list -c $category", $output, $rc);
        exit($rc);
    }

    /**
     * This function export domains from blacklist
     * 
     * @param : $tarfile : tar.gz file to create
     * @param : $categories : list of categories to export. Each category must be seperate by ","
     *
     * @return integer as exit status
     */
    public function export($tarfile, $categories)
    {
        $export_blacklist_script = $this->scripts_dir . "/blacklist-export.py";
        exec("$this->bin_python $export_blacklist_script -c $categories -f $tarfile", $output, $rc);
        exit($rc);
    }

    public function caching()
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
