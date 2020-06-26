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

/* load list of Timezone */
//require_once(ROOT .DS. 'vendor' . DS . 'keexybox' . DS . 'tz.php');

// Load external class for IPv4 calc
//require_once(APP .DS. 'Controller' . DS . 'Component' . DS . 'IP4Calc.php');

use App\Controller\AppController;
use Cake\Error\Debugger;
use Cake\I18n\Time;
//use keexybox\tz;
use Cake\Datasource\ConnectionManager;
//use keexybox\IP4Calc;

use Cake\Cache\Cache;

/**
 * This class allows to setup Keexybox
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 *
 * @property \App\Model\Table\ConfigTable $Config
 */
class ConfigController extends AppController
{
    public function index()
    {
        $this->viewBuilder()->setLayout('adminlte');
        //$this->autoRender = false;
    }

    /**
     * List all available Keexybox settings
     *
     * @return void 
     */
    public function advanced()
    {
        // Set value of search Query to null by default
        $this->set('search_query', null);

        if(isset($this->request->query['query'])) {
            if($this->request->query['action'] == 'search') {
                $query = $this->request->query['query'];
                $config = $this->Config->find()
                        ->where(['Config.param LIKE' => "%$query%"]);

                $this->paginate = [
                    'limit' => $this->request->query['results'],
                ];

                // Set value of search Query to show search in view result
                $this->set('search_query', $query);
            }
        } else {
            $config = $this->Config;
            $this->paginate = [
                'limit' => 25,
            ];
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            exec($this->kxycmd("config ".$this->request->data['config']." all"), $output, $rc);
            if($rc == 0) {
                $this->Flash->success(__('{0} configuration files generated successfully.', $this->request->data['config']));
            } else {
                $this->Flash->error(__('Unable to write {0} configuration files.', $this->request->data['config']));
            }
        }

        $this->set('config', $this->paginate($config));
        $this->set('_serialize', ['config']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Edit a single setting request from advanced page
     *
     * @param string|null $id Config id.
     *
     * @return void Redirects on successful edit, renders view otherwise.
     *
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $config = $this->Config->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $config = $this->Config->patchEntity($config, $this->request->data);
            if ($this->Config->save($config)) {
                $this->Flash->success(__('The value has been changed successfully.'));
                return $this->redirect(['action' => 'advanced']);
            } else {
                $this->Flash->error(__('Unable to change the value.')." ".__('Please try again.'));
            }
        }
        $this->set(compact('config'));
        $this->set('_serialize', ['config']);
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Edit Network settings
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function network()
    {
        // Getting host name
        $host_name = $this->Config->get('host_name');
        
        // Check if Wi-Fi Access point is enabled
        $hostapd_enabled = $this->Config->get('hostapd_enabled')->value;
        $hostapd_interface = $this->Config->get('hostapd_interface')->value;
        $hostapd_bridge_ports = $this->Config->get('hostapd_bridge_ports')->value;
        $hostapd_bridge = $this->Config->get('hostapd_bridge')->value;

        // Getting network input interface path
        $host_interface_input = $this->Config->get('host_interface_input');
        // Needed for autoselect current set device in view
        $host_interface_input_value = str_replace(':0', '', $host_interface_input->value);

        // Getting network input interface path
        $host_interface_output = $this->Config->get('host_interface_output');
        // Needed for autoselect current set device in view
        $host_interface_output_value = $host_interface_output->value;

        // Getting network interface path
        $nic_path = $this->Config->get('nic_path');

        // List directory that contain network interface
        $nic_files = scandir($nic_path->value);

        // Cleaning list and remove loopback interface
        $wifi_class = null;
        $nic_devices = null;
        foreach($nic_files as $nic_file) {
            if( $nic_file != "." AND $nic_file != ".." AND $nic_file != "lo" ) {
                if ($hostapd_enabled == 0) {
                    $nic_devices[$nic_file] = $nic_file;
                    if(is_dir($nic_path->value."/".$nic_file."/wireless")) {
                        $wifi_class .= $nic_file." ";
                    }
                } else {
                    if ($nic_file != $hostapd_bridge_ports AND $nic_file != $hostapd_interface) { 
                        $nic_devices[$nic_file] = $nic_file;
                        if(is_dir($nic_path->value."/".$nic_file."/wireless")) {
                            $wifi_class .= $nic_file." ";
                        }
                    }
                }
            }
        }

        // TEST
        //$wifi_class .= 'wlan1';
        //$nic_devices['wlan1'] = 'wlan1';
        //$wifi_dev['wlan1'] = 'wlan1';
         // END TEST

        $wifi_class = trim($wifi_class);

        // Get IP params
        $ip_params = array('host_ip_input', 'host_ip_output', 'host_netmask_input', 'host_netmask_output', 'host_gateway');

        // IP, mask and gateway settings
        foreach ($ip_params as $param) {
            $$param = $this->Config->get($param);
        }

        // DNS settings
        $host_dns1 = $this->Config->get('host_dns1');
        $host_dns2 = $this->Config->get('host_dns2');

        if ($this->request->is(['patch', 'post', 'put'])) {
            debug($this->request->getData());

            // return code used know if config can be saved
            // $rc = 1 : one on more field were not validated
            // $rc = 2 : input and output IP are in the same network
            // $rc = 3 : output IP and gateway IP are not in the same network
            $rc = 0;

            /*
            $confdata = array(
                'param' => 'host_name',
                'value' => $this->request->data['host_name']
            );

            $data_host_name = $this->Config->patchEntity($host_name, $confdata, [
                'validate' => 'alphanum',
            ]);

            if($data_host_name->errors()) {
                $this->Flash->set(__('Hostname must be Alphanumeric'), [ 
                    'key' => 'error_host_name',
                    'element' => 'custom_error' ]);
                $rc = 1;
            }
            */

            /*
             * UPDATING HOST INPUT AND OUTPUT INTERFACES ONLY IF Wi-Fi Access Point is disabled
             */

            if ($hostapd_enabled == 0) {
                // If input interface is the same as output interface, then create a virtual interface for input
                if($this->request->data['host_interface_input'] == $this->request->data['host_interface_output']) {
                    $this->request->data['host_interface_input'] = $this->request->data['host_interface_output'].":0";
                }
    
                // Patch input interface
                $confdata = array(
                    'param' => 'host_interface_input',
                    'value' => $this->request->data['host_interface_input']
                );
                $data_host_interface_input = $this->Config->patchEntity($host_interface_input, $confdata);
    
                // Patch output interface
                $confdata = array(
                    'param' => 'host_interface_ouput',
                    'value' => $this->request->data['host_interface_output']
                );
                $data_host_interface_output = $this->Config->patchEntity($host_interface_output, $confdata);
            }


            // IP validations
            foreach ($ip_params as $param)
            {
                $confdata = array(
                    'param' => $param,
                    'value' => $this->request->data[$param]
                    );

                ${"data_$param"} = $this->Config->patchEntity($$param, $confdata, [
                    'validate' => 'ipaddr',
                    ]);

                // IF VALIDATION RETURN ERROR
                if(${"data_$param"}->errors()) {
                    $this->Flash->set(__('Invalid IP address'), [ 
                        'key' => 'error_'.$param,
                        'element' => 'custom_error' ]);
                    $rc = 1;
                }
            }

            if ($rc != 1) {
                // Check if input ip address is not on same network as ouput ip address
                // Use Vendor IP4Calc class to calculate Subnet from host IP and Netmask
                $this->loadComponent('Ipv4');
                $iMask = $this->Ipv4->getNetwork($this->request->data['host_ip_input'], $this->request->data['host_netmask_input']);    
                $oMask = $this->Ipv4->getNetwork($this->request->data['host_ip_output'], $this->request->data['host_netmask_output']);
                $gMask = $this->Ipv4->getNetwork($this->request->data['host_gateway'], $this->request->data['host_netmask_output']);

                if ($iMask == $oMask) {
                    $rc = 2;
                }

                if ($oMask != $gMask) {
                    $rc = 3;
                }
            }

            // DNS validations
            $new_host_dns1 = $this->request->data['host_dns1'];
            $new_host_dns2 = $this->request->data['host_dns2'];

            // DNS config swapping or replacing depend on admin user input
            if (empty($new_host_dns1) and !empty($new_host_dns2)) {
                $new_host_dns1 = $new_host_dns2;
                $new_host_dns2 = null;
            } elseif (empty($new_host_dns1) and empty($new_host_dns2)) {
                $new_host_dns1 = '127.0.0.1';

            } elseif (!empty($new_host_dns1) and $new_host_dns1 == $new_host_dns2) {
                $new_host_dns2 = null;
            }

            // DNS1 VALIDATION
            $confdata = array(
                'param' => 'host_dns1',
                'value' => $new_host_dns1
            );

            $data_host_dns1 = $this->Config->patchEntity($host_dns1, $confdata, [
                'validate' => 'emptyipaddr',
                ]);

            if($data_host_dns1->errors()) {
                $this->Flash->set(__('Invalid IP address'), [ 
                    'key' => 'error_host_dns1',
                    'element' => 'custom_error' ]);
                $rc = 1;
            }

            // DNS2 VALIDATION
            $confdata = array(
                'param' => 'host_dns2',
                'value' => $new_host_dns2
            );

            $data_host_dns2 = $this->Config->patchEntity($host_dns2, $confdata, [
                'validate' => 'emptyipaddr',
                ]);

            if($data_host_dns1->errors()) {
                $this->Flash->set(__('Invalid IP address'), [ 
                    'key' => 'error_host_dns2',
                    'element' => 'custom_error' ]);
                $rc = 1;
            }

            // Update datas if validation are ok
            if ($rc == 0)
            {
                // Saving in database 
                //$this->Config->save($data_host_name);
                // save all config that are IP param
                foreach ($ip_params as $param)
                {
                    $this->Config->save(${"data_$param"});
                }

                // Save host interface (only if Wi-Fi Access Point is disabled) and DNS
                if ($hostapd_enabled == 0) {
                    $this->Config->save($data_host_interface_input);
                    $this->Config->save($data_host_interface_output);
                }
                $this->Config->save($data_host_dns1);
                $this->Config->save($data_host_dns2);

                // Updating configuration files 
                // var to count write configuration errors
                $count_cmd_rc = 0;

                // array that list command to run in shell 
                $config_cmds = [
                    'config network main',
                    //'config network hostname',
                    //'config dhcp main',
                    'config tor main',
                    'config bind named',
                    'config bind set_default_zone',
                    'config apache vhosts',
                    ];

                // Running commands
                foreach($config_cmds as $config_cmd) {
                    exec($this->kxycmd("$config_cmd"), $o, $cmd_rc);
                    $count_cmd_rc = $count_cmd_rc + $cmd_rc;
                }

                if($count_cmd_rc == 0) {
                    /*
                    $this->Flash->set(__('Network'), [
                        'key' => 'restart_network',
                        'element' => 'reboot_link', 
                        'params' => [ 'restartlink' => '/services/reboot' ]
                    ]);
                    */

                    // Only use for wizard config
                    $run_wizard = $this->Config->get('run_wizard');
                    $install_type = null;
                    if (isset($this->request->query['install_type'])) {
                        $install_type = $this->request->query['install_type'];
                    }

                    if ($run_wizard->value == 1) {
                        return $this->redirect(['controller' => 'Config', 'action' => 'wdhcp', 'install_type' => $install_type]);
                    } else {
                        $this->Flash->success(__('Network settings successfully saved. Please adjust the DHCP settings and reboot Keexybox.'));
                        return $this->redirect(['controller' => 'Config', 'action' => 'dhcp']);
                    }
                } else {
                    $this->Flash->error(__('Unable to write {0} configuration files.', null));
                }

            } elseif ($rc == 2) {
                $this->Flash->error(__('Internal network IP and ouput IP must be in a different subnets.'));
            } elseif ($rc == 3) {
                $this->Flash->error(__('Output network IP and gateway are not in the same subnet.'));
            } else {
                $this->Flash->error(__('Network settings have not been updated.'));
            }
        }
    
        // loop to show params on view
        $this->set('host_name', $host_name);
        $this->set('hostapd_enabled', $hostapd_enabled);
        $this->set('host_interface_input', $host_interface_input_value);
        $this->set('host_interface_output', $host_interface_output_value);
        $this->set('nic_devices', $nic_devices);
        $this->set('wifi_class', $wifi_class);

        foreach ($ip_params as $param) {
            $this->set($param, $$param);
        }
        $this->set('host_dns1', $host_dns1);
        $this->set('host_dns2', $host_dns2);
        $this->viewBuilder()->setLayout('adminlte');
    }
    /**
     * Edit Network settings for wizard
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function wnetwork()
    {
        $this->network();
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Edit WPA config file
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function wpa()
    {
        $wpa_config_file = $this->Config->get('wpa_config_file');
        $wpa_config_file_contents = file_get_contents($wpa_config_file['value']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            //debug($this->request->data);
            file_put_contents($wpa_config_file['value'], $this->request->data['wpa_config']);
            $wpa_config_file_contents = file_get_contents($wpa_config_file['value']);
        }
        //debug($wpa_config_file_contents);
        $this->set('wpa_config_file_contents', $wpa_config_file_contents);
        $this->set('wpa_config_file', $wpa_config_file['value']);
        $this->viewBuilder()->setLayout('adminlte-nh');
    }

    /**
     * Edit Wifi Access Point settings
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function wifiap()
    {
        /** Extract hostapd_% params from database **/

        $hostapd_settings = $this->Config->find('all', [ 'conditions' => [ 'param LIKE' => 'hostapd_%' ]]);
        foreach($hostapd_settings as $hostapd_setting) {
            $param = $hostapd_setting->param;
            $$param = $hostapd_setting->value;
        }

        // Get if hostapd was enable before update. It is used to know if we have to update network configuration
        $hostapd_was_enabled = $hostapd_enabled;

        /**************************************/
        /** Check and Save POST request data **/
        /**************************************/

        if ($this->request->is(['patch', 'post', 'put'])) {

            /** Update data in database **/ 
            // Retrieve requested data from Database
            $request_data = $this->request->getData();
            foreach ($request_data as $param => $value) {
                $$param = $this->Config->get($param);
            }

            // Set and validate each request data
            $validation_errors = 0;
            foreach ($request_data as $param => $value) {
                //debug($param);
                // Prepare data to commit
                $data = ['value' => $value];
                // Check data
                if ( $param == 'hostapd_ssid' ) {
                    $$param = $this->Config->patchEntity($$param, $data, ['validate' => 'ssid']);
                } else {
                    $$param = $this->Config->patchEntity($$param, $data);
                }
                // Count error
                if($$param->errors()) {
                    $this->Flash->set(__($$param->errors()['value']['hostapd']), [
                        'key' => 'error_'.$param,
                        'element' => 'custom_error' ]);
                    $validation_errors++;
                }
            }

            // If no error, save each data
            if ($validation_errors == 0) {
                foreach($request_data as $param => $value) {
                    $$param = $this->Config->save($$param);
                }
            }

            if ($hostapd_was_enabled == 0 && $hostapd_enabled->value == 1) {
                //debug('Change network configuration for Access Point');
                // 1 - Save values of input and output interfaces
                // Backup value of input interface
                $data = ['value' => $this->Config->get('host_interface_input')->value];
                $hostapd_host_interface_input_bak = $this->Config->get('hostapd_host_interface_input_bak');
                $hostapd_host_interface_input_bak = $this->Config->patchEntity($hostapd_host_interface_input_bak, $data);
                $hostapd_host_interface_input_bak = $this->Config->save($hostapd_host_interface_input_bak);

                // Backup value of output interface
                $data = ['value' => $this->Config->get('host_interface_output')->value];
                $hostapd_host_interface_output_bak = $this->Config->get('hostapd_host_interface_output_bak');
                $hostapd_host_interface_output_bak = $this->Config->patchEntity($hostapd_host_interface_output_bak, $data);
                $hostapd_host_interface_output_bak = $this->Config->save($hostapd_host_interface_output_bak);

                // 2 - Set Bridge interface as input and output interfaces
                // Set new input interface
                $data = ['value' => $this->Config->get('hostapd_bridge')->value.":0"];
                $host_interface_input = $this->Config->get('host_interface_input');
                $host_interface_input = $this->Config->patchEntity($host_interface_input, $data);
                $host_interface_input = $this->Config->save($host_interface_input);

                // Set new output interface
                $data = ['value' => $this->Config->get('hostapd_bridge')->value];
                $host_interface_output = $this->Config->get('host_interface_output');
                $host_interface_output = $this->Config->patchEntity($host_interface_output, $data);
                $host_interface_output = $this->Config->save($host_interface_output);

                // 3 - Update network and hostapd configs
                // array that list command to run in shell 
                $config_cmds = [
                    'config network main',
                    'config hostapd main',
                    ];


            } elseif ($hostapd_was_enabled == 1 && $hostapd_enabled->value == 0) {
                //debug('Restore network configuration');
                // 1 - Restore values of input and output interfaces
                $data = ['value' => $this->Config->get('hostapd_host_interface_input_bak')->value];
                $host_interface_input = $this->Config->get('host_interface_input');
                $host_interface_input = $this->Config->patchEntity($host_interface_input, $data);
                $host_interface_input = $this->Config->save($host_interface_input);

                $data = ['value' => $this->Config->get('hostapd_host_interface_output_bak')->value];
                $host_interface_output = $this->Config->get('host_interface_output');
                $host_interface_output = $this->Config->patchEntity($host_interface_output, $data);
                $host_interface_output = $this->Config->save($host_interface_output);

                // 2 - Update network and hostapd configs
                // array that list command to run in shell 
                $config_cmds = [
                    'config network main',
                    'config hostapd main',
                    ];

            } elseif ($hostapd_was_enabled == 1 && $hostapd_enabled->value == 1) {
                //debug('Update hostapd config only');
                // 1 - Update hostapd config only
                // array that list command to run in shell 
                $config_cmds = [
                    'config hostapd main',
                    ];
            } 

            // Updating configuration files 
            // var to count write configuration errors
            $count_cmd_rc = 0;

            // Running commands
            if (isset($config_cmds)) {
                foreach($config_cmds as $config_cmd) {
                    exec($this->kxycmd("$config_cmd"), $o, $cmd_rc);
                    $count_cmd_rc = $count_cmd_rc + $cmd_rc;
                }
            }

            if($count_cmd_rc == 0) {
                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (null !== $this->request->getQuery('install_type')) {
                    $install_type = $this->request->getQuery('install_type');
                }

                if ($run_wizard->value == 1) {
                    return $this->redirect(['controller' => 'Config', 'action' => 'wdhcp', 'install_type' => $install_type]);
                } else {
                    $this->Flash->success(__('Wi-Fi Access Point settings successfully saved.'));
                }
            } else {
                $this->Flash->error(__('Unable to write {0} configuration files.', null));
            }
        }

        /** Build lists for select controls **/

        // Getting network interface path
        $nic_path = $this->Config->get('nic_path');

        // List directory that contain network interface
        $nic_files = scandir($nic_path->value);
        $wifi_interfaces = null;
        $wired_interfaces = null;
        foreach($nic_files as $nic_file) {
            if(is_dir($nic_path->value."/".$nic_file."/wireless")) {
                $wifi_interfaces[$nic_file] = $nic_file;
            } else {
                if( $nic_file != "." AND $nic_file != ".." AND $nic_file != "lo" AND $nic_file != "br0") {
                    $wired_interfaces[$nic_file] = $nic_file;
                }
            }
        }

        $this->loadComponent('WifiAp');

        $this->set('wifi_interfaces', $wifi_interfaces);
        $this->set('wired_interfaces', $wired_interfaces);

        $country_list = $this->WifiAp->CountryList();
        $this->set('country_list', $country_list);

        $channel_list = $this->WifiAp->ChannelList();
        $this->set('channel_list', $channel_list);

        $hw_mode_list = $this->WifiAp->HwModeList();
        $this->set('hw_mode_list', $hw_mode_list);

        foreach($hostapd_settings as $hostapd_setting) {
            $this->set($hostapd_setting->param, $hostapd_setting->value);
        }

        /** View Layout **/
        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Edit DHCP settings
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function dhcp()
    {
        // Get if DHCP IS ENABLED
        $dhcp_enabled = $this->Config->get('dhcp_enabled');
        $dhcp_external = $this->Config->get('dhcp_external');
    
        // Get IP Param : Grouped for IP validation
        $params = array('dhcp_start_ip_input', 'dhcp_end_ip_input', 'dhcp_start_ip_output', 'dhcp_end_ip_output');
        foreach ($params as $param) {
            $$param = $this->Config->get($param);
        }

        $this->loadComponent('Ipv4');

        $InputInfo = $this->Ipv4->getInputInfo();
        $hiNet = $InputInfo['network'];
        $hiMaskDec = $InputInfo['mask_dec'];
        $host_netmask_input = $InputInfo['netmask'];

        $input_network_mask = "$hiNet/$hiMaskDec";

        $OutputInfo = $this->Ipv4->getOutputInfo();
        $hoNet = $OutputInfo['network'];
        $hoMaskDec = $OutputInfo['mask_dec'];
        $host_netmask_output = $OutputInfo['netmask'];

        $output_network_mask = "$hoNet/$hoMaskDec";

        // If Post Request
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Return code to know if all validations are ok
            $rc = 0;
    
            // Validate dhcp_enabled
            $confdata = array(
                'param' => 'dhcp_enabled',
                'value' => $this->request->data['dhcp_enabled']
                );
            $data_dhcp_enabled = $this->Config->patchEntity($dhcp_enabled, $confdata);
            if($data_dhcp_enabled->errors()) {
                $rc = 1;
            }

            // Validate dhcp_external
            $confdata = array(
                'param' => 'dhcp_external',
                'value' => $this->request->data['dhcp_external']
                );
            $data_dhcp_external = $this->Config->patchEntity($dhcp_external, $confdata);
            if($data_dhcp_external->errors()) {
                $rc = 1;
            }

            // Define and organize IP params to check
            $request_params = null;
            if($this->request->data['dhcp_external'] == true) {
                $request_params = array(
                        $hoNet => [
                            'netmask' => $host_netmask_output,
                            'range_params' => ['dhcp_start_ip_output', 'dhcp_end_ip_output'],
                            'used_ip' => [$OutputInfo['ip'], $InputInfo['ip'], $OutputInfo['gateway']],
                            ],
                        );
            } else {
                $request_params = array(
                        $hoNet => [
                            'netmask' => $host_netmask_output,
                            'range_params' => ['dhcp_start_ip_output', 'dhcp_end_ip_output'],
                            'used_ip' => [$OutputInfo['ip'], $InputInfo['ip'], $OutputInfo['gateway']],
                            ],
                        $hiNet => [
                            'netmask' => $host_netmask_input,
                            'range_params' => ['dhcp_start_ip_input', 'dhcp_end_ip_input'],
                            'used_ip' => [$OutputInfo['ip'], $InputInfo['ip'], $OutputInfo['gateway']],
                            ],
                        );
            }

            // loop for IP validations
            foreach ($request_params as $subnet => $validation_params)
            {
                foreach ($validation_params['range_params'] as $param) {
                    $confdata = array(
                        'param' => $param,
                        'value' => $this->request->data[$param]
                    );
        
                    ${"data_$param"} = $this->Config->patchEntity($$param, $confdata, [
                        'validate' => 'ipaddr',
                    ]);
        
                    // IF VALIDATION RETURN ERROR
                    if(${"data_$param"}->errors()) {
                        $this->Flash->set(__('Invalid IP address'), [ 
                            'key' => 'error_'.$param,
                            'element' => 'custom_error' ]);
                        $rc = 1;
                    }
    
                    if ($rc != 1 and $rc != 2 and $rc != 3) {
                        $ip_dhcp_subnet = $this->Ipv4->getNetwork($this->request->data[$param], $validation_params['netmask']);
                        // Change return code if one of DHCP IPs does not match with subnets
                        if ($ip_dhcp_subnet != $subnet) {
                            $rc = 2;
                        }
                        foreach ($validation_params['used_ip'] as $used_ip) {
                            if ($used_ip == $this->request->data[$param]) {
                                $rc = 3;
                            }
                        }
                    }
                }
            }

            // Update data if validations are ok
            if ($rc == 0)
            {
                $this->Config->save($data_dhcp_enabled);
                $this->Config->save($data_dhcp_external);
                /*
                foreach ($request_params as $param)
                {
                    $this->Config->save(${"data_$param"});
                }
                */

                foreach($request_params as $request_param) {
                    foreach($validation_params['range_params'] as $param) {
                        $this->Config->save(${"data_$param"});
                    }

                }

                exec($this->kxycmd("config dhcp main"), $output, $cmd_rc1);
                exec($this->kxycmd("config dhcp reservations"), $output, $cmd_rc2);
                $cmd_rc = $cmd_rc1 + $cmd_rc2;

                if ($cmd_rc == 0) {
                    // Restart DHCP if it is enabled
                    if($this->Config->get('dhcp_enabled')->value == 1) {
                        exec($this->kxycmd("service dhcp restart"), $output, $cmd_rc);
                    } else {
                        exec($this->kxycmd("service dhcp stop"), $output, $cmd_rc);
                        $cmd_rc = 0;
                    }


                    // If restart is ok or not needed
                    if($cmd_rc == 0) {

                        $run_wizard = $this->Config->get('run_wizard');
                        $install_type = null;
                        if (isset($this->request->query['install_type'])) {
                            $install_type = $this->request->query['install_type'];
                        }

                        if ($run_wizard->value == 1) {
                            $this->Flash->success(__('DHCP settings updated successfully.'));
                            return $this->redirect(['controller' => 'Blacklist', 'action' => 'wadd', 'install_type' => $install_type]);
                        } else {
                            $this->Flash->success(__('DHCP settings updated successfully.'));
                        }
                    } else {
                        $this->Flash->warning(__('DHCP settings updated successfully.')." ".__('But DHCP reload failed.'));
                    }
                } else {
                    $this->Flash->error(__('Unable to write {0} configuration files.', 'DHCP'));
                }

            } elseif ($rc == 2) {
                $this->Flash->error(__('One or more DHCP IP Addresses does not match with subnets.'));
            } elseif ($rc == 3) {
                $this->Flash->error(__('One or more DHCP IP Addresses are used by Keexybox.'));
            } else {
                $this->Flash->error(__('DHCP settings have not been updated.'));
            }
        }
    
        // Set params for view
        $this->set('dhcp_enabled', $dhcp_enabled);
        $this->set('dhcp_external', $dhcp_external);
        $this->set('input_network_mask', $input_network_mask);
        $this->set('output_network_mask', $output_network_mask);

        foreach ($params as $param) {
            $this->set($param, $$param);
        }

        $this->viewBuilder()->setLayout('adminlte');
    }
    /**
     * Edit DHCP settings
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function wdhcp()
    {
        $this->dhcp();
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Edit Certificate settings used for HTTP server
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function certificate()
    {
        $ssl_csr_c = $this->Config->get('ssl_csr_c', ['contain' => []]);
        $ssl_csr_cn = $this->Config->get('ssl_csr_cn', ['contain' => []]);
    
        // List of params to load
        $params = array('ssl_csr_st', 'ssl_csr_l', 'ssl_csr_o', 'ssl_csr_ou');
    
        // Load params
        foreach ($params as $param) {
            $$param = $this->Config->get($param, ['contain' => []]);
        }
    
        if ($this->request->is(['patch', 'post', 'put'])) {
            $rc = 0;
            
            // VALIDATION FOR COUNTRY CODE 2 LETTERS
            $confdata = array(
                'param' => 'ssl_csr_c',
                'value' => strtoupper($this->request->data['ssl_csr_c'])
                );
    
            $data_ssl_csr_c = $this->Config->patchEntity($ssl_csr_c, $confdata, [
                'validate' => 'alpha',
                ]);
    
            if($data_ssl_csr_c->errors()) {
                $this->Flash->set(__('Invalid country code'), [ 
                    'key' => 'error_ssl_csr_c',
                    'element' => 'custom_error' ]);
                $rc = 1;
            }
            
            // VALIDATION FOR FQDN
            if(isset($this->request->data['ssl_csr_cn']))
            {
                    $confdata = array(
                        'param' => 'ssl_csr_cn',
                        'value' => strtolower($this->request->data['ssl_csr_cn'])
                    );
        
                    $data_ssl_csr_cn = $this->Config->patchEntity($ssl_csr_cn, $confdata, [
                        'validate' => 'fqdn',
                    ]);
        
                    if($data_ssl_csr_cn->errors()) {
                        $this->Flash->set(__('Invalid domain'), [ 
                            'key' => 'error_ssl_csr_cn',
                            'element' => 'custom_error' ]);
                        $rc = 1;
                    }
            }
    
            foreach ($params as $param)
            {
                $confdata = array(
                    'param' => $param,
                    'value' => $this->request->data[$param]
                    );
    
                ${"data_$param"} = $this->Config->patchEntity($$param, $confdata, [
                    'validate' => 'names',
                    ]);
    
                // IF VALIDATION RETURN ERROR
                if(${"data_$param"}->errors()) {
                    $this->Flash->set(__('Invalid name'), [ 
                        'key' => 'error_'.$param,
                        'element' => 'custom_error' ]);
                    $rc = 1;
                }
            }
    
            // Update datas if validation are ok
            if ($rc == 0)
            {
                $this->Config->save($data_ssl_csr_c);
                if(isset($this->request->data['ssl_csr_cn'])) {
                    $this->Config->save($data_ssl_csr_cn);
                }
                foreach ($params as $param)
                {
                    $this->Config->save(${"data_$param"});
                }
                // Generate certificate
                exec($this->kxycmd("config certificate generate"), $o, $cmd_rc);
                if($cmd_rc == 0) {
                    $this->Flash->success(__('Certificate updated successfully. It will be active after next reboot.'));
                } else {
                    $this->Flash->success(__('Unable to generate certificate.'));
                }
            } else {
                $this->Flash->error(__('Certificate has not been updated.'));
            }
        }
        
        // Set to show params on view
        $this->set('ssl_csr_c', $ssl_csr_c);
        $this->set('ssl_csr_cn', $ssl_csr_cn);

        foreach ($params as $param) {
            $this->set($param, $$param);
        }

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Set date time, timezone and NTP
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function datetime()
    {
        $params = array('host_ntp1', 'host_ntp2');

        // Load params
        foreach ($params as $param) {
            $$param = $this->Config->get($param, ['contain' => []]);
        }

        // Get and set Time zone list
        $this->loadComponent('Times');
        //$tz = new tz();
        //$tzlist = $tz->TZlist();
        $tzlist = $this->Times->TZlist();

        $host_timezone = $this->Config->get('host_timezone', ['contain' => []]);
        date_default_timezone_set($host_timezone['value']);

        if ($this->request->is(['patch', 'post', 'put'])) {
            // Return code to know if all validations are ok
            $rc = 0;

            /*
             * Set system time configuration
             */
            $date = $this->request->data['date'];
            $time = $this->request->data['time'];

