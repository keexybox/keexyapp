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

namespace App\Controller;

use App\Controller\AppController;
use Cake\Cache\Cache;
use Cake\Error\Debugger;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;

/**
 * This class is the controller to manage Blacklist 
 * by adding, removing or categorizing domains
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class BlacklistController extends AppController
{
    /**
     * Query the blacklist and allow manage domains 
     *
     * @return void
     */
    public function query()
    {
        //$blacklists = $this->Blacklist->find('all');
        $blacklists = null;

        // Built categories list with domains counts per category and store in cache
        $this->loadComponent('BlacklistCache');
        if(($categories = $this->BlacklistCache->ReadCache('bl_categories_list')) === false) {
            $this->BlacklistCache->WriteCache();
            $categories = $this->BlacklistCache->ReadCache('bl_categories_list');
        }

        // Add All catagories in list
        $categories = array('all' => __('all')) + $categories;

        $this->set('search_query', null);
        $this->set('category', 'all');
        $this->set('results', 25);

        if(null !== $this->request->getQuery('query')) {
            if($this->request->getQuery('action') == 'search') {
                if($this->request->getQuery('category') != 'all') {
                    $query_cat = ['category' => $this->request->getQuery('category')];
                } else {
                    $query_cat = null;
                }
                $query = $this->request->getQuery('query');
                $blacklists = $this->Blacklist->find()
                        ->where(['zone LIKE' => "%$query%"])
                        ->andWhere($query_cat);

                $this->paginate = [
                    'limit' => $this->request->getQuery('results'),
                ];

                // Set value of search Query to show search in view result
                $this->set('search_query', $query);
                $this->set('results', $this->request->getQuery('results'));
                $this->set('category', $this->request->getQuery('category'));
                $this->set('blacklists', $this->paginate($blacklists));
            }
        } else {
            $this->paginate = [
                'limit' => 25
            ];
        }

        if ($this->request->is('post')) {
            if(null !== $this->request->getData('check')) {

                // ACTION CHANGE CATEGORY 
                if($this->request->getData('action') == 'setcategory') {
                    if($this->request->getData('category') != '' or $this->request->getData('newcategory') != '') {
                        if($this->request->getData('newcategory') == '') {
                            $data = ['category' => $this->request->getData('category')];
                        } else {
                            $data = ['category' => $this->request->getData('newcategory')];
                        }
    
                        foreach($this->request->getData('check') as $check) {
                            $zone = $this->Blacklist->get($check);
                            $zone = $this->Blacklist->patchEntity($zone, $data);
                            $this->Blacklist->save($zone);
                        }
                    } else {
                        $this->Flash->warning(__('Please define a category'));
                    }
                    return $this->redirect($this->referer());
                }

                // ACTION DELETE
                if($this->request->getData('action') == 'delete') {
                    foreach($this->request->getData('check') as $check) {
                        $zone = $this->Blacklist->get($check);
                        $this->Blacklist->delete($zone);
                    }
                    return $this->redirect($this->referer());
                }
            } else {
                $this->Flash->warning(__('Nothing was selected.'));
            }
        }

        $this->set('categories', $categories);
        $this->set('blacklists', $blacklists);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Add multiple domains to the blacklist
     *
     * @return redirect
     */
    public function add($profile_id = null)
    {
        $this->loadComponent('Urlparser');

        if(isset($profile_id)) {
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        }

        // Built categories list with domain counts per category and store in cache
        $this->loadComponent('BlacklistCache');
        if(($categories = $this->BlacklistCache->ReadCache('bl_categories_list')) === false) {
            $this->BlacklistCache->WriteCache();
            $categories = $this->BlacklistCache->ReadCache('bl_categories_list');
        }

        // domains can be prefill in query by others pages
        // This built prefill urls or domains in HTML form
        $this->set('prefillurls', null);
        $query_urls = $this->request->getQuery('urls');
        if(isset($query_urls)) {
            $prefillurls = null;
            foreach($query_urls as $query_url) {
                $prefillurls .= $query_url." "; 
            }
            $this->set('prefillurls', $prefillurls);
        } 

        $BL = ['category' => null, 'newcategory' => null, 'replace' => false];

        if ($this->request->is('post')) {

            // Reset prefill urls or domains in HTML form
            if(isset($prefillurls)) {
                $prefillurls = null;
                $prefillurls .= $this->request->getData('domains');
                $this->set('prefillurls', $prefillurls);
            } else {
                $prefillurls = $this->request->getData('domains');
                $this->set('prefillurls', $prefillurls);
            }

            $BL = ['category' => $this->request->getData('category'), 'newcategory' => $this->request->getData('newcategory'), 'replace' => $this->request->getData('replace')];
            $this->set('prefill_urls', $this->request->data['domains']);

            $domains = preg_split("/[\n\r\t\ ]+/", trim($this->request->getData('domains')));
            unset($this->request->data['domains']);
            if($this->request->getData('weblist') != '') {
                $weblists = preg_split("/[\n\r\t\ ]+/", trim($this->request->data['weblist']));
            } else {
                $weblists = null;
            }
            unset($this->request->data['weblist']);

            // Define Category
            if($this->request->getData('category') == '' and $this->request->getData('newcategory') == '') {
                $this->request->data['category'] = 'default';
            }
            elseif(!isset($this->request->data['newcategory']) or $this->request->getData('newcategory') != '') {
                $this->request->data['category'] = $this->request->data['newcategory'];
                unset($this->request->data['newcategory']);
            } else {
                unset($this->request->data['newcategory']);
            }

            $blacklist_data = $this->request->data;

            if(isset($domains)) {
                foreach($domains as $domain) {
                    $parsedurl = $this->Urlparser->Parseurl($domain);
                    if(isset($parsedurl['fqdn']) and $parsedurl['fqdn'] != '') {
                        // Setting data
                        $blacklist_data['zone'] = $parsedurl['fqdn'];
                        $blacklist_data['category'] = strtolower($blacklist_data['category']);
                        $blacklist_data['host'] = '@*';
    
                        // if replace URL is checked
                        if($blacklist_data['replace'] == false) {
                            // Check domain exists
                            $blacklist = $this->Blacklist->find('all', ['conditions' => [
                                'zone' => $blacklist_data['zone'],
                            ]])->first();
    
                            // If not exist, add it.
                            if(!isset($blacklist['zone'])) {
                                $blacklist = $this->Blacklist->newEntity();
                                $blacklist = $this->Blacklist->patchEntity($blacklist, $blacklist_data);
                                if(!$this->Blacklist->save($blacklist)) {
                                    $unsaved_domains[] = $domain;
                                }
                            } 
    
                        // Else add/modify domain event if exists
                        } else {
                            $blacklist = $this->Blacklist->newEntity();
                            $blacklist = $this->Blacklist->patchEntity($blacklist, $blacklist_data);
                            if(!$this->Blacklist->save($blacklist)) {
                                $unsaved_domains[] = $domain;
                            }
                        }
                    } else {
                        $unsaved_domains[] = $domain;
                    }
                }

                if(isset($blacklist)) {
                    $errors = $blacklist->errors();
                    if(isset($errors['category']['name'])) {
                        $this->Flash->error($errors['category']['name']);
                    } else {
                        // clear Blacklist Cache
                        //$this->loadComponent('BlacklistCache');
                        //$this->BlacklistCache->ClearCache();
        
                        $addresses = null;
                        if(isset($unsaved_domains)) {
                            foreach($unsaved_domains as $unsaved_domain) {
                                $addresses .= $unsaved_domain.", ";
                            }
                            // Remove last ","
                            $addresses = rtrim($addresses,", ");
                            $this->Flash->warning(__('Following domains are invalid and were not added to the Blacklist: '.h($addresses)));
                        } else {
                            $this->Flash->success(__('Domains added successfully to the Blacklist.'));
                        }
                    }
                }
            }

            if(isset($weblists)) {
                $wi_g_rc = 0;
                foreach($weblists as $weblist) {
                    exec($this->kxycmd("blacklist webimport ".$weblist." ".$blacklist_data['category']), $output, $wi_rc);
                    $wi_g_rc = $wi_g_rc + $wi_rc; 
                }
                if($wi_g_rc == 0) {
                    $this->Flash->success(__('Domains imported successfully to Blacklist from lists.'));
                } else {
                    $this->Flash->warning(__('Some lists were invalid to import domains to the Blacklist.'));
                }
            }

            // Only use for wizard config
            $run_wizard = $this->Config->get('run_wizard');
            $install_type = null;
            if (isset($this->request->query['install_type'])) {
                $install_type = $this->request->query['install_type'];
            }

            if ($run_wizard->value == 1) {
                return $this->redirect(['controller' => 'blacklist', 'action' => 'wadd', 'install_type' => $install_type ]);
            } else {
                return $this->redirect(['controller' => 'blacklist', 'action' => 'index']);
            }

        }

        if(isset($categories)) {
            $this->set('categories', $categories);
        } else {
            $this->set('categories', null);
        }
        //$this->set(compact('url', 'profiles'));
        $this->set('BL', $BL);
        $this->set('_serialize', ['url']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Add multiple domains to the blacklist with Wizard
     *
     * @return redirect
     */
    public function wadd($profile_id = null)
    {
        $this->add($profile_id);
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Remove a domain from the blacklist
     *
     * @param string $zone: single domain to remove
     *
     * @return void Redirects to referer.
     */
    public function delete($zone = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $blacklist = $this->Blacklist->get($zone);

        if ($this->Blacklist->delete($blacklist)) {
            $this->Flash->success(__('Domain {0} has been deleted.', h($blacklist->zone)));
        } else {
            $this->Flash->error(__('Domain {0} could not be deleted.', h($blacklist->zone))." ".__('Please try again.'));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Delete a category and all domains it contains
     *
     * @param string|null $category
     *
     * @return void Redirects to referer.
     */
    public function deleteCategory($category)
    {
        $this->request->allowMethod(['post', 'delete']);
        if($this->Blacklist->deleteAll(['category' => $category])) {
            $this->loadModel('ProfilesBlacklists');
            $this->ProfilesBlacklists->deleteAll(['category' => $category]);

            $this->Flash->success(__('Category {0} has been deleted.', h($category)));
            return $this->redirect($this->referer());
        } else {
            $this->Flash->error(__('Unable to delete category {0}.', h($category))." ".__('Please try again.'));
        }
    }

    /**
     * Import blacklist from tar.gz file
     *
     * @return void 
     */
    public function import()
    {
        if ($this->request->is('post')) {
            // Save file on webroot/upload/
            if(move_uploaded_file($this->request->data['file']['tmp_name'], "upload/".$this->request->data['file']['name']))
            {
                exec($this->kxycmd("blacklist import ".WWW_ROOT."upload/".$this->request->data['file']['name']), $output, $rc);
                if($rc == 0) {
                    // clear Blacklist Cache
                    //$this->loadComponent('BlacklistCache');
                    //$this->BlacklistCache->ClearCache();

                    $this->Flash->success(__('Domains imported successfully to the Blacklist.'));
                } else {
                    $this->Flash->error(__('Unable to import domains to the Blacklist, please try with another file.'));
                }
                // Run pyhton script to import
            } else {
                $this->Flash->error(__('Unable to upload file.')." ".__('Please try again.'));
            }
        }
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * List all blacklist categories with number of domains per category
     * It also allows to delete or export categories
     *
     * @return void 
     */
    public function index()
    {

        // Create list of available categories
        $this->loadModel('Blacklist');
        $this->loadModel('ProfilesBlacklists');

        // Built categories list with domain counts per category and store in cache
        $this->loadComponent('BlacklistCache');

        if(($categories_count = $this->BlacklistCache->ReadCache('bl_websites_count')) === false or ($categories = $this->BlacklistCache->ReadCache('bl_categories_list')) === false) {
            $this->BlacklistCache->WriteCache();
            $categories_count = $this->BlacklistCache->ReadCache('bl_websites_count');
            $categories = $this->BlacklistCache->ReadCache('bl_categories_list');
        }

        if ($this->request->is('post')) {
            if( null !== $this->request->getData('check')) {
                if($this->request->getData('action') == 'delete') {
                    foreach($this->request->getData('check') as $category) {
                        $this->Blacklist->deleteAll(['category' => $category]);
                        $this->ProfilesBlacklists->deleteAll(['category' => $category]);
                    }
                }
                if($this->request->getData('action') == 'export') {
                    $categories_string = null;
                    foreach($this->request->getData('check') as $check) {
                        $categories_string .= "$check,";
                    }
                    $categories_string = rtrim($categories_string, ",");
                    $this->export($categories_string);
                }
                if($this->request->getData('action') == 'setcategory') {
                    if($this->request->getData('category') != '' or $this->request->getData('newcategory') != '') {
                        if($this->request->getData('newcategory') == '') {
                            //$data = ['category' => $this->request->data['category']];
                            $new_category = $this->request->getData('category');
                            
                        } else {
                            //$data = ['category' => $this->request->data['newcategory']];
                            $new_category = $this->request->getData('newcategory');
                        }
    
                        foreach($this->request->getData('check') as $old_category) {
                            // Update category for all domains in blacklist
                            $rename_query = "UPDATE blacklist SET category = '".$new_category."' WHERE category = '".$old_category."'";
                            $this->loadModel('Blacklist');
                            $connection = ConnectionManager::get('keexyboxblacklist');
                            $results = $connection->execute($rename_query); 
                            //exec($this->kxycmd("blacklist rename_category $old_category $new_category"), $output, $rc);

                            // Change category for all profiles that were using the category
                            $profile_bl_cat_update_query = "UPDATE profiles_blacklists SET category='".$new_category."' WHERE category='".$old_category."'";
                            $this->loadModel('ProfilesBlacklists');
                            $connection = ConnectionManager::get('default');
                            $results = $connection->execute($profile_bl_cat_update_query); 
                        }

                    } else {
                        $this->Flash->warning(__('Please define a category'));
                    }
                }
            }
        }

        $this->set('categories', $categories);
        $this->set('categories_count', $categories_count);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Export domains for categories
     *
     * @param string $categories : categories to exports seperate by ",". Example: ads,adult,telemetry 
     *
     * @return void 
     */
    public function export($categories) {
        $this->autoRender = false;

        $category = explode(",", $categories);
        $category = $category[0];

        $tar_achive = WWW_ROOT . "download/keexybox_bl_".$category.".tar.gz";
        
        exec($this->kxycmd("blacklist export $tar_achive $categories"), $output, $rc);

        // Change response Header
        $this->response->header('Content-Type', 'application/gzip');

        // Force download file
        $this->response->file(
            $tar_achive,
            ['download' => true, 'name' => "keexybox_bl_$category.tar.gz"]
            );
    }

    /**
     * This function allow to refresh Blacklist cache manually from blacklist/index page
     *
     * @return void Redirects to index
     */
    public function refresh()
    {
        $this->autoRender = false;
        $this->loadComponent('BlacklistCache');
        $this->BlacklistCache->ClearCache();
        return $this->redirect(['controller' => 'blacklist', 'action' => 'index']);
    }

    /**
     * Remove ALL Blacklist URL, it TRUNCATE the table
     *
     * @return void 
     */
    public function ClearBlacklist()
    {
        $this->autoRender = false;

        $this->request->allowMethod(['post', 'delete']);

        $this->loadModel('Blacklist');
        $connection = ConnectionManager::get('keexyboxblacklist');
        $results = $connection->execute('TRUNCATE TABLE blacklist'); 
        return $this->redirect($this->referer());
    }
}
