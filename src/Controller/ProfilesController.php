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

use Cake\ORM\TableRegistry;
use Cake\Utility\Xml;

/**
 * This class allows to manage profiles settings
 * Profile handles all connection settings for users and devices
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ProfilesController extends AppController
{
    /* on test */
    public function overlay()
    {
        $this->viewBuilder()->setLayout('adminlte');
    }
    /* on test */
    public function loadoverlay()
    {
        sleep(10);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * List profiles and bulk management of profiles
     *
     * @return void
     */
    public function index()
    {
        /* Set or not a default ordering */
        if(null !== $this->request->getQuery('sort')) {
            $profiles = $this->Profiles->find('all');
        } else {
            $profiles = $this->Profiles->find('all')->order(['profilename']);
        }

        // Set value of search Query to null by default
        $this->set('search_query', null);

        if(null !== $this->request->getQuery('query')) {
            if($this->request->getQuery('action') == 'search') {
                $query = $this->request->getQuery('query');
                $profiles = $this->Profiles
                    ->find()
                    ->where(['Profiles.profilename LIKE' => "%$query%"])
                    ->orWhere(['Profiles.default_routing LIKE' => "%$query%"])
                    ->orWhere(['Profiles.default_ipfilter LIKE' => "%$query%"]);
            }
            // Set value of search Query to show search in view result
            $this->set('search_query', $query);
        }

        $this->paginate = ['limit' => 25];
        $this->set('profiles', $this->paginate($profiles));
        $this->set('_serialize', ['profiles']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Add new profile
     *
     * @return void Redirects to edit page on successful add, renders view otherwise.
     */
    public function add()
    {
        $profile = $this->Profiles->newEntity();

        if ($this->request->is('post')) {

            $profile = $this->Profiles->patchEntity($profile, $this->request->data);
            if ($this->Profiles->save($profile)) {

                /* Update profiles DNS configuration */
                $count_cmd_rc = 0;
                exec($this->kxycmd("config bind set_profiles"), $o, $cmd_rc);
                $count_cmd_rc = $count_cmd_rc + $cmd_rc;

                exec($this->kxycmd("config bind set_acl"), $o, $cmd_rc);
                $count_cmd_rc = $count_cmd_rc + $cmd_rc;

                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (isset($this->request->query['install_type'])) {
                    $install_type = $this->request->query['install_type'];
                }

                /* Restart Bind if update config is ok */
                if($count_cmd_rc == 0) {
                    exec($this->kxycmd("service bind reload"), $o, $cmd_rc);

                    $this->Flash->success(__('Profile added successfully.')." ".__('Please now adjust the profile settings.'));

                    if($cmd_rc == 0) { 
                        if ($run_wizard->value == 1) {
                            return $this->redirect(['action' => 'wedit', $profile->id, 'install_type' => $install_type ]);
                        } else {
                            return $this->redirect(['action' => 'edit', $profile->id]);
                        }
                    } else {
                        $this->Flash->warning(__('Unable to reload DNS service.'));

                        if ($run_wizard->value == 1) {
                            return $this->redirect(['action' => 'wedit', $profile->id, 'install_type' => $install_type ]);
                        } else {
                            return $this->redirect(['action' => 'edit', $profile->id]);
                        }
                    }
                } else {
                    $this->Flash->error(__('Unable to write {0} configuration files.', 'DNS ACLs'));
                }
            } else {
                /* Show message on error */
                $this->Flash->error(__('Unable to add the profile.'));
            }
        }
        $this->set('profile', $profile);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Add new profile with wizard
     *
     * @return void Redirects to edit page on successful add, renders view otherwise.
     */
    public function wadd()
    {
        $this->add();
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Edit profile setting : DNS options, connection type, blacklist options, time scheduling
     *
     * @param int|null $id : profile id
     *
     * @return void Redirects to edit() on successful add, renders view otherwise.
     */
    public function edit($id = null)
    {
        $this->loadModel('ProfilesTimes');
        $this->loadModel('ProfilesRouting');
        $this->loadModel('ProfilesBlacklists');
        $this->loadModel('Blacklist');

        $profile = $this->Profiles->get($id, ['contain' => ['ProfilesTimes', 'ProfilesBlacklists']]);

        $enabled_categories = [];
        foreach($profile['profiles_blacklists'] as $enabled_category) {
            $enabled_categories[] = $enabled_category['category'];
        }
        
        $this->loadComponent('BlacklistCache');
        if(($categories_list = $this->BlacklistCache->ReadCache('bl_profile_categories')) === false) {
            $this->BlacklistCache->WriteCache();
            $categories_list = $this->BlacklistCache->ReadCache('bl_profile_categories');
        }

        $categories = null;

        // Check available categories that are enabled and built an array for the view
        
        if($categories_list != false) {
            foreach($categories_list as $category) {
                if(in_array($category['category'], $enabled_categories)) {
                    $categories[] = array('category' => $category['category'], 'enabled' => 1);
                } else {
                    $categories[] = array('category' => $category['category'], 'enabled' => 0);
                }
            }
        } 

        if ($this->request->is(['post', 'put'])) {

            $profile_data = $this->request->data;

            $enabled_categories_string = null;
            if(isset($profile_data['categories']) and is_array($profile_data['categories'])) {
                // CleanUp profile blacklists
                $this->ProfilesBlacklists->deleteAll(['profile_id' => $profile['id']]);
                
                // Update blacklist categories for the profile
                foreach($profile_data['categories'] as $category) {
                    $enabled_categories_string .= $category.",";

                    $ProfilesBlacklist_data['profile_id'] = $profile['id'];
                    $ProfilesBlacklist_data['category'] = $category;

                    $ProfilesBlacklist = $this->ProfilesBlacklists->newEntity();
                    $ProfilesBlacklist = $this->ProfilesBlacklists->patchEntity($ProfilesBlacklist, $ProfilesBlacklist_data);

                    $this->ProfilesBlacklists->save($ProfilesBlacklist);
                }
            } else {
                // Just CleanUp profile blacklists
                $this->ProfilesBlacklists->deleteAll(['profile_id' => $profile['id']]);
            }

            $enabled_categories_string = rtrim($enabled_categories_string, ", ");

            if($enabled_categories_string == null) {
                $profile_data['blacklists'] = null;
            } else {
                $profile_data['blacklists'] = $enabled_categories_string;
            }

            unset($profile_data['categories']);

            // Remove time settings that are not saved in profile table
            unset($profile_data['times']);
            unset($profile_data['newtimes']);

            $this->Profiles->patchEntity($profile, $profile_data);

            if ($this->Profiles->save($profile)) {
                $this->loadComponent('Times');

                // Update modified time settings in ProfilesTimes table
                if(isset($this->request->data['times'])) {
                    $times = $this->request->data['times'];
                    foreach($times as $key => $time) {
                        $time_data = $this->Times->SetTimeData($time);
                        if(isset($time_data['add_time']) and $time_data['add_time'] == 'yes') {
                            $profilesTime = $this->ProfilesTimes->get($key);
                            $profilesTime = $this->ProfilesTimes->patchEntity($profilesTime, $time_data);
                            $this->ProfilesTimes->save($profilesTime);
                        } elseif (isset($time_data['add_time']) and $time_data['add_time'] == 'no') {
                            $warning_message = __('One or more connection schedules were not saved.')." ".__('End time was before start time or no day was selected.');
                        }
                    }
                }

                // Add new time settings in ProfilesTimes table
                if(isset($this->request->data['newtimes'])) {
                    $newtimes = $this->request->data['newtimes'];
                    foreach($newtimes as $time) {

                        $new_time_data = $this->Times->SetTimeData($time);
                        if(isset($new_time_data['add_time']) and $new_time_data['add_time'] == 'yes') {
                            $new_time_data['profile_id'] = $profile['id'];
                            $profilesTime = $this->ProfilesTimes->newEntity();
                            $profilesTime = $this->ProfilesTimes->patchEntity($profilesTime, $new_time_data);
                            $this->ProfilesTimes->save($profilesTime);
                        } elseif (isset($new_time_data['add_time']) and $new_time_data['add_time'] == 'no') {
                            $warning_message = __('One or more connection schedules were not saved.')." ".__('End time was before start time.');
                        }
                    }
                }

                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (isset($this->request->query['install_type'])) {
                    $install_type = $this->request->query['install_type'];
                }

                if(isset($warning_message)) {
                    $this->Flash->warning($warning_message);
                } else {
                    /* Update profiles configuration */
                    exec($this->kxycmd("config bind update_profile_view ".$profile['id']), $o, $cmd_rc);
                    /* Restart Bind if update config is ok */
                    if($cmd_rc == 0) {
                        exec($this->kxycmd("service bind reload"), $o, $cmd_rc);
                        if($cmd_rc == 0) { 
                            if ($run_wizard->value == 1) {
                                $this->Flash->success(__('Profile saved successfully.'));
                                return $this->redirect(['action' => 'wadd', 'install_type' => $install_type]);
                            } else {
                                $this->Flash->success(__('Profile saved successfully.'));
                                return $this->redirect(['action' => 'index']);
                            }
                        } else {
                            $this->Flash->warning(__('Profile saved successfully.')." ".__('But unable to reload DNS service.'));
                            return $this->redirect(['action' => 'index']);
                        }
                    } else {
                        $this->Flash->error(__('Unable to write {0} configuration files.', 'DNS'));
                    }
                }

            } else {
                $this->Flash->error(__('Unable to update profile.'));
            }

        }

        /* Set options for times*/
        $num = 0;
        while($num < 24) {
            $hour_opt[$num] = sprintf("%02d", $num);
            $num++;
        }

        $num = 0;
        while($num < 60) {
            $min_opt[$num] = sprintf("%02d", $num);
            $num = $num + 5;
        }
        $this->set('hour_opt', $hour_opt);
        $this->set('min_opt', $min_opt);
        /* Set options for times*/

        /* Options to define number of time admin user can add in one step */
        $this->set('count_times', 1);
        $this->set('max_times', 11);

        $this->set('profile', $profile);
        $this->set('categories', $categories);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Edit profile setting with wizard: DNS options, connection type, blacklist options, time scheduling
     *
     * @param int|null $id : profile id
     *
     * @return void Redirects to edit() on successful add, renders view otherwise.
     */
    public function wedit($id = null)
    {
        $this->edit($id);
        $this->viewBuilder()->setLayout('wizard');
    }

    /** 
     * Reconnects all actives users and devices using the profile
     * 
     * @param integer $id : profile id
     *
     * @return void Redirects to referer
     */
    public function reconnect($id)
    {
        $this->autoRender = false;
        $this->loadModel('ActivesConnections');
        $UsersController = new UsersController;
        $DevicesController = new DevicesController;

        /* Reconnect actives users */
        $actives_u_conn = $this->ActivesConnections->find('all', ['conditions' => [ 
            'ActivesConnections.profile_id' => $id,
            'ActivesConnections.type' => 'usr'
            ]]);

        $count_user_conn = $actives_u_conn->count();
        $count_user_reconn = 0;
        if(isset($actives_u_conn)) {
            foreach ($actives_u_conn as $active_u_conn) {
                exec($this->kxycmd("users reconnect ".$active_u_conn['name']." ". $active_u_conn['ip']), $output, $rc);
                if($rc == 0) {
                    $count_user_reconn++;
                }
            }
        }

        /* Reconnect actives devices */
        $actives_d_conn = $this->ActivesConnections->find('all', ['conditions' => [ 
            'ActivesConnections.profile_id' => $id,
            'ActivesConnections.type' => 'dev'
            ]]);

        $count_device_conn = $actives_d_conn->count();
        $count_device_reconn = 0;
        if(isset($actives_d_conn)) {
            foreach ($actives_d_conn as $active_d_conn) {
                exec($this->kxycmd("devices reconnect ".$active_d_conn['name']." ". $active_d_conn['ip']), $output, $rc);
                if($rc == 0) {
                    $count_device_reconn++;
                }
            }
        }

        $count_conn = $count_user_conn + $count_device_conn;
        $count_reconn = $count_user_reconn + $count_device_reconn;
        if($count_conn == $count_reconn) {
            $this->Flash->success(__("All users and devices reconnected successfully."));
        } else {
            $this->Flash->error(__("Unable to reconnect all users and devices."));
        }

        return $this->redirect($this->referer());
        
    }

    /** 
     * Generate a form for request to the administrator what to export
     * 
     * @param integer $id : profile to export
     *
     * @return void Redirects with a query string to download_export
     */
    public function export($id)
    {
        $profile = $this->Profiles->get($id);
        if(isset($this->request->query['export_profile'])
                or isset($this->request->query['export_times'])
                or isset($this->request->query['export_websites'])
                or isset($this->request->query['export_firewall'])
                or isset($this->request->query['export_blacklists'])
            ) {
            return $this->redirect(['action' => 'download_export', $profile['id'], '?' => $this->request->query]);
        }
        $this->set('profile', $profile);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Create profile export file 
     * 
     * @param integer $id : profile id
     *
     * @return void Reponses file to download
     */
    public function downloadExport($id)
    {
        $this->autoRender = false;
        // Needed Models
        $this->loadModel('ProfilesIpfilters');
        $this->loadModel('ProfilesRouting');
        $this->loadModel('ProfilesTimes');
        $this->loadModel('ProfilesBlacklists');

        // Get profile settings
        $Profile = $this->Profiles->get($id)->toArray();

        // Set data to export to null by default
        $ExpProfile = null;

        // Add profile setting to export if requested
        if(isset($this->request->query['export_profile']) and $this->request->query['export_profile'] == true) {
            $ExpProfile = $Profile;
            unset($ExpProfile['id'], $ExpProfile['created'], $ExpProfile['modified']);
        }

        // Add profile Domains routing and filtering to export if requested
        if(isset($this->request->query['export_websites']) and $this->request->query['export_websites'] == true) {

            // Getting Domains routing and filters
            $ProfilesRouting = $this->ProfilesRouting->find('all', ['conditions' => ['profile_id' => $id]])->toArray();

            // Recursively convert object to array 
            $ProfilesRouting = json_decode(json_encode($ProfilesRouting), true);

            $item_id = 1;
            $ExpProfilesRouting = null;
            foreach($ProfilesRouting as $ProfileRouting) {
                unset($ProfileRouting['id'], $ProfileRouting['profile_id']);
                $ExpProfilesRouting["item".$item_id] = $ProfileRouting;
                $item_id++;
            }
            $ExpProfile['profile_routing'] = $ExpProfilesRouting;
        }

        // Add profile firewall rules to export if requested
        if(isset($this->request->query['export_firewall']) and $this->request->query['export_firewall'] == true) {

            $ProfilesIpfilters = $this->ProfilesIpfilters->find('all', ['conditions' => ['profile_id' => $id]])->toArray();
            $ProfilesIpfilters = json_decode(json_encode($ProfilesIpfilters), true);

            $item_id = 1;
            $ExpProfilesIpfilters = null;
            foreach($ProfilesIpfilters as $ProfileIpfilter) {
                // Unset ids
                unset($ProfileIpfilter['id'], $ProfileIpfilter['profile_id']);
                $ExpProfilesIpfilters["item".$item_id] = $ProfileIpfilter;
                $item_id++;
            }
            $ExpProfile['profile_ipfilters'] = $ExpProfilesIpfilters;
        }

        // Add profile times to export if requested
        if(isset($this->request->query['export_times']) and $this->request->query['export_times'] == true) {

            $ProfilesTimes = $this->ProfilesTimes->find('all', ['conditions' => ['profile_id' => $id]])->toArray();

            $ProfilesTimes = json_decode(json_encode($ProfilesTimes), true);

            $item_id = 1;
            $ExpProfilesTimes = null;
            foreach($ProfilesTimes as $ProfileTime) {
                unset($ProfileTime['id'], $ProfileTime['profile_id']);
                $ExpProfilesTimes["item".$item_id] = $ProfileTime;
                $item_id++;
            }
            $ExpProfile['profile_times'] = $ExpProfilesTimes;
        }

        // Add profile blacklists to export if requested
        if(isset($this->request->query['export_blacklists']) and $this->request->query['export_blacklists'] == true) {

            $ProfilesBlacklists = $this->ProfilesBlacklists->find('all', ['conditions' => ['profile_id' => $id]])->toArray();

            $ProfilesBlacklists = json_decode(json_encode($ProfilesBlacklists), true);

            $item_id = 1;
            $ExpProfilesBlacklists = null;
            foreach($ProfilesBlacklists as $ProfileBlacklist) {
                unset($ProfileBlacklist['id'], $ProfileBlacklist['profile_id']);
                $ExpProfilesBlacklists["item".$item_id] = $ProfileBlacklist;
                $item_id++;
            }
            $ExpProfile['profile_blacklists'] = $ExpProfilesBlacklists;
        }

        // Create XML data from array
        $xmlArray = ['root' => $ExpProfile];
        $xmlObject = Xml::fromArray($xmlArray, ['format' => 'tags']);
        $xmlString = $xmlObject->asXML();

        // Define XML file name and tar file path
        $xml_file = 'keexybox_profile.xml';
        $tar_achive = WWW_ROOT . "download/".'keexybox_profile_'.$Profile['profilename'].'.tar';
        
        // Change to webroot/download
        chdir(WWW_ROOT . "download/");

        // Insert xml data in file
        file_put_contents($xml_file, $xmlString);
        
        // Remove previous archives
        if(file_exists($tar_achive)) {
            unlink($tar_achive);
        }
        if(file_exists($tar_achive.".gz")) {
            unlink($tar_achive.".gz");
        }
        
        // Loading class to manage Tar file
        $a = new \PharData($tar_achive);

        // Add file to tar
        $a->addFile($xml_file);
        
        // Compress tar file
        $a->compress(\Phar::GZ);
        
        // Remove tar file and keep tar.gz file
        unlink($xml_file);
        unlink($tar_achive);

        // Change response Header
        $this->response->header('Content-Type', 'application/gzip');

        // Force download file
        $this->response->file(
            $tar_achive.'.gz',
            ['download' => true, 'name' => 'keexybox_profile_'.$Profile['profilename'].'.tar.gz']
            );
    }

    /**
     * Allow admin user to upload file to import
     *
     * @return void Redirects to import
     */
    public function uploadImport()
    {
        if ($this->request->is('post')) {
            if(move_uploaded_file($this->request->data['file']['tmp_name'], "upload/".$this->request->data['file']['name']))
            {
                $this->Flash->success(__('File has been uploaded successfully.')." ".__('Please select below data to import.'));
                $redirect_query = $this->request->query;
                $redirect_query['file'] = $this->request->data['file']['name'];
                return $this->redirect(['action' => 'import', '?' => $redirect_query]);
            } else {
                $this->Flash->error(__('Unable to upload file.')." ".__('Please try again.'));
            }
        }

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Display form to ask admin user what to import and process the file that were uploaded to import profile
     *
     * @return void Redirects to edit the created profile, redirects to uploadImport() if failed
     */
    public function import()
    {
        // Load required model
        $this->loadModel('ProfilesIpfilters');
        $this->loadModel('ProfilesRouting');
        $this->loadModel('ProfilesTimes');
        $this->loadModel('ProfilesBlacklists');
        $this->loadModel('Blacklist');

        $import_settings = $this->request->query;

        if(isset($import_settings['file']) and file_exists("upload/".$import_settings['file'])) {
            $profile = $this->Profiles->newEntity();

            $tar_achive = "upload/".$import_settings['file'];

            $a = new \PharData($tar_achive);

            $a->extractTo('upload/', 'keexybox_profile.xml', true);

            $xml_data = file_get_contents('upload/keexybox_profile.xml');
            $import_data = Xml::toArray(Xml::build($xml_data));

            if(!isset($import_data['root']['profile_blacklists'])) {
                $import_data['root']['profile_blacklists'] = '';
            }

            if($import_data['root']['profile_blacklists'] != '') {
                foreach($import_data['root']['profile_blacklists'] as $enabled_category){
                    $enabled_categories[] = $enabled_category['category'];
                }
            } else {
                $enabled_categories[] = null;
            }

            $this->loadComponent('BlacklistCache');
            if(($categories_list = $this->BlacklistCache->ReadCache('bl_profile_categories')) === false) {
                $this->BlacklistCache->WriteCache();
                $categories_list = $this->BlacklistCache->ReadCache('bl_profile_categories');
            }

            $categories = null;

            // Check available categories that are enabled and built an array for the view
            if($categories_list != false) {
                foreach($categories_list as $category) {
                    if(in_array($category['category'], $enabled_categories)) {
                        $categories[] = array('category' => $category['category'], 'enabled' => 1);
                    } else {
                        $categories[] = array('category' => $category['category'], 'enabled' => 0);
                    }
                }
            }     
    

            if($this->request->is('post')) {
                // Import data in existing profile, this allow to import setting in existing profile
                if(isset($this->request->data['profile_id'])) {
                    $profile_id = $this->request->data['profile_id'];
                // Or import data in new profile
                } else {

                    $profile = $this->Profiles->patchEntity($profile, $this->request->data);

                    if ($this->Profiles->save($profile)) {
                        // Update blacklist categories for the profile
                        if(isset($this->request->data['categories'])){ 
                            foreach($this->request->data['categories'] as $category) {
                                // Update blacklist categories for the profile
    
                                $ProfilesBlacklist_data['profile_id'] = $profile['id'];
                                $ProfilesBlacklist_data['category'] = $category;
    
                                $ProfilesBlacklist = $this->ProfilesBlacklists->newEntity();
                                $ProfilesBlacklist = $this->ProfilesBlacklists->patchEntity($ProfilesBlacklist, $ProfilesBlacklist_data);
    
                                $this->ProfilesBlacklists->save($ProfilesBlacklist);
                            }
                        }

                        /* Update profiles configuration */
                        exec($this->kxycmd("config bind update_profile_view ".$profile->id));
                        exec($this->kxycmd("config bind set_profiles"));
                        exec($this->kxycmd("config bind update_acl"));
                        /* Restart Bind */
                        exec($this->kxycmd("service bind reload"));
                        $profile_id = $profile->id;
                    } else {
                        $this->Flash->error(__('Unable to import profile.'));
                    }
                }

                if(isset($profile_id)) {
                    // Import Domain routing if it was checked and was on the query string
                    if(isset($import_settings['import_websites']) and $import_settings['import_websites'] == true) {
                        if($this->request->data['import_routing'] == true) {
                            if(isset($import_data['root']['profile_routing']) and $import_data['root']['profile_routing'] != '') {
                                foreach($import_data['root']['profile_routing'] as $profile_routing) {
                                    $routing_data = $profile_routing;
                                    $routing_data['profile_id'] = $profile_id;
        
                                    $routing = $this->ProfilesRouting->newEntity();
                                    $routing = $this->ProfilesRouting->patchEntity($routing, $routing_data);
        
                                    $this->ProfilesRouting->save($routing);
                                }
                            }
                        }
                    }

                    // Import Firewall rules if it was checked
                    if(isset($import_settings['import_firewall']) and $import_settings['import_firewall'] == true) {
                        if($this->request->data['import_ipfilters'] == true) {
                            if(isset($import_data['root']['profile_ipfilters']) and $import_data['root']['profile_ipfilters'] != '') {
                                foreach($import_data['root']['profile_ipfilters'] as $profile_ipfilter) {
                                    $ipfilter_data = $profile_ipfilter;
                                    $ipfilter_data['profile_id'] = $profile_id;
                                    $ipfilter = $this->ProfilesIpfilters->newEntity();
                                    // Validate if data match
                                    if($ipfilter_data['dest_ip_type'] == 'net') {
                                        $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data);
                                    }
                                    elseif($ipfilter_data['dest_ip_type'] == 'range') {
                                        $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'iprange']);
                                    }
                                    elseif($ipfilter_data['dest_ip_type'] == 'hostname') {
                                        $ipfilter = $this->ProfilesIpfilters->patchEntity($ipfilter, $ipfilter_data, ['validate' => 'fqdn']);
                                    }
                                    $this->ProfilesIpfilters->save($ipfilter);
                                }
                            }
                        }
                    }
                    // Import Time access if it was checked
                    if(isset($import_settings['import_times']) and $import_settings['import_times'] == true) {
                        if($this->request->data['import_times'] == true) {
                            if(isset($import_data['root']['profile_times']) and $import_data['root']['profile_times'] != '') {
                                foreach($import_data['root']['profile_times'] as $profile_time) {
                                    $time_data = $profile_time;
                                    $time_data['profile_id'] = $profile_id;
                                    $times = $this->ProfilesTimes->newEntity();
                                    $times = $this->ProfilesTimes->patchEntity($times, $time_data);
                                    $this->ProfilesTimes->save($times);
                                }
                            }
                        }
                    }
                    $this->Flash->success(__('Profile imported successfully.'));
                    return $this->redirect(['action' => 'edit', $profile_id]);

                } else {
                    // Show message on error
                    $this->Flash->error(__('No profile defined to import data.'));
                }
            }

            // Set values for view
            $profiles = $this->ProfilesIpfilters->Profiles->find('list', ['limit' => 200]);
            $this->set('categories', $categories);
            $this->set('profiles', $profiles);
            $this->set('profile', $profile);
            $this->set('import_data', $import_data['root']);
            $this->set('import_settings', $import_settings);

        } else {
            $this->Flash->error(__('No data to import.'));
            return $this->redirect(['action' => 'upload_import', '?' => $import_settings]);
        }

        $this->viewBuilder()->setLayout('adminlte');
    }


    /**
     * Delete a profile 
     * It define the default profile of all users and devices that were assigned to the deleted profile
     *
     * @param integer $id : profile id
     *
     * @return void Redirects to index()
     */
    public function delete($id)
    {
        $this->loadModel('Users');
        $this->loadModel('Devices');

        // Allow only HTTP methode POST DELETE
        $this->request->allowMethod(['post', 'delete']);

        $profile = $this->Profiles->get($id);
        if($id != 1) {
            //Load profile to delete
            //Delete profile
            if ($this->Profiles->delete($profile)) {

                // RESET USERS SETTING USING THIS PROFILE
                $dependent_users = $this->Users->find('all', ['conditions' => [ 'Users.profile_id' => $id]]);
                foreach($dependent_users as $user) {
                    //Set default profile for all dependent users of deleted profiles
                    $update_user = $this->Users->patchEntity($user, ['profile_id' => '1']);
                    $this->Users->save($update_user);
                    //Disconnect users dependent of profile
                    $UsersController = new UsersController;
                    $UsersController->disconnectuser($user['username']);
                }
    
                // RESET DEVICES SETTING USING THIS PROFILE
                $dependent_devices = $this->Devices->find('all', ['conditions' => [ 'Devices.profile_id' => $id]]);
                foreach($dependent_devices as $device) {
                    //Set default profile for all dependent devices of deleted profiles
                    $update_device = $this->Devices->patchEntity($device, ['profile_id' => '1']);
                    $this->Devices->save($update_device);
                    //Disconnect devices dependent of profile
                    $DevicesController = new DevicesController;
                    if($DevicesController->disconnect($device['devicename'])) {
                        //If device was connected, reconnect it with default profile
                        $DevicesController->connect($device['devicename']);
                    }
                }

                // REMOVE BIND CONFIGURATION 
                exec($this->kxycmd("config bind remove_profile ".$profile['id']), $output, $rc);

                $this->Flash->success(__("Profile {0} has been deleted.", h($profile['profilename'])));
                return $this->redirect(['action' => 'index']);
            }
        } else {
            $this->Flash->error(__('Deleting profile {0} is forbidden.', h($profile['profilename'])));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Reload all profile connection rules for given profile ID
     *
     * @param integer $id : profile id
     *
     * @return void Redirects to referer
     */
    public function reload($id)
    {
        $this->autoRender = false;
        $profile = $this->Profiles->get($id);
        if(isset($profile['id'])) {
            exec($this->kxycmd("profiles ResetAccess ".$profile['id']), $output, $rc);
        } else {
            $this->Flash->error(__("Profile ID is not specified."));
        }
    
        if($rc == 0) {
            $this->Flash->success(__("Profile {0} has been reloaded successfully.", $profile['profilename']));
        } else {
            $this->Flash->error(__("Unable to reload profile {0}.", $profile['profilename']));
        }
    
        return $this->redirect($this->referer());
    }

}