            // Time will be set based on new timezone
            date_default_timezone_set($this->request->data['tz']);
            // Get system timezone
            $systemtz = $this->Times->getSystemTimezone();

            // Convert requested user timezone to systeme systeme timezone
            $datetime = new Time("$date $time");
            $datetime = $datetime->timezone($systemtz)->format('Y-m-d H:i:s');

            // Set to system
            exec($this->kxycmd("config set_date_time $datetime"), $output, $rc);

            /*
             * End set system time configuration
             */

            $data = [ 
                'param' => 'host_timezone',
                'value' => $this->request->data['tz']
            ];

            /* Validate each NTP servers */
            foreach ($params as $param)
            {
                $confdata = array(
                    'param' => $param,
                    'value' => $this->request->data[$param]
                );

                /* Validate NTP is FQDN */
                ${"data_$param"} = $this->Config->patchEntity($$param, $confdata, [
                        'validate' => 'fqdn',
                    ]);

                /* if NTP is not FQDN then validate if it is an IP */
                if(${"data_$param"}->errors()) {
                    ${"data_$param"} = $this->Config->patchEntity($$param, $confdata, [
                        'validate' => 'ipaddr',
                    ]);

                    if(${"data_$param"}->errors()) {
                        $this->Flash->set(__('Invalid ntp server'), [ 
                            'key' => 'error_'.$param,
                            'element' => 'custom_error' ]);
                        $rc = 1;
                    }
                }
            }

