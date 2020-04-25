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

/**
 * This class allows to manage Firewall rules of a profile
 *
 * @property \App\Model\Table\ProfilesIpfiltersTable $ProfilesIpfilters
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ProfilesIpfiltersController extends AppController
{

    /**
     * List profile firewall rules and allow bulk management of rules 
     *
     * @param integer $profile_id
     * @return void
     */
    public function index($profile_id = null)
    {
        $this->loadModel('Profiles');
        $this->loadModel('ProfilesIpfilters');
        $this->loadComponent('IpfiltersRulesSort');
        $profiles = $this->Profiles->find('list');

        $rules_list = null;
        $rules_list = $this->ProfilesIpfilters->find('list', ['conditions' => ['ProfilesIpfilters.profile_id' => $profile_id]])->order(['rule_number' => 'ASC']);

        // Set value of search Query to null by default
        $this->set('search_query', null);

        if(isset($this->request->query['o']) and isset($profile_id)) {
            // Force reset ordering before running sorting query
            $this->IpfiltersRulesSort->force_sort_rules($profile_id);

            // Sorting
            foreach($this->request->query['o'] as $key => $order) {
                $var = explode(':', $order);
                $rules_seq_numbers[$var[0]] = $var[0];
                $rules_id[] = $var[1];
            }
            // sorting rules numbers, this part permit do keep existing rule_number for sorting
            ksort($rules_seq_numbers);
            // Combine rule_id order to rules number
            $new_sort = array_combine($rules_seq_numbers, $rules_id);

            foreach($new_sort as $rule_number => $rule_id) {
                //debug($order[0]);
                // Get rules ID
                $ipfilter = $this->ProfilesIpfilters->get($rule_id);
                $rule_data['rule_number'] = $rule_number;
                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $rule_data);
                $this->ProfilesIpfilters->save($ipfilter);
            }

            // Force reset ordering after running sorting query
            $this->IpfiltersRulesSort->force_sort_rules($profile_id);
            return $this->redirect($this->referer());
        }

        if($profile_id != null) {
            $ProfilesIpfilters = $this->ProfilesIpfilters->find('all', ['conditions' => ['ProfilesIpfilters.profile_id' => $profile_id]])
                                        ->contain(['Profiles'])
                                        ->order(['rule_number' => 'ASC']);
            $links['add'] = 'add/'.$profile_id;
            $links['edit'] = 'edit/'.$profile_id;
            $links['import'] = 'import/'.$profile_id;
            $links['export'] = 'export/'.$profile_id;
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        } else {
            $ProfilesIpfilters = $this->ProfilesIpfilters->find('all')->contain(['Profiles']);
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
                    $ProfilesIpfilters = $this->ProfilesIpfilters->find()
                           ->where(['ProfilesIpfilters.dest_ip LIKE' => "%$query%"])
                           ->orWhere(['ProfilesIpfilters.dest_iprange_first LIKE' => "%$query%"])
                           ->orWhere(['ProfilesIpfilters.dest_iprange_last LIKE' => "%$query%"])
                           ->orWhere(['ProfilesIpfilters.dest_hostname LIKE' => "%$query%"])
                           ->andWhere(['ProfilesIpfilters.profile_id' => $profile_id])
                        ->order(['rule_number' => 'ASC']);
                 } else {
                    $ProfilesIpfilters = $this->ProfilesIpfilters->find()
                           ->where(['ProfilesIpfilters.dest_ip LIKE' => "%$query%"])
                           ->orWhere(['ProfilesIpfilters.dest_iprange_first LIKE' => "%$query%"])
                           ->orWhere(['ProfilesIpfilters.dest_iprange_last LIKE' => "%$query%"])
                           ->orWhere(['ProfilesIpfilters.dest_hostname LIKE' => "%$query%"])
                        ->order(['rule_number' => 'ASC']);
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
                        $ipfilter = $this->ProfilesIpfilters->get($params[0]);
                        $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $data);
                        $this->ProfilesIpfilters->save($ipfilter);
                      }
                  }

                // ACTION ENABLE 
                if($this->request->data['action'] == 'enable') {
                      $data = ['enabled' => 1];
                      foreach($this->request->data['check'] as $check) {
                          $params = explode(";", $check);
                        $ipfilter = $this->ProfilesIpfilters->get($params[0]);
                        $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $data);
                        $this->ProfilesIpfilters->save($ipfilter);
                      }
                  }

                if($this->request->data['action'] == 'move_before') {
                    if($this->request->data['move_before_rule_id'] != '') {
                        $target_position = $this->ProfilesIpfilters->get($this->request->data['move_before_rule_id']);
                        $position = $target_position['rule_number'] - 1;
                        $gap = count($this->request->data['check']);
    
                        //rules shifting
                        $rules_to_shift = $this->ProfilesIpfilters->find('all', ['conditions' => [
                                'ProfilesIpfilters.profile_id' => $profile_id,
                                'ProfilesIpfilters.rule_number >' => $position,
                            ]])->order(['rule_number' => 'ASC']);
    
                        foreach($rules_to_shift as $rule_to_shift) {
                            $ipfilter_data['rule_number'] = $rule_to_shift['rule_number'] + $gap;
    
                            $ipfilter = $this->ProfilesIpfilters->get($rule_to_shift['id']);
                            if($ipfilter['dest_ip_type'] == 'net') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'net']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'range') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'iprange']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'hostname') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'fqdn']);
                            }
                            $this->ProfilesIpfilters->save($ipfilter);
                        }
    
                        // Inserting rules
                        foreach($this->request->data['check'] as $rule_id) {
                            $position++;
                            $ipfilter_data['rule_number'] = $position;
                            //debug($rule_id);
                            //debug($ipfilter_data['rule_number']);
    
                            $ipfilter = $this->ProfilesIpfilters->get($rule_id);
                            if($ipfilter['dest_ip_type'] == 'net') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'net']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'range') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'iprange']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'hostname') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'fqdn']);
                            }
                            $this->ProfilesIpfilters->save($ipfilter);
                        }
                        $this->IpfiltersRulesSort->force_sort_rules($ipfilter['profile_id']);
                    } else {
                        $this->Flash->warning(__('Please select rule position.'));
                    }
                }

                if($this->request->data['action'] == 'move_after') {
                    if($this->request->data['move_after_rule_id'] != '') {
                        $target_position = $this->ProfilesIpfilters->get($this->request->data['move_after_rule_id']);
                        //debug($target_position);
                        $position = $target_position['rule_number'];
                        $gap = count($this->request->data['check']);
    
                        //rules shifting
                        $rules_to_shift = $this->ProfilesIpfilters->find('all', ['conditions' => [
                                'ProfilesIpfilters.profile_id' => $profile_id,
                                'ProfilesIpfilters.rule_number >' => $position,
                            ]])->order(['rule_number' => 'ASC']);
    
                        foreach($rules_to_shift as $rule_to_shift) {
                            $ipfilter_data['rule_number'] = $rule_to_shift['rule_number'] + $gap;
    
                            $ipfilter = $this->ProfilesIpfilters->get($rule_to_shift['id']);
                            if($ipfilter['dest_ip_type'] == 'net') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'net']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'range') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'iprange']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'hostname') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'fqdn']);
                            }
                            $this->ProfilesIpfilters->save($ipfilter);
                        }
    
                        // Inserting rules
                        foreach($this->request->data['check'] as $rule_id) {
                            $position++;
                            $ipfilter_data['rule_number'] = $position;
    
                            $ipfilter = $this->ProfilesIpfilters->get($rule_id);
                            if($ipfilter['dest_ip_type'] == 'net') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'net']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'range') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'iprange']);
                            }
                            elseif($ipfilter['dest_ip_type'] == 'hostname') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'fqdn']);
                            }
                            $this->ProfilesIpfilters->save($ipfilter);
                        }
                        $this->IpfiltersRulesSort->force_sort_rules($ipfilter['profile_id']);
                    } else {
                        $this->Flash->warning(__('Please select rule position.'));
                    }
                }

                // ACTION COPY TO PROFILE 
                if($this->request->data['action'] == 'copyprofile') {
                    if($this->request->data['profile_id'] != '') {
                          foreach($this->request->data['check'] as $rule_id) {
    
                            $ipfilter_data = $this->ProfilesIpfilters->get($rule_id)->toArray();
    
                            $ipfilter_data['profile_id'] = $this->request->data['profile_id'];
                            unset($ipfilter_data['id'], $ipfilter_data['rule_number']);
    
                            $ipfilter = $this->ProfilesIpfilters->newEntity();
    
                            if($ipfilter_data['dest_ip_type'] == 'net') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'net']);
                            }
                            elseif($ipfilter_data['dest_ip_type'] == 'range') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'iprange']);
                            }
                            elseif($ipfilter_data['dest_ip_type'] == 'hostname') {
                                $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'fqdn']);
                            }
                            $ipfilter = $this->ProfilesIpfilters->save($ipfilter);
                          }
    
                        $this->IpfiltersRulesSort->force_sort_rules($ipfilter_data['profile_id']);
                    } else {
                        $this->Flash->warning(__('Please select a profile.'));
                    }
                  }

                // ACTION DELETE
                if($this->request->data['action'] == 'delete') {
                    foreach($this->request->data['check'] as $check) {
                        $params = explode(";", $check);
                          $ipfilter = $this->ProfilesIpfilters->get($params[0]);
                           $this->ProfilesIpfilters->delete($ipfilter);
                      }
                  }
             } else {
                $this->Flash->warning(__('Nothing was selected.'));
            }
        }

        $this->set('ProfilesIpfilters', $this->paginate($ProfilesIpfilters));
        $this->set('links', $links);
        $this->set('profiles', $profiles);
        $this->set('_serialize', ['ProfilesIpfilters']);
        $this->set('rules_list', $rules_list);
        //$this->viewBuilder()->setLayout('adminlte');
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Add a new target firewall rule for given profile source
     * Users and devices that are set to a profile are always the source of a connection
     *
     * @param integer $profile_id
     * @return void Redirects to index page
     */
    public function add($profile_id = null)
    {
        $this->LoadModel('Profiles');
        $this->loadComponent('PortsParser');
        $this->loadComponent('IpfiltersRulesSort');

        if(isset($profile_id)) {
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        }
        $profilesIpfilter = $this->ProfilesIpfilters->newEntity();
        $mask_numbers = range(32,0);

        foreach($mask_numbers as $mask_number)
        {
            $mask_range[$mask_number] = $mask_number;
        }


        if ($this->request->is('post')) {

            if(isset($this->request->query['rule_number'])) {
                $rule_number = $this->request->query['rule_number'];
            } else {
                $rule_number = 0;
            }

            $data_ipfilter = $this->request->data;
            $data_ipfilter['rule_number'] = $rule_number;

            // Set port string
            $ports = $this->PortsParser->set_ports_string($data_ipfilter['dest_ports']);

            $data_ipfilter['dest_ports'] = $ports['accepted'];
            
            if($this->request->data['dest_ip_type'] == 'net') {
                unset($data_ipfilter['dest_iprange_first'], $data_ipfilter['dest_iprange_last'], $data_ipfilter['dest_hostname']);
                $profilesIpfilter = $this->ProfilesIpfilters->patchEntity($profilesIpfilter, $data_ipfilter,
                        ['validate' => 'net']
                        );
            } 
            elseif($this->request->data['dest_ip_type'] == 'range') {
                unset($data_ipfilter['dest_ip'], $data_ipfilter['dest_ip_mask'], $data_ipfilter['dest_hostname']);
                if(ip2long($data_ipfilter['dest_iprange_first']) < ip2long($data_ipfilter['dest_iprange_last']))
                {
                    $profilesIpfilter = $this->ProfilesIpfilters->patchEntity($profilesIpfilter, $data_ipfilter,
                        ['validate' => 'iprange']
                        );
                } else {
                       $this->Flash->set(__('Last IP must be greater than first IP'), [ 
                           'key' => 'error_iprange',
                           'element' => 'custom_error' ]);
                }
            }

            if($ports['invalid'] == '') {
                   if ($this->ProfilesIpfilters->save($profilesIpfilter)) {
                    $this->IpfiltersRulesSort->force_sort_rules($profile_id);

                      $this->Flash->success(__('Firewall rule has been saved.'));
    
                    if($profile_id != null) {
                        return $this->redirect(['action' => 'index', $profile_id]);
                    } else {
                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                      $this->Flash->error(__('Firewall rule could not be saved.')." ".__('Please try again.'));
                   }
            } else {
                $this->Flash->error(__("Firewall rule could not be saved.")." ".__("Following ports are invalid: ".$ports['invalid']));
            }
        }
        $profiles = $this->ProfilesIpfilters->Profiles->find('list', ['limit' => 200]);
        $this->set('mask_range', $mask_range);
        $this->set(compact('profilesIpfilter', 'profiles'));
        $this->set('_serialize', ['profilesIpfilter']);
        //$this->viewBuilder()->setLayout('adminlte');
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Edit target firewall rule for given profile source
     * Users and devices that are set to a profile are always the source of a connection
     *
     * @param integer $id : ID of rule
     * @param integer $profile_id: ID of profile
     * @return void Redirects to index page
     */
    public function edit($id = null, $profile_id = null)
    {
        $this->LoadModel('Profiles');
        $this->loadComponent('PortsParser');
        $this->loadComponent('IpfiltersRulesSort');

        if(isset($profile_id)) {
            $profile = $this->Profiles->get($profile_id);
            $this->set('profile', $profile);
        }

        $profilesIpfilter = $this->ProfilesIpfilters->get($id, [
            'contain' => ['Profiles']
        ]);

        $expl_ports = explode(',', $profilesIpfilter->dest_ports);
        foreach($expl_ports as $expl_port) {
            $expl_range = explode(':', $expl_port);
            if(isset($expl_range[0]) and isset($expl_range[1])) {
                $dest_ports[] = ['port' => $expl_range[0], 'last_port' => $expl_range[1]];
            } elseif(isset($expl_range[0]) and !isset($expl_range[1])) {
                $dest_ports[] = ['port' => $expl_range[0], 'last_port' => null];
            }
        }

        $profilesIpfilter['dest_ports'] = $dest_ports;

        //debug($profilesIpfilter);

        $mask_numbers = range(32,0);

        foreach($mask_numbers as $mask_number)
        {
            $mask_range[$mask_number] = $mask_number;
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data_ipfilter = $this->request->data;

            // Set port string
            $ports = $this->PortsParser->set_ports_string($data_ipfilter['dest_ports']);
            $new_ports = $this->PortsParser->set_ports_string($data_ipfilter['new_dest_ports']);
            $data_ipfilter['dest_ports'] = $ports['accepted'].",".$new_ports['accepted'];
            $data_ipfilter['dest_ports'] = rtrim($data_ipfilter['dest_ports'], ",");

            if($this->request->data['dest_ip_type'] == 'net') {
                //unset($data_ipfilter['dest_iprange_first'], $data_ipfilter['dest_iprange_last']);

                // Set others dest_ip_type as null
                $data_ipfilter['dest_iprange_first'] = null;
                $data_ipfilter['dest_iprange_last'] = null;
                $data_ipfilter['dest_hostname'] = null;

                $profilesIpfilter = $this->ProfilesIpfilters->patchEntity($profilesIpfilter, $data_ipfilter,
                        ['validate' => 'net']
                        );

                if ($this->ProfilesIpfilters->save($profilesIpfilter)) {
                    $this->Flash->success(__('Firewall rule has been saved.'));

                    if($profile_id != null) {
                        return $this->redirect(['action' => 'index', $profile_id]);
                    } else {
                        return $this->redirect(['action' => 'index']);
                    }
                } else {
                    $this->Flash->error(__('Firewall rule could not be saved.')." ".__('Please try again.'));
                }

            } 
            elseif($this->request->data['dest_ip_type'] == 'range') {
                //unset($data_ipfilter['dest_ip'], $data_ipfilter['dest_ip_mask']);
                // Set others dest_ip_type as null
                $data_ipfilter['dest_ip'] = null;
                $data_ipfilter['dest_ip_mask'] = null;
                $data_ipfilter['dest_hostname'] = null;

                if(ip2long($data_ipfilter['dest_iprange_first']) < ip2long($data_ipfilter['dest_iprange_last']))
                {
                    $profilesIpfilter = $this->ProfilesIpfilters->patchEntity($profilesIpfilter, $data_ipfilter,
                        ['validate' => 'iprange']
                        );
                    if ($this->ProfilesIpfilters->save($profilesIpfilter)) {
                        $this->Flash->success(__('Firewall rule has been saved.'));

                        if($profile_id != null) {
                            return $this->redirect(['action' => 'index', $profile_id]);
                        } else {
                            return $this->redirect(['action' => 'index']);
                        }
                    } else {
                        $this->Flash->error(__('Firewall rule could not be saved.')." ".__('Please try again.'));
                    }
                } else {
                       $this->Flash->set(__('Last IP must be greater than first IP'), [ 
                           'key' => 'error_iprange',
                           'element' => 'custom_error' ]);
                }
            }

            elseif($this->request->data['dest_ip_type'] == 'hostname') {
                //unset($data_ipfilter['dest_ip'], $data_ipfilter['dest_ip_mask'], $data_ipfilter['dest_iprange_first'], $data_ipfilter['dest_iprange_last']);

                $data_ipfilter['dest_ip'] = null;
                $data_ipfilter['dest_ip_mask'] = null;
                $data_ipfilter['dest_iprange_first'] = null;
                $data_ipfilter['dest_iprange_last'] = null;

                $profilesIpfilter = $this->ProfilesIpfilters->patchEntity($profilesIpfilter, $data_ipfilter,
                    ['validate' => 'fqdn']
                    );
                if ($this->ProfilesIpfilters->save($profilesIpfilter)) {
                    $this->IpfiltersRulesSort->force_sort_rules($profile_id);
                       $this->Flash->success(__('Firewall rule has been saved.'));

                    if($profile_id != null) {
                        return $this->redirect(['action' => 'index', $profile_id]);
                    } else {
                        return $this->redirect(['action' => 'index']);
                    }
                   } else {
                       $this->Flash->error(__('Firewall rule could not be saved.')." ".__('Please try again.'));
                   }
            }
        }
        $profiles = $this->ProfilesIpfilters->Profiles->find('list', ['limit' => 200]);
        $this->set('mask_range', $mask_range);
        $this->set(compact('profilesIpfilter', 'profiles'));
        $this->set('_serialize', ['profilesIpfilter']);
        //$this->viewBuilder()->setLayout('adminlte');
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Delete a firewall rule
     *
     * @param string|null $id: ID of rule to delete 
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $profilesIpfilter = $this->ProfilesIpfilters->get($id);
        if ($this->ProfilesIpfilters->delete($profilesIpfilter)) {
            $this->Flash->success(__('Firewall rule has been deleted.'));
        } else {
            $this->Flash->error(__('Firewall rule could not be deleted.')." ".__('Please try again.'));
        }

        return $this->redirect($this->referer());
    }
}
