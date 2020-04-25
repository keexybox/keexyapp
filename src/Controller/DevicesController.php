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

//require_once(APP .DS. 'Controller' . DS . 'Component' . DS . 'IP4Calc.php');
use App\Controller\AppController;
use Cake\Core\Configure;

/**
 * This class allows to manage devices
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class DevicesController extends AppController
{
    /**
     * List devices and bulk management of devices
     *
     * @return void
     */
    public function index()
    {
        if(null !== $this->request->getQuery('sort')) {
            $devices = $this->Devices->find('all')->contain(['Profiles']);
        } else    {
            $devices = $this->Devices->find('all')->contain(['Profiles'])->order(['devicename']);
        }

        // Set value of search Query to null by default
        $this->set('search_query', null);

        if(null !== $this->request->getQuery('query')) {
            if($this->request->getQuery('action') == 'search') {
                $query = $this->request->getQuery('query');
                $devices = $this->Devices->find()
                    ->where(['Devices.devicename LIKE' => "%$query%"])
                    ->orWhere(['Devices.mac LIKE' => "%$query%"])
                    ->orWhere(['Profiles.profilename LIKE' => "%$query%"])
                    ->contain(['Profiles']);

                $this->paginate = [
                    'contain' => ['Profiles'],
                    'limit' => $this->request->getQuery('results'),
                ];

                // Set value of search Query to show search in view result
                $this->set('search_query', $query);
            }
        } else {
            $this->paginate = [
                'contain' => ['Profiles'],
                'limit' => 25
            ];
        }

        if ($this->request->is('post')) {
            if(isset($this->request->data['check'])) {
                if($this->request->data['action'] == 'search') {
                    $query = $this->request->data['query'];
                    $devices = $this->Devices->find()
                        ->where(['Devices.devicename LIKE' => "%$query%"])
                        ->orWhere(['Devices.mac LIKE' => "%$query%"])
                        ->orWhere(['Profiles.profilename LIKE' => "%$query%"])
                        ->contain(['Profiles']);
                }
    
                if($this->request->data['action'] == 'connect') {
                    if(isset($this->request->data['check'])) {
                        foreach($this->request->data['check'] as $id) {
                            $device = $this->Devices->get($id);
                                $this->silentconnect($device->devicename);
                        }
                    }
                }
    
                if($this->request->data['action'] == 'disable') {
                    if(isset($this->request->data['check'])) {
                    //debug($this->request->data['check']);
                        foreach($this->request->data['check'] as $id) {
                            $device = $this->Devices->get($id);
                            $data = ['enabled' => false];
                            $this->Devices->patchEntity($device, $data);
                            $this->Devices->save($device);
                        }
                    }
                }
    
                if($this->request->data['action'] == 'enable') {
                    if(isset($this->request->data['check'])) {
                        //debug($this->request->data['check']);
                        foreach($this->request->data['check'] as $id) {
                            $device = $this->Devices->get($id);
                            $data = ['enabled' => 1];
                                $this->Devices->patchEntity($device, $data);
                                $this->Devices->save($device);
                            }
                    }
                }
    
                if($this->request->data['action'] == 'setprofile') {
                    if($this->request->data['profile_id'] != '') {
                        if(isset($this->request->data['check'])) {
                                foreach($this->request->data['check'] as $id) {
                                if(isset($this->request->data['profile_id'])) {
                                    $device = $this->Devices->get($id);
                                    $data = ['profile_id' => $this->request->data['profile_id']];
                                    $this->Devices->patchEntity($device, $data);
                                    $this->Devices->save($device);
                                }
                            }
                        }
                    } else {
                        $this->Flash->warning(__('Please select a profile.'));
                    }
                }
    
                if($this->request->data['action'] == 'delete') {
                    if(isset($this->request->data['check'])) {
                        //debug($this->request->data['check']);
                            foreach($this->request->data['check'] as $id) {
                                $device = $this->Devices->get($id);
                                if($id != 1) {
                                //Delete device
                                if ($this->Devices->delete($device)) {
                                    $this->disconnect($device['devicename']);
                                }
                            }
                        }
                    }
                }
            } else {
                $this->Flash->warning(__('Nothing was selected.'));
            }
        }

        $this->set('devices', $this->paginate($devices));
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');
        $this->set('profiles',$profiles);
        $this->set('_serialize', ['devices']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Do an ARP Scan on neighborhood network and 
     * generate a list of devices that can be added.
     *
     * @return void
     */
    public function scan()
    {
        $this->loadModel('Profiles');

        // Get default language to sugest as default language
        $this->loadModel('Config');
        $locale = $this->Config->get('locale')->value;

        // ARP SCAN
        if(!$this->request->is('post')) {
            exec($this->kxycmd("arp arp_scan"), $scan_res);
            $devices = null;
            if(isset($scan_res[0])) {
                $devices = unserialize($scan_res[0]);
            }

            $devices_checked = null;
            foreach($devices as $device) {
                $device_res = $this->Devices->findByMac($device['mac'])->first();
                if(isset($device_res['mac'])) {
                    $device['declared'] = true;
                } else {
                    $device['declared'] = false;
                }
                $devices_checked[] = $device;
            }
            $devices = $devices_checked;

            $profiles = $this->Profiles->find('list');
            $this->set('profiles',$profiles);
            $this->set('devices', $devices);
            $this->viewBuilder()->setLayout('adminlte');
        }

        if ($this->request->is('post')) {
            if (isset($this->request->data['check'])) {
                foreach($this->request->data['check'] as $host) {
                    $params = explode(";", $host);
                    // Define devicename
                    if($params[1] == '?' or $params[1] == '') {
                        $devicename = "DEV_".str_replace(":", "", strtoupper($params[0]));
                    } else {
                        $devicename = preg_replace("/[^a-zA-Z0-9]+/", "", $params[1])."_".substr(str_replace(":", "", strtoupper($params[0])), 6);
                    }
    
                    //Storing data
                    $data = [
                        'mac' => strtoupper($params[0]),
                        'devicename' => $devicename,
                        'dhcp_reservation_ip' => '',
                        'lang' => $locale,
                        'profile_id' => $this->request->data['profile_id'],
                        'enabled' => 1,
                    ];
    
                    //Save data
                    $device = $this->Devices->newEntity();
                    $device = $this->Devices->patchEntity($device, $data);
                    $this->Devices->save($device);
                }
    
                // Update DHCP config and reload
                exec($this->kxycmd("config dhcp reservations"));
                exec($this->kxycmd("cake service dhcp reload"));
    
                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (isset($this->request->query['install_type'])) {
                    $install_type = $this->request->query['install_type'];
                }
    
                if ($run_wizard->value == 1) {
                    return $this->redirect(['controller' => 'devices', 'action' => 'wscan', 'install_type' => $install_type]);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            } else {
                // Only use for wizard config
                $this->Flash->warning(__('Nothing was selected.')." ".__('No devices were added.'));
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (isset($this->request->query['install_type'])) {
                    $install_type = $this->request->query['install_type'];
                }
    
                if ($run_wizard->value == 1) {
                    return $this->redirect(['controller' => 'devices', 'action' => 'wscan', 'install_type' => $install_type]);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            }
        }

    }

    /**
     * Do an ARP Scan on neighborhood network and 
     * generate a list of devices that can be added.
     *
     * @return void
     */
    public function wscan()
    {
        $this->scan();
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Add device 
     *
     * @param mac        : device hardware address detected by scan
     * @param devicename : proposed device name
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($mac = null, $devicename = null)
    {
        // Get available profiles
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');

        // Set list of availables languages
        $this->loadComponent('Lang');
        $this->set('langs', $this->Lang->ListLanguages());

        // Get default language to sugest if as default language
        $this->loadModel('Config');

        $lang = $this->Config->get('locale')->value;
        $device = $this->Devices->newEntity();

        if(isset($mac) and isset($devicename)) {
            if($devicename == '?' or $devicename == '') {
                $devicename = "DEV_".str_replace(":", "", strtoupper($mac));
                $devicename = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $devicename); 
            } else {
                $devicename = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $devicename); 
            }
        }

        if ($this->request->is('post')) {

            $this->loadComponent('Mac');

            $this->request->data['mac'] = $this->Mac->rewrite($this->request->data['mac']);


            $device = $this->Devices->patchEntity($device, $this->request->data);

            // If validations are ok, especially dhcp_reservation_ip, we check the possible IP addresses conflicts
            if($device->errors() == null) {
                // Validation if dhcp_reservation_ip is in a right subnet et not used by Keexybox
                if($this->request->data['dhcp_reservation_ip'] != '') {
                    $this->loadComponent('Ipv4');
                    $d_i = $this->Ipv4->getNetwork($this->request->data['dhcp_reservation_ip'], $this->Ipv4->getInputInfo()['netmask']);
                    $d_o = $this->Ipv4->getNetwork($this->request->data['dhcp_reservation_ip'], $this->Ipv4->getOutputInfo()['netmask']);
                    $h_i = $this->Ipv4->getNetwork($this->Ipv4->getInputInfo()['ip'], $this->Ipv4->getInputInfo()['netmask']);
                    $h_o = $this->Ipv4->getNetwork($this->Ipv4->getOutputInfo()['ip'], $this->Ipv4->getOutputInfo()['netmask']);
        
                    if ($d_i != $h_i and $d_o != $h_o) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address does not match with any Keexybox subnets.');
    
                    } elseif ($this->request->data['dhcp_reservation_ip'] == $this->Ipv4->getInputInfo()['ip']) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address is being used by input interface.');
    
                    } elseif ($this->request->data['dhcp_reservation_ip'] == $this->Ipv4->getOutputInfo()['ip']) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address is being used by output interface.');
    
                    } elseif ($this->request->data['dhcp_reservation_ip'] == $this->Ipv4->getOutputInfo()['gateway']) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address is being used by the gateway.');
                    }
                }

                if(!isset($subnet_error_msg)) {
                    if ($this->Devices->save($device)) {
                        $this->Flash->success(__('The device has been saved.'));
                        // Update and reload DHCP config
                        exec($this->kxycmd("config dhcp reservations"));
                        exec($this->kxycmd("cake service dhcp reload"));
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->Flash->error(__('The device could not be saved.')." ".__('Please try again.'));
                    }
                } else {
                    $this->Flash->error($subnet_error_msg);

                }
            }
        }
        $profiles = $this->Devices->Profiles->find('list', ['limit' => 200]);
        $this->set('mac', $mac);
        $this->set('lang', $lang);
        $this->set('devicename', $devicename);
        $this->set(compact('device', 'profiles'));
        $this->set('_serialize', ['device']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Edit device settings
     *
     * @param string|null $id Device id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // Set list of availables languages
        $this->set('langs', $this->Lang->ListLanguages());

        $device = $this->Devices->get($id, [
            'contain' => []
        ]);


        $this->loadComponent('Ipv4');
        // Retrieve subnet input interface informations
        $host_input_info = $this->Ipv4->getInputInfo();
        $h_i = $host_input_info['network'];
        $subnet_i = $host_input_info['network']."/".$host_input_info['mask_dec'];


        // Retrieve subnet input interface informations
        $host_output_info = $this->Ipv4->getOutputInfo();
        $h_o = $host_output_info['network'];
        $subnet_o = $host_output_info['network']."/".$host_output_info['mask_dec'];

        // Set message info to display about DHCP
        $this->loadModel('Config');
        $dhcp_external_value = $this->Config->get('dhcp_external')->value;
        if($dhcp_external_value == 0) {
            $dhcp_info = __('By default IP addresses will be assigned in the subnet {0} if DHCP is enabled.', $subnet_i)." ".__('Here you can reserve an IP address for this device.')." ".__('If you want this device to use Keexybox as DNS only, define an IP address in the {0} subnet.', $subnet_o);
        } else {
            $dhcp_info = __('By default IP addresses will be assigned in the subnet {0} if DHCP is enabled.', $subnet_o)." ".__('Here you can reserve an IP address for this device.');
        }
        $this->set('dhcp_info', $dhcp_info);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $old_profile_id = $device['profile_id'];

            $this->loadComponent('Mac');
            $this->request->data['mac'] = $this->Mac->rewrite($this->request->data['mac']);

            // Patch data for validation
            $device = $this->Devices->patchEntity($device, $this->request->data);

            // If validations are ok, especially dhcp_reservation_ip, we check the possible IP addresses conflicts
            if($device->errors() == null) {
                // Validation if dhcp_reservation_ip is in a right subnet et not used by Keexybox
                if($this->request->data['dhcp_reservation_ip'] != '') {
                    $d_i = $this->Ipv4->getNetwork($this->request->data['dhcp_reservation_ip'], $this->Ipv4->getInputInfo()['netmask']);
                    $d_o = $this->Ipv4->getNetwork($this->request->data['dhcp_reservation_ip'], $this->Ipv4->getOutputInfo()['netmask']);
        
                    if ($d_i != $h_i and $d_o != $h_o) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address does not match with any Keexybox subnets.');
    
                    } elseif ($this->request->data['dhcp_reservation_ip'] == $this->Ipv4->getInputInfo()['ip']) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address is being used by input interface.');
    
                    } elseif ($this->request->data['dhcp_reservation_ip'] == $this->Ipv4->getOutputInfo()['ip']) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address is being used by output interface.');
    
                    } elseif ($this->request->data['dhcp_reservation_ip'] == $this->Ipv4->getOutputInfo()['gateway']) {
                        $subnet_error_msg = __('The device could not be saved.')." ".__('DHCP IP address is being used by the gateway.');
                    }
                }

                if(!isset($subnet_error_msg)) {
                    if ($this->Devices->save($device)) {
                        $this->Flash->success(__('The device has been saved.'));
                        // Update and reload DHCP config
                        exec($this->kxycmd("config dhcp reservations"));
                        exec($this->kxycmd("cake service dhcp reload"));
                        if($device['profile_id'] != $old_profile_id) {
                            $this->Flash->set(__('Do you want to reconnect {0} with new profile?', $device['devicename']), [
                                'key' => 'reconnect',
                                'element' => 'reconnect_link', 
                                'params' => [ 'reconnectlink' => '/devices/reconnect/'.$device['devicename']]
                            ]);
                        }
        
                        return $this->redirect(['action' => 'index']);
                    } else {
                        $this->Flash->error(__('The device could not be saved.')." ".__('Please try again.'));
                    }
                } else {
                    $this->Flash->error($subnet_error_msg);
                }
            }
        }
        $profiles = $this->Devices->Profiles->find('list', ['limit' => 200]);
        $this->set(compact('device', 'profiles'));
        $this->set('_serialize', ['device']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Delete device
     *
     * @param string|null $id Device id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $device = $this->Devices->get($id);
        if ($this->Devices->delete($device)) {
            if($this->disconnect($device['devicename'])) {
                $this->Flash->success(__('The device has been deleted.'));
            }
        } else {
            $this->Flash->error(__('The device could not be deleted.')." ".__('Please try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Export devices to downloadable CSV file
     *
     * @param null
     * @return CSV File
     */
    public function export() {
            $this->autoRender = false;
            $devices = $this->Devices->find('all')->contain(['Profiles']);

            // Create output file
            $fp = fopen('php://output', 'w');
            $csv_file = "keexybox_devices.csv";

            // Set Headers
            $this->response->header('Content-Type', 'application/csv');
            $this->response->header('Content-Disposition', "attachment; filename=$csv_file");

            // Delimiter
            $d=";";
            // Enclosure
            $e='"';

            // insert header to CSV file
            fputs($fp, $e.'devicename'.$e.$d.$e.'mac'.$e.$d.$e.'dhcp_reservation_ip'.$e.$d.$e.'profilename'.$e.$d.$e.'lang'.$e.$d.$e.'enabled'.$e."\n");

            // CSV DATA
            foreach ($devices as $device) {
                fputs($fp, $e.$device->devicename.$e.$d.$e.$device->mac.$e.$d.$e.$device->dhcp_reservation_ip.$e.$d.$e.$device->profile->profilename.$e.$d.$e.$device->lang.$e.$d.$device->enabled."\n");
            }
    }

    /**
     * Import devices from CSV file
     *
     * @param null
     * @return void Redirects to index
     */
    public function import()
    {
        if ($this->request->is('post')) {

            $this->loadModel('Profiles');
            $this->loadComponent('Mac');

            // Save file on webroot/upload/
            if(move_uploaded_file($this->request->data['file']['tmp_name'], "upload/".$this->request->data['file']['name']))
            {
                $csv_file = WWW_ROOT."upload/".$this->request->data['file']['name'];

                // delimiter
                $d=";";
                $e='"';

                // Remove Windows ending lines
                $file = file_get_contents($csv_file);
                $file = str_replace("\r", "", $file);
                file_put_contents($csv_file, $file);

                // open file
                $fp = fopen($csv_file, "r");

                $csv_data = [];
                while ($data = fgetcsv($fp, 0, $d, $e)) {
                    $csv_data[] = $data;
                }

                // Check if CSV contains right headers
                $csv_headers = $csv_data[0];

                // Value to store import errors from CSV
                $import_errors = [];

                // import status, use to Flash message
                //  0 = ok, 1 = warning, else = critical
                $import_status = 0;

                foreach ($csv_data as $key=>$csv_line) {
                    // if csv line contains 6 fields
                    if (count($csv_line) == 6) {
                        if ($key == 0) {
                            $check_res = 0;
                            if ($csv_line[0] != 'devicename') { $check_res++; }
                            if ($csv_line[1] != 'mac') { $check_res++; }
                            if ($csv_line[2] != 'dhcp_reservation_ip') { $check_res++; }
                            if ($csv_line[3] != 'profilename') { $check_res++; }
                            if ($csv_line[4] != 'lang') { $check_res++; }
                            if ($csv_line[5] != 'enabled') { $check_res++; }

                            // If headers are not ok, stop import
                            if( $check_res != 0 ) {
                                $import_status = 2;
                                break;
                            }
                        } else {
                            // Set device name
                            $device_data['devicename'] = trim($csv_line[0], "$e");
    
                            // Set and reformating Mac Address
                            $mac = trim($csv_line[1], "$e");
                            $device_data['mac'] = $this->Mac->rewrite($mac);

                            // Set DHCP IP reservation
                            $device_data['dhcp_reservation_ip'] = trim($csv_line[2], "$e");

                            // Set profile ID
                            $profilename = trim($csv_line[3], "$e");
                            $profile = $this->Profiles->findByProfilename($profilename)->first();
                            // If profile name does not exist set devices to default profile
                            if ($profile['id'] == null) {
                                $device_data['profile_id'] = 1;
                            } else {
                                $device_data['profile_id'] = $profile['id'];
                            }

                            // Set DHCP IP reservation
                            $device_data['lang'] = trim($csv_line[4], "$e");
    
                            // Set if device will be enabled
                            $device_data['enabled'] = trim($csv_line[5], "$e");
        
                            //Check if devicename exist
                            $device = $this->Devices->findByDevicename($device_data['devicename'])->first();
                            // if not exists, add a new one
                            if ($device == null) {
                                $device = $this->Devices->newEntity();
                                $device = $this->Devices->patchEntity($device, $device_data);
                                if ( ! $this->Devices->save($device)) {
                                    $import_errors[] = __('Import error line number {0}.', $key + 1);
                                }
                            // Else update existing one
                            } else {
                                //Update device
                                $device = $this->Devices->patchEntity($device, $device_data);
                                if ( ! $this->Devices->save($device)) {
                                    $import_errors[] = __('Import error line number {0}.', $key + 1);
                                }
                            }
                        }
                    } else {
                        $import_errors[] = __('Import error line number {0}.', $key + 1);
                    }
                }

                // If import error, set import status to warning
                if (count($import_errors) != 0) {
                    $import_status = 1;
                }

                // Flash final message
                if ($import_status == 0) {
                    $this->Flash->success(__('Devices have been imported successfully.'));
                    return $this->redirect(['action' => 'index']);
                } elseif ($import_status == 1){
                    $this->Flash->warning(__('Devices have been imported with errors.'));
                    $errors = null;
                    foreach($import_errors as $import_error) {
                        $errors = $import_error."\n";
                        $this->Flash->set($errors, [
                            'key' => 'import_log',
                            'element' => 'log_message', 
                        ]);
                    }
                } else {
                    $this->Flash->error(__('Devices not imported.')." ".__('Bad CSV headers.'));
                }

                // delete CSV file
                unlink($csv_file);

                // Update DHCP config
                exec($this->kxycmd("config dhcp reservations"));
                exec($this->kxycmd("cake service dhcp reload"));

                return $this->redirect($this->referer());
            } else {
                $this->Flash->error(__('Unable to upload file.')." ".__('Please try again.'));
            }
        }

        $this->viewBuilder()->setLayout('adminlte');

    }

    /**
     * Connect device to network.
     * It can be resquested from Admin console or daemon 
     *
     * @param string $devicename
     * @param string|null $ip
     * 
     * @return void Redirect to referer
     */
    public function connect($devicename, $ip = null)
    {
        $this->autoRender = false;
    
        // if single ip is set, connect device
        if(isset($ip)) {
            exec($this->kxycmd("devices connect $devicename $ip"), $output, $rc);
        } else {
            exec($this->kxycmd("devices connect $devicename"), $output, $rc);
        }
    
        if($rc == 0) {
            $this->Flash->success(__("Device {0} connected successfully.", h($devicename)));
        } else {
            $this->Flash->error(__("Unable to connect {0} device. Please check if the device is on the network.", h($devicename)));
        }
        return $this->redirect($this->referer());
    
    }

    /**
     * Connect device to network without message 
     * This method is called when admin request to connect multiples devices from devices list (index page)
     *
     * @param string $devicename
     * @param string|null $ip
     * 
     * @return void Redirect to referer
     */
    public function silentconnect($devicename, $ip = null)
    {
        $this->autoRender = false;
    
        // if single ip is set, connect device
        if(isset($ip)) {
            exec($this->kxycmd("devices connect $devicename $ip"), $output, $rc);
        } else {
            exec($this->kxycmd("devices connect $devicename"), $output, $rc);
        }
    
        return $this->redirect($this->referer());
    }

    /**
     * Disconnect a connected device.
     * It is only requested by a admin user.
     *
     * @param string $devicename
     * @param ip|null $ip : ip used by the device 
     *
     * @return void Redirect to referer
     */
    public function disconnect($devicename, $ip = null)
    {
        $this->autoRender = false;
        if(isset($ip)) {
            exec($this->kxycmd("devices disconnect $devicename $ip"), $output, $rc);
        } else {
            exec($this->kxycmd("devices disconnect $devicename"), $output, $rc);
        }
    
        if($rc == 0) {
            $this->Flash->success(__("Device {0} has been disconnected.", h($devicename)));
        } else {
            $this->Flash->error(__("Unable to disconnect Device {0}.", h($devicename)));
        }
    
        return $this->redirect($this->referer());
    }

    /**
     * Keep the device as active but pause the connection
     *
     * @param string $devicename
     * @param ip|null $ip : ip used by the device 
     * 
     * @return void Redirects to referer
     */
    public function pause($devicename, $ip = null)
    {
        $this->autoRender = false;

        if(isset($ip)) {
            // this disconnect user connected from ip
            exec($this->kxycmd("devices pause $devicename $ip"), $output, $rc);
        } else {
            // this disconnect user everywhere is connected
            exec($this->kxycmd("devices pause $devicename"), $output, $rc);
        }

        if($rc == 0) {
            $this->Flash->success(__("Device {0} connection has been paused.", h($devicename)));
        } else {
            $this->Flash->error(__("Unable to pause connection of device {0}.", h($devicename)));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Resume device connection that were paused
     *
     * @param string $devicename
     * @param ip|null $ip : ip used by the device 
     * 
     * @return void Redirects to referer
     */
    public function run($devicename, $ip = null)
    {
        $this->autoRender = false;

        if(isset($ip)) {
            // this disconnect user connected from ip
            exec($this->kxycmd("devices run $devicename $ip"), $output, $rc);
        } else {
            // this disconnect user everywhere is connected
            exec($this->kxycmd("devices run $devicename"), $output, $rc);
        }

        if($rc == 0) {
            $this->Flash->success(__("Device {0} connection resumed.", h($devicename)));
        } else {
            $this->Flash->error(__("Unable to resume connection of the device {0}.", h($devicename)));
        }
        return $this->redirect($this->referer());
    }

    /**
     * Reconnect a device to network 
     *
     * For example, this is use when profile changed for a device 
     * and admin wants to reconnect it with new profile
     *
     * @param string $devicename 
     * 
     * @return void Redirects to referer
     */
    public function reconnect($devicename)
    {
        $this->autoRender = false;
        if(isset($ip)) {
            exec($this->kxycmd("devices reconnect $devicename $ip"), $output, $rc);
        } else {
            exec($this->kxycmd("devices reconnect $devicename"), $output, $rc);
        }
    
        if($rc == 0) {
            $this->Flash->success(__("The device {0} has been reconnected successfully.", h($devicename)));
        } else {
            $this->Flash->error(__("Unable to reconnect the device {0}.", h($devicename)));
        }
    
        return $this->redirect($this->referer());
    }

}