            /* Update datas if validations are ok */
            if ($rc == 0) {

                $this->Config->patchEntity($host_timezone, $data);
                $this->Config->save($host_timezone);

                foreach ($params as $param)
                {
                    $this->Config->save(${"data_$param"});
                }

                exec($this->kxycmd("config ntp main"), $o, $cmd_rc);

                // Only use for wizard config
                $run_wizard = $this->Config->get('run_wizard');
                $install_type = null;
                if (isset($this->request->query['install_type'])) {
                    $install_type = $this->request->query['install_type'];
                }

                if($cmd_rc == 0) {
                    exec($this->kxycmd("service ntp restart"), $o, $cmd_rc);
                    if($cmd_rc == 0) {
                        $this->Flash->success(__('Date and time settings saved successfully.'));
                    } else {
                        $this->Flash->warning(__('Date and time settings saved successfully.')." ".__('But unable to restart NTP service.'));
                    }
                    if ($run_wizard->value == 1) {
                        return $this->redirect(['controller' => 'config', 'action' => 'wnetwork', 'install_type' => $install_type]);
                    }
                } else {
                    $this->Flash->error(__('Unable to write {0} configuration files.', 'NTP'));
                }

            } else {
                $this->Flash->error(__('Date and time settings have not been updated.'));
            }
        }
        
        // Set to view
        foreach ($params as $param) {
            $this->set($param, $$param);
        }
        $this->set('tzlist', $tzlist);
        $this->set('systemtz', $host_timezone);
        $this->viewBuilder()->setLayout('adminlte');
    }
    /**
     * Set date time, timezone and NTP
     *
     * @return void Redirects on successful edit, renders view otherwise.
     */
    public function wdatetime()
    {
        $this->datetime();
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * Edit captive portal settings
     *
     * @return void 
     */
    public function captiveportal()
    {
        // List of params to load
        //$params = array('dns_expiration_delay', 'connection_default_time', 'connection_max_time', 'log_db_retention', 'log_retention', 'bind_use_redirectors', 'locale');
        $params = array('connection_default_time', 'locale', 'cportal_register_allowed', 'cportal_register_code', 'cportal_register_expiration', 'cportal_default_profile_id');

        // Load params
        foreach($params as $setting) {
            //$$param = $this->Config->get($setting);
            //debug($$param);
            $param = $this->Config->get($setting)->param;
            $$param = $this->Config->get($setting)->value;
        }

        // Set list of available languages
        $this->loadComponent('Lang');
        $this->set('avail_languages', $this->Lang->ListLanguages());

        // Set list of available durations
        $this->loadComponent('ConnectionDuration');
        $this->set('avail_durations', $this->ConnectionDuration->GetDurationList());

        // Set list of profiles
        $this->loadModel('Profiles');
        $profiles = $this->Profiles->find('list');


        if($this->request->is('post')) {

            $request_data = $this->request->getData();
            foreach ($request_data as $param => $value) {
                $$param = $this->Config->get($param);
            }

            // Set and validate each request data
            $validation_errors = 0;
            foreach ($request_data as $param => $value) {
                //debug($param);
                // Prepare data to commit
                $data = ['value' => $value];
                // Check data
                if ( $param == 'connection_default_time' ) {
                    $data = ['value' => $value * 60];
                }
                //debug($data);
                $$param = $this->Config->patchEntity($$param, $data);

                // Count error
                if($$param->errors()) {
                    /*
                    $this->Flash->set(__($$param->errors()['value']['hostapd']), [
                        'key' => 'error_'.$param,
                        'element' => 'custom_error' ]);
                    */
                    $validation_errors++;
                }
            }

            // If no error, save each data
            if ($validation_errors == 0) {
                foreach($request_data as $param => $value) {
                    $$param = $this->Config->save($$param);
                }
                $this->Flash->success(__('Settings saved successfully.'));
            } else {
                $this->Flash->error(__('Settings could not be saved.')." ".__('Please try again.'));
	    }
        }

        // Set to view
        foreach($params as $setting) {
            $this->set($this->Config->get($setting)->param, $this->Config->get($setting)->value);
        }

        $this->set('profiles', $profiles);

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * Edit misc settings
     *
     * @return void 
     */
    public function misc()
    {
        // List of params to load
        //$params = array('dns_expiration_delay', 'connection_default_time', 'connection_max_time', 'log_db_retention', 'log_retention', 'bind_use_redirectors', 'locale');
        $params = array('dns_expiration_delay', 'log_db_retention', 'log_retention', 'bind_use_redirectors');

        // Load params
        foreach($params as $param) {
            $$param = $this->Config->get($param, ['contain' => []]);
        }

        if($this->request->is('post')) {
            // Return code to know if all field are validated
            $rc = 0;

            // Set and validate new value for dns_expiration_delay
            $data_dns_expiration_delay = ['value' => $this->request->data['dns_expiration_delay'] * 86400];

            $dns_expiration_delay = $this->Config->patchEntity(
                    $dns_expiration_delay, $data_dns_expiration_delay,
                    ['validate' => 'dns_expiration_delay']
                    );

            if($dns_expiration_delay->errors()) {
                $this->Flash->set(__('Routing cache expiration must be between 1 and 15 days.'), [ 
                        'key' => 'error_dns_expiration_delay',
                        'element' => 'custom_error' ]
                    );
                $rc = 1;
            } 
            
            // Set and validate new value for log_db_retention
            $data_log_db_retention = ['value' => $this->request->data['log_db_retention']];

            $data_log_db_retention = $this->Config->patchEntity(
                    $log_db_retention, $data_log_db_retention,
                    ['validate' => 'logs_retention']
                    );

            if($log_db_retention->errors()) {
                $this->Flash->set(__('Invalid log retention value'), [ 
                        'key' => 'error_log_db_retention',
                        'element' => 'custom_error' ]
                    );
                $rc = 1;
            } 

            // Set and validate new value for log_retention
            $data_log_retention = ['value' => $this->request->data['log_retention']];

            $data_log_retention = $this->Config->patchEntity(
                    $log_retention, $data_log_retention,
                    ['validate' => 'logs_retention']
                    );

            if($log_retention->errors()) {
                $this->Flash->set(__('Invalid log retention value'), [ 
                        'key' => 'error_log_retention',
                        'element' => 'custom_error' ]
                    );
                $rc = 1;
            } 

            // Set and validate new value for bind_use_redirectors
            $data_bind_use_redirectors = ['value' => $this->request->data['bind_use_redirectors']];

            $data_bind_use_redirectors = $this->Config->patchEntity($bind_use_redirectors, $data_bind_use_redirectors);

            if($data_bind_use_redirectors->errors()) {
                $this->Flash->set(__('Unable to set DNS redirectors'), [ 
                        'key' => 'error_bind_use_redirectors',
                        'element' => 'custom_error' ]
                    );
                $rc = 1;
            } 


            if ($rc == 0)
            {
                foreach ($params as $param)
                {
                    $this->Config->save($$param);
                }
                // Update bind config

                $count_cmd_rc = 0;
                exec($this->kxycmd("config bind named"), $o, $cmd_rc);
                $count_cmd_rc = $count_cmd_rc + $cmd_rc;
                exec($this->kxycmd("config logrotate main"), $o, $cmd_rc);
                $count_cmd_rc = $count_cmd_rc + $cmd_rc;

                // Reload bind
                if($count_cmd_rc == 0) {
                    exec($this->kxycmd("service bind reload"), $o, $cmd_rc);
                    if($cmd_rc == 0) {
                        $this->Flash->success(__('Settings saved successfully.'));
                    } else {
                        $this->Flash->warning(__('Settings saved successfully.')." ".__('But unable to reload DNS service.'));
                    }
                } else {
                    $this->Flash->error(__('Unable to write {0} configuration files.', null));
                }
            } else {
                $this->Flash->error(__('Settings could not be saved.')." ".__('Please try again.'));
            }
        }

        // Set to view
        foreach ($params as $param) {
            $this->set($param, $$param);
        }

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * This function clean domains routing DNS cache
     *
     * @return void Redirects to referer
     */
    public function ClearDnsCache()
    {
        $this->autoRender = false;

        $this->request->allowMethod(['post', 'delete']);

        $this->loadModel('DnsCache');
        $connection = ConnectionManager::get('default');
        $results = $connection->execute('TRUNCATE TABLE dns_cache'); 
        return $this->redirect($this->referer());
    }

    /**
     * This function shows the first wizard page configuration
     *
     * @return void
     */
    public function wstart()
    {
        $this->viewBuilder()->setLayout('wizard');
    }

    /**
     * This function shows the first wizard page configuration
     *
     * @return void
     */
    public function wend()
    {
        // Get IP params
        $host_ip_input = $this->Config->get('host_ip_input')->value;
        $host_ip_output = $this->Config->get('host_ip_output')->value;
        $apache_admin_https_port = $this->Config->get('apache_admin_https_port')->value;
        $apache_admin_port = $this->Config->get('apache_admin_port')->value;

        $install_type = null;
        if (isset($this->request->query['install_type'])) {
            $install_type = $this->request->query['install_type'];
        }

        $kxb_urls[] = null;
        if ($install_type == 2 || $install_type == 3) {
            $kxb_urls = [
                "http://$host_ip_output:$apache_admin_port",
                "http://keexybox:$apache_admin_port",
                "https://$host_ip_output:$apache_admin_https_port",
                "https://keexybox:$apache_admin_https_port",
                ];

        } else {
            $kxb_urls = [
                "http://$host_ip_input:$apache_admin_port",
                "http://$host_ip_output:$apache_admin_port",
                "http://keexybox:$apache_admin_port",
                "https://$host_ip_input:$apache_admin_https_port",
                "https://$host_ip_output:$apache_admin_https_port",
                "https://keexybox:$apache_admin_https_port",
                ];
        }

        $this->set('kxb_urls', $kxb_urls);


        if($this->request->is('post')) {
            // Disable wizard
            $run_wizard = $this->Config->get('run_wizard');
            $this->Config->patchEntity($run_wizard, ['value' => 0]);
            $this->Config->save($run_wizard);
            exec($this->kxycmd("service reboot"), $output, $rc);
        }

        $this->viewBuilder()->setLayout('wizard');
    }

}
