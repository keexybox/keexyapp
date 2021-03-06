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

use Cake\Event\Event;
use App\Controller\AppController;
//use Cake\I18n\I18n;

/**
 * ActivesConnections Controller
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \App\Model\Table\ActivesConnectionsTable $ActivesConnections
 */
class ConnectionsController extends AppController
{

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['view', 'offline']);
    }
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->loadModel('ActivesConnections');
        $activesconn = $this->ActivesConnections->find('all')
            ->contain(['Profiles', 'Users', 'Devices']);

        // Set value of search Query to null by default
        $this->set('search_query', null);

        if( null !== $this->request->getQuery('query')) {
             if($this->request->query['action'] == 'search') {
    
                 $query = $this->request->query['query'];
                 $activesconn = $this->ActivesConnections->find()
                    ->where(['ActivesConnections.name LIKE' => "%$query%"])
                    ->orWhere(['ActivesConnections.ip LIKE' => "%$query%"]);
    
                $this->paginate = [
                    'contain' => ['Profiles', 'Users', 'Devices'],
                    'limit' => $this->request->query['results'],
                ];
            }
            // Set value of search Query to show search in view result
            $this->set('search_query', $query);
        } else {
            $this->paginate = [
                'contain' => ['Profiles', 'Users', 'Devices'],
                'limit' => 25
            ];
        }

        if ($this->request->is('post')) {

            if($this->request->data['action'] == 'search') {
                $query = $this->request->data['query'];
                $activesconn = $this->ActivesConnections->find()
                    ->where(['ActivesConnections.name LIKE' => "%$query%"])
                    ->orWhere(['ActivesConnections.ip LIKE' => "%$query%"]);
              }

            if($this->request->data['action'] == 'run') {
                if(isset($this->request->data['check'])) {
                    foreach($this->request->data['check'] as $client) {
                        $params = explode(";", $client);
                        if($params[2] == 'dev') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("devices run $name $ip"), $output, $rc);
                          } 
                        elseif ($params[2] == 'usr') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("users run $name $ip"), $output, $rc);
                        }
                    }
                }
            }

            if($this->request->data['action'] == 'pause') {
                if(isset($this->request->data['check'])) {
                    foreach($this->request->data['check'] as $client) {
                        $params = explode(";", $client);
                        if($params[2] == 'dev') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("devices pause $name $ip"), $output, $rc);
                          } 
                        elseif ($params[2] == 'usr') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("users pause $name $ip"), $output, $rc);
                        }
                    }
                }
            }

            if($this->request->data['action'] == 'disconnect') {
                if(isset($this->request->data['check'])) {
                    foreach($this->request->data['check'] as $client) {
                        $params = explode(";", $client);
                        if($params[2] == 'dev') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("devices disconnect $name $ip"), $output, $rc);
                          } 
                        elseif ($params[2] == 'usr') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("users disconnect $name $ip"), $output, $rc);
                        }
                    }
                }
            }

            if($this->request->data['action'] == 'reconnect') {
                if(isset($this->request->data['check'])) {
                    foreach($this->request->data['check'] as $client) {
                        $params = explode(";", $client);
                        if($params[2] == 'dev') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("devices reconnect $name $ip"), $output, $rc);
                        } 
                        elseif ($params[2] == 'usr') {
                            $name = $params[0];
                            $ip = $params[1];
                            exec($this->kxycmd("users reconnect $name $ip"), $output, $rc);
                        }
                      }
                  }
            }
        }
        //debug($this->paginate($activesconn));
        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        $this->set('timezone', $timezone);

        $this->set('activesConnections', $this->paginate($activesconn));
        $this->set('_serialize', ['activesConnections']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    public function view()
    {
        $this->loadModel('ActivesConnections');
        $this->loadModel('ConnectionsHistory');
        $this->loadModel('ProfilesTimes');
        $this->loadModel('ProfilesRouting');
        $this->loadModel('ProfilesIpfilters');
        $this->loadModel('ProfilesBlacklists');
        $this->loadModel('Config');

        $this->set('cportal_check_tor_url', $this->Config->get('cportal_check_tor_url')->value);
        $this->set('cportal_homepage_button_name', $this->Config->get('cportal_homepage_button_name')->value);
        $this->set('cportal_homepage_button_url', $this->Config->get('cportal_homepage_button_url')->value);
        $this->set('cportal_ip_info_url', $this->Config->get('cportal_ip_info_url')->value);
        $this->set('cportal_brand_name', $this->Config->get('cportal_brand_name')->value);
        $this->set('cportal_brand_logo_url', $this->Config->get('cportal_brand_logo_url')->value);

        $ip = env('REMOTE_ADDR');
        $connection = $this->ActivesConnections->findByIp($ip)->contain(['Profiles'])->first();
        
        // Check blocked domain
        $search_domain = null;
        $suggested_domain_to_remove = null;
        if(null !== $this->request->getQuery('domain')) {

            $search_domain = $this->request->getQuery('domain');
            //$dns_results = null;

            $this->loadComponent('Urlparser');
            $parsedurl = $this->Urlparser->Parseurl($search_domain);
            $search_domain = $parsedurl['fqdn'];

            // Requested domain will be checked first
            $domains_to_check_in_bl[] = $search_domain; 

            // Build a list of CNAME records for related $search_domain
            $dns_results = dns_get_record($search_domain, DNS_CNAME);
            while($dns_results != null) {
                foreach($dns_results as $dns_result) {
                    if(isset($dns_result['type']) and $dns_result['type'] == 'CNAME') {
                        $domains_to_check_in_bl[] = $dns_result['target'];
                        $dns_results = dns_get_record($dns_result['target'], DNS_CNAME);
                    }
                }
            }

            $this->loadModel('Blacklist');

            // Build a list of CNAME records that can be in the Blacklist
            if(isset($domains_to_check_in_bl)) {
                foreach($domains_to_check_in_bl as $domain) {
                    // For each domain, checking if it is in BL from ROOT dns name
                    //   for example : check .org then keexybox.com and the www.keexybox.com
                    $split_domain = array_reverse(explode(".", $domain));
                    $nb_sub_domain = count($split_domain);
                    $i = 0;
                    $chk_sub_domain = $split_domain[0];
                    while($i < $nb_sub_domain) {
                        $bl_domain = $this->Blacklist->findByZone($chk_sub_domain)->first();
                        if(isset($bl_domain)) {
                            $suggested_domain_to_remove[] = $bl_domain;
                        }
                        $i++;
                        if($i < $nb_sub_domain) {
                            $chk_sub_domain = $split_domain[$i].".".$chk_sub_domain;
                        }
                    }
                }
            }
        }
        // END Check blocked domain

        if(isset($connection)) {
            $connection->profile_times = $this->ProfilesTimes->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->toArray();
            $connection->profile_routing = $this->ProfilesRouting->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->order(['address' => 'ASC'])->toArray();
            $connection->profile_ipfilters = $this->ProfilesIpfilters->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->order(['rule_number' => 'ASC'])->toArray();
            $connection->profile_blacklists = $this->ProfilesBlacklists->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->order(['category' => 'ASC'])->toArray();

            if(isset($connection['type'])) {
                if ($connection['type'] == 'usr') {
                    $connection['type'] = 'User';
                } elseif ($connection['type'] == 'dev') {
                    $connection['type'] = 'Device';
                }
            }

            $this->set('connection', $connection);

            $this->loadModel('Config');
            $timezone = $this->Config->get('host_timezone');
            $timezone = $timezone['value'];
            $this->set('timezone', $timezone);

        } else {
            return $this->redirect(['controller' => 'users', "action" => 'login']);
        }
        $this->set('search_domain', $search_domain);
        $this->set('bl_domains', $suggested_domain_to_remove);
        //$this->viewBuilder()->setLayout('connection_view');
        $this->viewBuilder()->setLayout('connection_view');
    }

    // This show user/device that is or was connected with given IP on given Date Time
    public function adminview()
    {
        $this->loadModel('ActivesConnections');
        $this->loadModel('ConnectionsHistory');
        $this->loadModel('ProfilesTimes');
        $this->loadModel('ProfilesRouting');
        $this->loadModel('ProfilesIpfilters');
        $this->loadModel('ProfilesBlacklists');
        $this->loadModel('Config');

        if(isset($this->request->query['client_ip']) and isset($this->request->query['date_time'])) {

            $q['ip'] = $this->request->query['client_ip'];
            if(isset($this->request->query['date_time'])) {
                $q['display_start_time <='] = $this->request->query['date_time'];
                $q['display_end_time >='] = $this->request->query['date_time'];
            }

            $connection = $this->ActivesConnections->find()
                //->contain(['ProfilesTimes', 'Profiles', 'ProfilesRouting', 'ProfilesIpfilters'])
                ->contain(['Profiles'])
                ->where($q)->first();

            $client_details = null;
            if(isset($connection->client_details)) {
                $client_details = json_decode($connection->client_details);
            }

            if($connection == null) {
                $connection = $this->ConnectionsHistory->find()
                    ->contain(['Profiles'])
                    ->where($q)->first();
            } 

            if($connection != null) {
                $connection->profile_times = $this->ProfilesTimes->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->toArray();
                $connection->profile_routing = $this->ProfilesRouting->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->order(['address' => 'ASC'])->toArray();
                $connection->profile_ipfilters = $this->ProfilesIpfilters->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->order(['rule_number' => 'ASC'])->toArray();
                $connection->profile_blacklists = $this->ProfilesBlacklists->find("all", ["conditions" => ["profile_id" => $connection->profile_id]])->order(['category' => 'ASC'])->toArray();
            } else {
                $this->Flash->error(__('No connection information found for this IP address.'));
                return $this->redirect($this->referer());
            }

            if(isset($connection['type'])) {
                if($connection['type'] == 'usr') {
                    $connection['type'] = 'User';
                } elseif ($connection['type'] == 'dev') {
                    $connection['type'] = 'Device';
                }
            }

            $this->set('connection', $connection);
        } else {
            return $this->redirect($this->referer());
        }

        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        $this->set('client_details', $client_details);
        $this->set('timezone', $timezone);

        $this->viewBuilder()->setLayout('adminlte');
    }

    public function history()
    {
        $this->loadModel('ConnectionsHistory');
        $connhistory = $this->ConnectionsHistory->find('all')
            ->contain(['Profiles', 'Users', 'Devices']);

        // Set value of search Query to null by default
        $this->set('search_query', null);

        if(isset($this->request->query['action']) and $this->request->query['action'] == 'search') {
    
             $query = $this->request->query['query'];
             $connhistory = $this->ConnectionsHistory->find()
                ->where(['ConnectionsHistory.name LIKE' => "%$query%"])
                ->orWhere(['ConnectionsHistory.ip LIKE' => "%$query%"]);

            $this->paginate = [
                'contain' => ['Profiles', 'Users', 'Devices'],
                'limit' => $this->request->query['results'],
                'order' => ['end_time' => 'DESC']
            ];
            $this->set('search_query', $query);
        }
        elseif(isset($this->request->query['action']) and $this->request->query['action'] == 'filter') {
            //SELECT * FROM `connections_history` WHERE `ip` = '192.168.1.18' AND `display_start_time` < '2017-12-13 08:00:00' AND `display_end_time` > '2017-12-13 08:00:00' 

             $connhistory = $this->ConnectionsHistory->find()
                ->where([
                        'ip' => $this->request->query['client_ip'],
                        'display_start_time <' => $this->request->query['date_time'],
                        'display_end_time >' => $this->request->query['date_time'],
                        ]);
    
            $this->paginate = [
                'contain' => ['Profiles', 'Users', 'Devices'],
                'limit' => $this->request->query['results'],
                'order' => ['end_time' => 'DESC']
            ];

            $this->paginate = [
                'contain' => ['Profiles', 'Users', 'Devices'],
                'limit' => 25,
                'order' => ['end_time' => 'DESC']
            ];
        }
        else {
            $this->paginate = [
                'contain' => ['Profiles', 'Users', 'Devices'],
                'limit' => 25,
                'order' => ['end_time' => 'DESC']
            ];
        }

        if ($this->request->is('post')) {

            if($this->request->data['action'] == 'search') {
                $query = $this->request->data['query'];
                $connhistory = $this->ConnectionsHistory->find()
                    ->where(['ConnectionsHistory.name LIKE' => "%$query%"])
                    ->orWhere(['ConnectionsHistory.ip LIKE' => "%$query%"]);
              }

            if($this->request->data['action'] == 'delete') {
                if(isset($this->request->data['check'])) {
                    foreach($this->request->data['check'] as $id) {
                        $conn_history = $this->ConnectionsHistory->get($id);
                        $this->ConnectionsHistory->delete($conn_history);
                    }
                }
            }
        }
        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];
        $this->set('timezone', $timezone);

        $this->set('connectionsHistory', $this->paginate($connhistory));
        $this->set('_serialize', ['activesConnections']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    public function offline()
    {
    }

    /**
     * Delete method
     *
     * @param string|null $id Connections History id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteHistory($id = null)
    {
        $this->autoRender = false;
        $this->request->allowMethod(['post', 'delete']);
        $this->loadModel('ConnectionsHistory');
        $connectionsHistory = $this->ConnectionsHistory->get($id);
        if ($this->ConnectionsHistory->delete($connectionsHistory)) {
            $this->Flash->success(__('The connection history has been deleted.'));
        } else {
            $this->Flash->error(__('The connection history could not be deleted.')." ".__('Please try again.'));
        }
        return $this->redirect(['action' => 'history']);
    }
}
