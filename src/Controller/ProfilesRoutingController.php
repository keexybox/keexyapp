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
use Cake\Event\Event;

/**
 * This class is use to manage domains routing
 * Domain routing allow to force routing thru tor or direct for a single or multiple domains
 * The routing flow is like this : 
 *    - "direct" : Device/User -> Internet
 *    - "tor"    : Device/User -> Tor Network -> Internet
 * 
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ProfilesRoutingController extends AppController
{
    /**
     * List and manage all domains routing for given profile
     *
     * @param integer $profile_id
     *
     * @return void
     */
    public function index($profile_id = null)
    {
        $this->loadModel('Profiles');
        $this->loadModel('ProfilesRouting');
        $this->loadComponent('Urlparser');
        $profiles = $this->Profiles->find('list');

        // Set value of search Query to null by default
        $this->set('search_query', null);

        // Create list of available categories
        $catresults = $this->ProfilesRouting->find()
                ->hydrate(false)
                ->select(['ProfilesRouting.category'])
                ->distinct(['ProfilesRouting.category']);

        foreach($catresults as $catresult) {
            $categories[$catresult['category']] = $catresult['category'];
        }

        // Getting Setting different links if URL is routing or filtering
        if($profile_id != null) {
            $ProfilesRouting = $this->ProfilesRouting->find('all', ['conditions' => ['ProfilesRouting.profile_id' => $profile_id]])->contain(['Profiles']);
            $links['add'] = 'add/'.$profile_id;
            $links['edit'] = 'edit/'.$profile_id;
            $links['import'] = 'import/'.$profile_id;
            $links['export'] = 'export/'.$profile_id;
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        } else {
            $ProfilesRouting = $this->ProfilesRouting->find('all')->contain(['Profiles']);
            //$ProfilesRouting = $this->ProfilesRouting->find('all');
            $addlink = 'add';
            $links['add'] = 'add';
            $links['edit'] = 'edit';
            $links['import'] = 'import';
            $links['export'] = 'export';
        }

        // SEARCH ENGINE
        if(isset($this->request->query['query'])) {
            if($this->request->query['action'] == 'search') {
                $query = $this->request->query['query'];
                if($profile_id != null) {
                    $ProfilesRouting = $this->ProfilesRouting->find()
                            ->where(['ProfilesRouting.address LIKE' => "%$query%"])
                            ->orWhere(['ProfilesRouting.category LIKE' => "%$query%"])
                            ->andWhere(['ProfilesRouting.profile_id' => $profile_id]);
                } else {
                    $ProfilesRouting = $this->ProfilesRouting->find()
                            ->where(['ProfilesRouting.address LIKE' => "%$query%"])
                            ->orWhere(['ProfilesRouting.category LIKE' => "%$query%"]);
                }

                $this->paginate = [
                    'contain' => ['Profiles'],
                    'limit' => $this->request->query['results'],
                ];

                // Set value of search Query to show search in view result
                $this->set('search_query', $query);
            }
        } else {
            $this->paginate = [
                'contain' => ['Profiles'],
                'limit' => 25,
            ];
        }
        if ($this->request->is('post')) {
            //debug($this->request->data);
            if(isset($this->request->data['check'])) {

                // ACTION DISABLE 
                if($this->request->data['action'] == 'disable') {
                    $data = ['enabled' => 0];

                        foreach($this->request->data['check'] as $check) {
                            $params = explode(";", $check);
                            $route = $this->ProfilesRouting->get($params[0]);
                            $route = $this->ProfilesRouting->patchEntity($route, $data);
                            $this->ProfilesRouting->save($route);
                        }
                    }

                // ACTION ENABLE 
                if($this->request->data['action'] == 'enable') {
                        $data = ['enabled' => 1];
                        foreach($this->request->data['check'] as $check) {
                            $params = explode(";", $check);
                            $route = $this->ProfilesRouting->get($params[0]);
                            $route = $this->ProfilesRouting->patchEntity($route, $data);
                            $this->ProfilesRouting->save($route);
                        }
                    }

                // ACTION COPY TO PROFILE 
                if($this->request->data['action'] == 'copyprofile') {
                    if($this->request->data['profile_id'] != '') {
                        //debug($this->request->data);
                        $data = ['profile_id' => $this->request->data['profile_id']];
    
                            foreach($this->request->data['check'] as $check) {
                                $params = explode(";", $check);
    
                            $website = $this->ProfilesRouting->get($params[0], [
                                'contain' => ['Profiles']]);
    
                            $route_exists = $this->ProfilesRouting->find('all', ['conditions' => [
                                    'address' => $website['address'],
                                    'profile_id' => $this->request->data['profile_id'],
                                    ]])->first();
    
                            if(!$route_exists) {
                                $route_data = [
                                    'address' => $website['address'],
                                    'routing' => $website['routing'],
                                    'category' => $website['category'],
                                    'profile_id' => $this->request->data['profile_id'],
                                    'enabled' => $website['enabled'],
                                    ];
                                $route = $this->ProfilesRouting->newEntity();
                                $route = $this->ProfilesRouting->patchEntity($route, $route_data);
                                $route = $this->ProfilesRouting->save($route);
                            } else {
                                $unsaved_urls[] = $website['address'];
                            }
                            }
                        if(isset($unsaved_urls)) {
                            $addresses = null;
                            foreach($unsaved_urls as $unsaved_url) {
                                $addresses .= $unsaved_url.", ";
                            }
                            $this->Flash->warning(__('Selected domain routings have been copied successfully.')." ".__('Except for the following addresses which already exist for the profile: {0}', h($addresses)));
                        } else {
                            $this->Flash->success(__('Selected domain routings have been copied successfully.'));
                        }
                    } else {
                        $this->Flash->warning(__('Please select a profile.'));
                    }
                    }

                // ACTION CHANGE CATEGORY 
                if($this->request->data['action'] == 'setcategory') {
                    if($this->request->data['category'] != '' or $this->request->data['newcategory'] != '') {
                        if($this->request->data['newcategory'] == '') {
                                $data = ['category' => $this->request->data['category']];
                            } else {
                                $data = ['category' => $this->request->data['newcategory']];
                            }
    
                            foreach($this->request->data['check'] as $check) {
                            $params = explode(";", $check);
                            $route = $this->ProfilesRouting->get($params[0]);
                            $route = $this->ProfilesRouting->patchEntity($route, $data);
                            $this->ProfilesRouting->save($route);
                            }
                        } else {
                            $this->Flash->warning(__('Please define a category'));
                        }
                    }

                // ACTION CHANGE ACCESS TYPE
                if($this->request->data['action'] == 'setrouting') {
                    if($this->request->data['routing'] != '') {
                        //debug($this->request->data);
                        $route_data['routing'] = $this->request->data['routing'];
                        foreach($this->request->data['check'] as $check) {
                            $params = explode(";", $check);
                            $route = $this->ProfilesRouting->get($params[0]);
                            $route = $this->ProfilesRouting->patchEntity($route, $route_data);
                            $this->ProfilesRouting->save($route);
                        }
                    } else {
                        $this->Flash->warning(__('Please select routing target.'));
                    }
                }

                // ACTION DELETE
                if($this->request->data['action'] == 'delete') {
                    foreach($this->request->data['check'] as $check) {
                        $params = explode(";", $check);
                            $route = $this->ProfilesRouting->get($params[0]);
                            $this->ProfilesRouting->delete($route);
                        }
                    }
            } else {
                $this->Flash->warning(__('Nothing was selected.'));
            }
        }

        if(isset($categories)) {
            $this->set('categories', $categories);
        } else {
            $this->set('categories', null);
        }

        $this->set('ProfilesUrls', $this->paginate($ProfilesRouting));
        //$this->set('ProfilesUrls', $ProfilesUrls);
        $this->set('links', $links);
        $this->set('profiles',$profiles);
        $this->set('_serialize', ['ProfilesUrls']);
        //$this->viewBuilder()->setLayout('adminlte');
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Add multiple domains routing
     *
     * @return void
     */
    public function add($profile_id = null)
    {
        $this->LoadModel('ProfilesRouting');
        $this->LoadModel('Profiles');
        $this->loadComponent('Urlparser');
        if(isset($profile_id)) {
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        }
        $catresults = $this->ProfilesRouting->find()
                ->hydrate(false)
                ->select(['category'])
                ->distinct(['category']);

        foreach($catresults as $catresult) {
            $categories[$catresult['category']] = $catresult['category'];
        }

        // I have to check if this can be removed
        $query_profile_id = $this->request->query('profile_id');
        $query_urls = $this->request->query('urls');
        if(isset($query_urls)) {
            $this->set('prefill_urls', $query_urls);

        }
        if(isset($query_profile_id)) {
            return $this->redirect(['controller' => 'profiles-routing', 'action' => 'add', $query_profile_id, 'urls' => $query_urls]);
        }
        // End I have to check if this can be removed

        if ($this->request->is('post')) {
            //debug($this->request->data);
            //$urls = preg_split("/[\s,]+/", trim($this->request->data['urls']));
            $urls = preg_split("/[\n\r\t\ ]+/", trim($this->request->data['urls']));
            unset($this->request->data['urls']);

            // Define Category
            if($this->request->data['category'] == '' and $this->request->data['newcategory'] == '') {
                $this->request->data['category'] = 'default';
            }
            elseif(!isset($this->request->data['newcategory']) or $this->request->data['newcategory'] != '') {
                $this->request->data['category'] = $this->request->data['newcategory'];
                unset($this->request->data['newcategory']);
            } else {
                unset($this->request->data['newcategory']);
            }


            // Set values for profilerouting
            $routing_data = $this->request->data;

            // Set values for profileurlfilter
            $filter_data = $this->request->data;

            //debug($this->request->data);

            foreach($urls as $url) {
                $parsedurl = $this->Urlparser->Parseurl($url);
                if(isset($parsedurl['fqdn']) and $parsedurl['fqdn'] != '') {
                    // Setting address for routing
                    $routing_data['address'] = $parsedurl['fqdn'];

                    // Setting address, url and type for filtering
                    $filter_data['address'] = $parsedurl['url'];
                    $filter_data['type'] = $parsedurl['type'];

                    // Ip of FQDN Validation. We create newEntity just for validation. It will be reset later
                    $route = $this->ProfilesRouting->newEntity();
                    $address_ok = 0;
                    $route = $this->ProfilesRouting->patchEntity($route, $routing_data, ['validate' => 'ipaddr']);
                    if($route->errors()) { $address_ok++; }
                    $route = $this->ProfilesRouting->patchEntity($route, $routing_data, ['validate' => 'fqdn']);
                    if($route->errors()) { $address_ok++; }

                    if($address_ok < 2) {

                        if($filter_data['replace'] == false) {
                            $route = $this->ProfilesRouting->find('all', ['conditions' => [
                                'address' => $routing_data['address'],
                                'profile_id' => $routing_data['profile_id'],
                            ]])->first();
                            if(!isset($route['address'])) {
                                $route = $this->ProfilesRouting->newEntity();
                                $route = $this->ProfilesRouting->patchEntity($route, $routing_data);
                                if(!$this->ProfilesRouting->save($route)) {
                                    $unsaved_urls[] = $url;
                                }
                            }

                        } else {
                            $route = $this->ProfilesRouting->find('all', ['conditions' => [
                                'address' => $routing_data['address'],
                                'profile_id' => $routing_data['profile_id'],
                            ]])->first();
                            if(!isset($route['address'])) {
                                $route = $this->ProfilesRouting->newEntity();
                                $route = $this->ProfilesRouting->patchEntity($route, $routing_data);
                                if(!$this->ProfilesRouting->save($route)) {
                                    $unsaved_urls[] = $url;
                                }
                            } else {
                                $route = $this->ProfilesRouting->patchEntity($route, $routing_data);
                                if(!$this->ProfilesRouting->save($route)) {
                                    $unsaved_urls[] = $url;
                                }
                            }
                        }
                    } else {
                        $unsaved_urls[] = $url;
                    }
                } else {
                    $unsaved_urls[] = $url;
                }
            }

            $addresses = null;
            if(isset($unsaved_urls)) {
                foreach($unsaved_urls as $unsaved_url) {
                    $addresses .= $unsaved_url.", ";
                }
                // Remove last ","
                $addresses = rtrim($addresses,", ");
                $this->Flash->warning(__('Domain routings saved successfully.')." ".__('Except for the following addresses: {0}', h($addresses)));
            } else {
                $this->Flash->success(__('Domain routings saved successfully.'));
            }
            return $this->redirect(['controller' => 'profiles-routing', 'action' => 'index', $profile_id]);
        }

        $profiles = $this->Profiles->find('list', ['limit' => 200]);
        if($profile_id != null) {
            $this->set('profile_id', $profile_id);
        }
        if(isset($categories)) {
            $this->set('categories', $categories);
        } else {
            $this->set('categories', null);
        }
        $this->set(compact('url', 'profiles'));
        $this->set('_serialize', ['url']);
        //$this->viewBuilder()->setLayout('adminlte');
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Edit single domain routing
     *
     * @param integer $id : ID of rule
     * @param string|null $id Profiles Routing id.
     *
     * @return void Redirects on successful edit, renders view otherwise.
     *
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null, $profile_id = null)
    {
        //$this->LoadModel('Urls');
        $this->LoadModel('Profiles');
        $this->loadComponent('Urlparser');


        $profilesRouting = $this->ProfilesRouting->get($id);

        if(isset($profile_id)) {
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        }

        // set old profile id to use it for post
        $old_profile_id = $profilesRouting['profile_id'];

        $catresults = $this->ProfilesRouting->find()
                ->hydrate(false)
                ->select(['ProfilesRouting.category'])
                ->distinct(['ProfilesRouting.category']);

        foreach($catresults as $catresult) {
            $categories[$catresult['category']] = $catresult['category'];
        }

        if ($this->request->is(['patch', 'post', 'put'])) {

            $route = $profilesRouting;
            // Define Category
            if($this->request->data['category'] == '' and $this->request->data['newcategory'] == '') {
                $this->request->data['category'] = 'default';
            }
            elseif(!isset($this->request->data['newcategory']) or $this->request->data['newcategory'] != '') {
                $this->request->data['category'] = $this->request->data['newcategory'];
                unset($this->request->data['newcategory']);
            } else {
                unset($this->request->data['newcategory']);
            }

            $routing_data = $this->request->data;

            // Ip of FQDN Validation. We create newEntity just for validations.
            $address_ok = 0;
            $route_check = $this->ProfilesRouting->newEntity();
            $route_check = $this->ProfilesRouting->patchEntity($route_check, $routing_data, ['validate' => 'ipaddr']);
            if($route_check->errors()) { $address_ok++; }
            $route_check = $this->ProfilesRouting->patchEntity($route_check, $routing_data, ['validate' => 'fqdn']);
            if($route_check->errors()) { $address_ok++; }

            if($address_ok < 2) {
                $route = $this->ProfilesRouting->patchEntity($route, $routing_data);
                $this->ProfilesRouting->save($route);
                $this->Flash->success(__('Domain routings saved successfully.'));
                if($profile_id != null) {
                    return $this->redirect(['controller' => 'profiles-routing', 'action' => 'index', $profile_id]);
                } else {
                    return $this->redirect(['controller' => 'profiles-routing', 'action' => 'index']);
                }
            } else {
                $this->Flash->error(__('Domain routings have not been saved.'));
                $this->Flash->set(__('Invalid address'), [ 
                    'key' => 'error_address',
                    'element' => 'custom_error' ]);
            }
        }

        $profiles = $this->ProfilesRouting->Profiles->find('list', ['limit' => 200]);
        if($profile_id != null) {
            $this->set('profile_id', $profile_id);
        }
        if(isset($categories)) {
            $this->set('categories', $categories);
        } else {
            $this->set('categories', null);
        }

        $this->set(compact('profilesRouting', 'profiles'));
        $this->set('_serialize', ['profilesRouting']);
        //$this->viewBuilder()->setLayout('adminlte');
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Delete domain routing rule
     *
     * @param integer $id : ID of rule
     *
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $profilesRouting = $this->ProfilesRouting->get($id);

        if ($this->ProfilesRouting->delete($profilesRouting)) {
            $this->Flash->success(__('Domain routing {0} has been deleted.', h($profilesRouting->address)));
        } else {
            $this->Flash->error(__('Domain routing {0} could not be deleted.', h($profilesRouting->address))." ".__('Please try again.'));
        }
        return $this->redirect($this->referer());
    }
}
