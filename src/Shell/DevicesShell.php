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

/**
 * This class manage Internet connection for Devices
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class DevicesShell extends BoxShell
{
    public function startup(){}

    /**
     * This function check if connection is registred in table keexybox.actives_connections
     * and get SQL query that can retrieve connection information
     *
     * @param $conn_name: It expect to be the device name
     * @param $ip: It is device IP address, required to reconnect a Device
     * 
     * @return Object of SQL Query that can retrieve Device connection information from database
     */
    private function GetRegistered($conn_name, $ip = null)
    {
        if(isset($ip)) {
            $actives_conns = $this->ActivesConnections->find('all', ['conditions' => [
                'ActivesConnections.name' => $conn_name,
                'type' => 'dev',
                'ip' => $ip
                ]]);
        } else {
            $actives_conns = $this->ActivesConnections->find('all', ['conditions' => [
                'ActivesConnections.name' => $conn_name,
                'type' => 'dev',
                ]]);
        }
        return $actives_conns;
    }

    /**
     * This function register a device into the table keexybox.actives_connections
     * This step is required before loading or unloading rules for a profile
     * It also add device IP in DNS ACL for domains filtering
     *
     * @param $conn_name: It expect to be the device name
     * @param $ip: It is device IP address. It can be use to force IP address for device.
     * 
     * @return interger: 0 on success else it is an error
     */
    private function Register($conn_name, $ip = null)
    {
        $device = $this->Devices->findByDevicename($conn_name)->first();

        date_default_timezone_set("UTC");
        $start_time = time();
        // Device has permanent access (Until 2038 bug)
        $end_time = '2147483640';

        // Return code to know if device ip regestred sucessfully to load routes
        $rc=1;

        if(!$device['id']) {
            $message = "No device match $conn_name";
            $rc = 1;
        } else {
            if($device['enabled'] == 1) {
                // if single ip is set, connect device

                // Need to put again date_default_timezone_set, dont know why date php function convert start_time with timezone set in db
                date_default_timezone_set("UTC");
                if(isset($ip)) {
                    $session_data = [
                            'ip' => $ip,
                            'name' => $device['devicename'],
                            //'name_id' => $device['id'],
                            'device_id' => $device['id'],
                            'profile_id' => $device['profile_id'],
                            'type' => 'dev',
                            'status' => 'running',
                            'mac' => $device['mac'],
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'display_start_time' => date('Y-m-d H:i:s', $start_time),
                            'display_end_time' => date('Y-m-d H:i:s', $end_time)
                    ];

                    $device_session = $this->ActivesConnections->newEntity();
                    $device_session = $this->ActivesConnections->patchEntity($device_session, $session_data);

                    if(!$device_session->getErrors()) {
                        $this->ActivesConnections->save($device_session); 
                        $message = "Device ".$device['devicename']." with profile ID ".$device['profile_id']." registred with ip address : $ip";
                        $rc = 0;
                    } else {
                        $message = "Device ".$device['devicename']." with profile ID ".$device['profile_id']." registred with ip address : $ip";
                        $rc = 1;
                    }
                // if single ip not set, get IPs from mac address to connect device
                } else {
                    //Get IPs from MAC Address
                    $arp = new ArpShell();
                    //$arps = $arp->GetMacIps(strtolower($device['mac']));
                    $arps = $arp->GetMacIps($device['mac']);

                    // Need to put again date_default_timezone_set, dont know why date php function convert start_time with timezone set in db
                    date_default_timezone_set("UTC");
                    foreach ($arps as $arp)
                    {  
                        $session_data = [
                        // ip is primary key don't forget to set as accessible in Model/Entity
                            'ip' => $arp[0],
                            'name' => $device['devicename'],
                            //'name_id' => $device['id'],
                            'device_id' => $device['id'],
                            'profile_id' => $device['profile_id'],
                            'type' => 'dev',
                            'status' => 'running',
                            'mac' => $device['mac'],
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'display_start_time' => date('Y-m-d H:i:s', $start_time),
                            'display_end_time' => date('Y-m-d H:i:s', $end_time)
                        ];

            
                        $device_session = $this->ActivesConnections->newEntity();
                        $device_session = $this->ActivesConnections->patchEntity($device_session, $session_data);

                        if(!$device_session->getErrors()) {
                            $this->ActivesConnections->save($device_session); 
                            $message = "Device ".$device_session['name']." with profile ID ".$device['profile_id']." registred with ip address : ".$device_session['ip'];

                            // UPDATE DNS BIND ACL 
                            $config = new ConfigShell();
                            $rc_bind_conf = $config->bind('update_acl', null, $exit = false);
                            if($rc_bind_conf == 100) {
                                $service = new ServiceShell();
                                $service->bind('reload', $exit = 'no');
                            }

                            $rc = 0;
                        } else {
                            $message = "Unable to register device ".$device_session['name']." or device already registred";
                            $rc = 1;
                        }
                    }
                }
            } else {
                $message = "Device ".$device['devicename']." not registred because it is disabled";
                $rc = 1;
            }
        }
        $this->out($message);
        $this->LogMessage($message, 'devices');
        return $rc;
    }

    /**
     * This function unregister a device into the table keexybox.actives_connections
     * It also remove device IP in DNS ACL for domains filtering
     *
     * @param $conn_name: It expect to be the device name
     * @param $ip: It is device IP address. It can be use to force IP address for device.
     * 
     * @return interger: 0 on success else it is an error
     */
    private function Unregister($conn_name, $ip = null)
    {
        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }

        $rc = 1;
        date_default_timezone_set("UTC");
        foreach ($actives_conns as $active_conn) {
            $active_conn = $this->ActivesConnections->get($active_conn['ip']);

            // Set connection history
            $end_time = time();
            $history_data = [
                'ip' => $active_conn->ip,
                'name' => $active_conn->name,
                'device_id' => $active_conn->device_id,
                'profile_id' => $active_conn->profile_id,
                'type' => $active_conn->type,
                'mac' => $active_conn->mac,
                'start_time' => $active_conn->start_time,
                'end_time' => $end_time,
                'duration' => $end_time - $active_conn->start_time,
                'display_start_time' => $active_conn->display_start_time,
                'display_end_time' => date('Y-m-d H:i:s', $end_time),
                ];

            if($this->ActivesConnections->delete($active_conn)) {
                // UPDATE DNS BIND ACL 
                $config = new ConfigShell();
                $rc_bind_conf = $config->bind('update_acl', null, $exit = 'no');
                if($rc_bind_conf == 100) {
                    $service = new ServiceShell();
                    $service->bind('reload', $exit = 'no');
                }
                // Add to history
                $connection_history = $this->ConnectionsHistory->newEntity();
                $connection_history = $this->ConnectionsHistory->patchEntity($connection_history, $history_data);
                $this->ConnectionsHistory->save($connection_history);

                $rc = 0;
                $this->LogMessage("Device ".$active_conn['name']." with ip address ".$active_conn['ip']." unregistred successfully", 'devices');
            }
        }
        return $rc;
    }

    /**
     * This function is use to change the device connection status into the table keexybox.actives_connections
     *
     * param $status: It can be "pause" or "running"
     * @param $conn_name: It expect to be the device name
     * @param $ip: It is device IP address. It can be use to force IP address for device.
     * 
     * @return void
     *
     */
    private function UpdateStatus($status, $conn_name, $ip = null)
    {
        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }

        foreach($actives_conns as $active_conn) {
            $active_conn = $this->ActivesConnections->get($active_conn['ip']);
            $session_data = [
                'status' => $status,
                ];
            $active_conn = $this->ActivesConnections->patchEntity($active_conn, $session_data);
            if(!$active_conn->getErrors()) {
                $this->ActivesConnections->save($active_conn); 
            }
        }
    }

    /**
     * This function load rules to connect device to Internet
     *
     * @param $conn_name: It expect to be the device name
     * @param $ip: It is device IP address. 
     * 
     * @return array: Errors informations encountered during the connection process
     *
     */
    public function EnableAccess($conn_name, $ip = null)
    {
        parent::initialize();
    
        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }
    
        if(!$actives_conns->isEmpty()) {

            $router = new RulesShell;

            foreach($actives_conns as $active_conn) {

                if($active_conn->status == 'running') {

                    // Show message and log action 
                    $message = "Enabling access for device ".$active_conn['name']." with profile ID ".$active_conn['profile_id'].", IP ".$active_conn['ip']." and MAC ".$active_conn['mac'];
                    $this->out($message);
                    $this->LogMessage($message, 'devices');
    
        
                    // Verify if rules for profiles are already been created
                    $rc = $router->CheckChainsExists($active_conn['profile_id']);
    
                    // If yes, load profile rules
                    if($rc != 0) {
                        $profile_rules = new ProfilesShell;
                        $create_access_return = $profile_rules->CreateAccess($active_conn['profile_id']);
                        if($create_access_return['rc'] == 1) {
                            $return['rc'] = 1;
                            return $return;
                        }
                    }
    
                    // Lookup if there are some time conditions for access
                    $conn_times = $this->ProfilesTimes->find('all', ['conditions' => [
                        'ProfilesTimes.profile_id' => $active_conn['profile_id']
                    ]]);
    
                    if($conn_times->isEmpty()) {
                        // If no time conditions are set, Access is enabled for 24/7 access
                        $router->LoadDeviceRules($active_conn['profile_id'], $active_conn['mac']);
                    } else {
                        foreach($conn_times as $conn_time) {
                            $router->LoadDeviceRules($active_conn['profile_id'], $active_conn['mac'], $conn_time['daysofweek'], $conn_time['timerange']);
                        }
                    }
    
                    // Check if device rules loaded successfully
                    $load_access_return = $router->GetRulesCount();
                    if(isset($create_access_return)) {
                        foreach(['count_rules', 'count_critical_errors', 'count_warning_errors'] as $count_value) {
                            $return[$count_value] = $load_access_return[$count_value] + $create_access_return[$count_value];
                        }
                    } else {
                        $return = $load_access_return;
                    }
    
                    if($return['count_critical_errors'] == 0 and $return['count_warning_errors'] == 0) {
                        $message = "All rules for $conn_name loaded successfully";
                        $return['rc'] = 0;
                    }
                    elseif($return['count_warning_errors'] > 0 and $return['count_critical_errors'] == 0) {
                        $message = $return['count_warning_errors']." warning error(s) on loading rules for $conn_name, check log files";
                        $return['rc'] = 2;
                    }
                    elseif($return['count_critical_errors'] > 0) {
                        $message = $return['count_critical_errors']." critical error(s) on loading rules for $conn_name, check log files";
                        $return['rc'] = 1;
                    }
                    else {
                        $message = "Loading rules status for $conn_name is unknown";
                        $return['rc'] = 3;
                    }
                } else {
                    $message = "Device $conn_name is in pause status. Access was not enabled for this device.";
                    $return['rc'] = 4;
                }

                $this->out($message);
                $this->LogMessage($message, 'devices');
                return $return;
            }
        } else {
            $message = "Unable to enable access : Device $conn_name is not registred";
            $this->out($message);
            $this->LogMessage($message, 'devices');
            $return['rc'] = 1;
            return $return;
        }
    }

    /**
     * This function unload rules to disconnect device from Internet
     *
     * @param $conn_name: It expect to be the device name
     * @param $ip: It is device IP address. 
     * 
     * @return array: Errors informations encountered during the connection process
     *
     */
    public function DisableAccess($conn_name, $ip = null)
    {
        parent::initialize();

        $message = null;
        $return = null;

        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }

        if($actives_conns->isEmpty()) {
            $message = "Device $conn_name must be registred before unloading rules";
            $return['rc'] = 1;
        } else {
            // LOADING DEVICE ROUTER
            $router = new RulesShell;
            foreach($actives_conns as $active_conn) {
                $message = "Removing access for device ".$active_conn['name']." with profile ID ".$active_conn['profile_id'].", IP ".$active_conn['ip']." and MAC ".$active_conn['mac'];
                $router->UnloadDeviceRules($active_conn['profile_id'], $active_conn['mac']);
            }

            $return = $router->GetRulesCount();

            if($return['count_critical_errors'] == 0 and $return['count_warning_errors'] == 0) {
                $message = "All rules for $conn_name unloaded successfully";
                $return['rc'] = 0;
            }
            elseif($return['count_warning_errors'] > 0 and $return['count_critical_errors'] == 0) {
                $message = $return['count_warning_errors']." warning error(s) on unloading rules for $conn_name, check log files";
                $return['rc'] = 2;
            }
            elseif($return['count_critical_errors'] > 0) {
                $message = $return['count_critical_errors']." critical error(s) on unloading rules for $conn_name, check log files";
                $return['rc'] = 1;
            }
            else {
                $message = "Unloading rules status for $conn_name is unknown";
                $return['rc'] = 3;
            }
        }

        $this->out($message);
        $this->LogMessage($message, 'devices');
        return $return;
    }

    /**
     * This function is called in CLI from Controllers to connect a device to Internet. 
     * The connect steps are as follows:
     *  - Register:      Register device into the database
     *  - EnableAccess:  Connect device to Internet
     *
     * @param $args[0]: It expect to be the device name
     * @param $args[1]: It is device IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function connect()
    {
        $rc = null;
        $message = null;

        if(empty($this->args[0])) {
            $message = "Device name argument is missing";
            $rc = 1;
            exit($rc);
        } else {
            $conn_name = $this->args[0];
            if(isset($this->args[1])) {
                $ip = $this->args[1];
            }
        }

        if(isset($ip)) {
            $rc = $this->Register($conn_name, $ip);
            if($rc == 0) {
                $return = $this->EnableAccess($conn_name, $ip);
                if($return['rc'] == 0) {
                    $message = "$conn_name with ip $ip successfully connected";
                    $rc = $return['rc'];
                } elseif($return['rc'] == 1) {
                    $message = "connection failed for $conn_name with ip $ip";
                    $rc = $return['rc'];
                } elseif($return['rc'] == 2) {
                    $message = "$conn_name with ip $ip connected with warning";
                    $rc = $return['rc'];
                } else {
                    $message = "$conn_name with ip $ip connection status unknown";
                    $rc = $return['rc'];
                }
            } else {
                $message = "Unable to register $conn_name with ip $ip";
                $rc = 1;
            }
        } else {
            $rc = $this->Register($conn_name);
            if($rc == 0) {
                $return = $this->EnableAccess($conn_name);
                if($return['rc'] == 0) {
                    $message = "$conn_name successfully connected";
                    $rc = $return['rc'];
                } elseif($return['rc'] == 1) {
                    $message = "connection failed for $conn_name";
                    $rc = $return['rc'];
                } elseif($return['rc'] == 2) {
                    $message = "$conn_name connected with warning";
                    $rc = $return['rc'];
                } else {
                    $message = "$conn_name connection status unknown";
                    $rc = $return['rc'];
                }
            } else {
                $message = "Unable to register $conn_name";
                $rc = 1;
            }
        }

        $this->out($message);
        $this->LogMessage($message, 'devices');
        exit($rc);
    }


    /**
     * This function is called in CLI by Controllers to disconnect a device from Internet. 
     * The disconnect steps are as follows:
     *  - DisableAccess: Disconnect device from Internet
     *  - Unregister:    Unregister device from the database
     *
     * @param $args[0]: It expect to be the device name
     * @param $args[1]: It is device IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function disconnect()
    {
        $rc = null;
        $message = null;

        if(empty($this->args[0])) {
            $message = "Device name argument is missing";
            $rc = 1;
            exit($rc);
        } else {
            $conn_name = $this->args[0];
            if(isset($this->args[1])) {
                $ip = $this->args[1];
            }
        }

        if(isset($ip)) {
            $return = $this->DisableAccess($conn_name, $ip);
            if($return['rc'] == 0) {
                $rc = $this->Unregister($conn_name, $ip);
                if($rc == 0 ) {
                    $message = "$conn_name with IP $ip successfully disconnected";
                    $rc = 0;
                } else {
                    $message = "Unable to disconnect $conn_name, or device is not connected";
                    $rc = 1;
                }
            } else {
                $message = "Unable to unload rules for $conn_name, unregister abord";
                $rc = 1;
            }
        } else {
            $return = $this->DisableAccess($conn_name);
            if($return['rc'] == 0) {
                $rc = $this->Unregister($conn_name);
                if($rc == 0 ) {
                    $message = "$conn_name successfully disconnected";
                    $rc = 0;
                } else {
                    $message = "Unable to disconnect $conn_name, or device is not connected";
                    $rc = 1;
                }
            } else {
                $message = "Unable to unload rules for $conn_name, unregister abord";
                $rc = 1;
            }
        }

        $this->out($message);
        $this->LogMessage($message, 'devices');
        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to pause Internet connection for a device. 
     * The pause steps are as follows:
     *  - GetRegistered: Retrieve connection informations from database
     *  - DisableAccess: Disconnect device from Internet
     *  - UpdateStatus:  Device connection status is set to "pause" into the database
     *
     * @param $args[0]: It expect to be the device name
     * @param $args[1]: It is device IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function pause()
    {
        if(empty($this->args[0])) {
            $message = "Devicename argument is missing";
            $rc = 1;
            exit($rc);
        } else {
            $conn_name = $this->args[0];
            if(isset($this->args[1])) {
                $ip = $this->args[1];
            }
        }

        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }

        $count_reconn_errors = 0;

        foreach($actives_conns as $active_conn) {
            if(isset($active_conn['ip'])) {
                $return = $this->DisableAccess($active_conn['name'], $active_conn['ip']);
            } else {
                $return = $this->DisableAccess($active_conn['name']);
            }

            if($return['rc'] == 0) {
                $this->UpdateStatus('pause', $active_conn['name'], $active_conn['ip']);
                $message = "Access for ".$active_conn['name']." with IP ".$active_conn['ip']." paused";
            } else {
                $message = "Unable to pause access for ".$active_conn['name']." with IP ".$active_conn['ip'];
                $count_reconn_errors++;
            }
            $this->out($message);
            $this->LogMessage($message, 'devices');
        }

        if($count_reconn_errors == 0) {
            $rc = 0;
        } else {
            $rc = 1;
        }

        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to resume Internet connection for a device. 
     * The run steps are as follows:
     *  - GetRegistered: Retrieve connection informations from database
     *  - UpdateStatus:  Device connection status is set to "running" into the database
     *  - EnableAccess:  Connect device to Internet
     *
     * @param $args[0]: It expect to be the device name
     * @param $args[1]: It is device IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function run()
    {
        if(empty($this->args[0])) {
            $message = "Devicename argument is missing";
            $rc = 1;
            exit($rc);
        } else {
            $conn_name = $this->args[0];
            if(isset($this->args[1])) {
                $ip = $this->args[1];
            }
        }

        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }

        $count_reconn_errors = 0;

        foreach($actives_conns as $active_conn) {
            $this->UpdateStatus('running', $active_conn['name'], $active_conn['ip']);
            if(isset($active_conn['ip'])) {
                $return = $this->EnableAccess($active_conn['name'], $active_conn['ip']);
            } else {
                $return = $this->EnableAccess($active_conn['name']);
            }

            if($return['rc'] == 0) {
                $message = "Access for ".$active_conn['name']." with IP ".$active_conn['ip']." now running";
            } else {
                $message = "Unable to run access for ".$active_conn['name']." with IP ".$active_conn['ip'];
                $count_reconn_errors++;
            }
            $this->out($message);
            $this->LogMessage($message, 'devices');
        }

        if($count_reconn_errors == 0) {
            $rc = 0;
        } else {
            $rc = 1;
        }

        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to disconnect and reconnect a device to Internet. 
     * The reconnect steps are as follows:
     *  - GetRegistered: Retrieve connection informations from database
     *  - DisableAccess: Disconnect device from Internet
     *  - Unregister:    Unregister device from the database
     *  - Register:      Register device into the database
     *  - EnableAccess:  Connect device to Internet
     *
     * @param $args[0]: It expect to be the device name
     * @param $args[1]: It is device IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function reconnect($conn_name, $ip = null)
    {
        if(empty($this->args[0])) {
            $message = "Device name argument is missing";
            $rc = 1;
            exit($rc);
        } else {
            $conn_name = $this->args[0];
            if(isset($this->args[1])) {
                $ip = $this->args[1];
            }
        }

        if(isset($ip)) {
            $actives_conns = $this->GetRegistered($conn_name, $ip);
        } else {
            $actives_conns = $this->GetRegistered($conn_name);
        }

        $rc = 0;
        $count_reconn_errors = 0;

        if($actives_conns->isEmpty()) {
            $message = "$conn_name is not registred, no reconnect done";
            $this->out($message);
            $this->LogMessage($message, 'devices');
            $rc = 1;
        } else {
            foreach($actives_conns as $active_conn) {
                // Reset duration
                $duration = $active_conn['end_time'] - time();
                $duration = round($duration / 60, 0);

                // Reconnect
                $return = $this->DisableAccess($conn_name, $active_conn['ip']);
                if($return['rc'] == 0) {
                    $rc = $this->Unregister($conn_name, $active_conn['ip']);
                    if($rc == 0) {
                        $rc = $this->Register($conn_name, $active_conn['ip'], $duration);
                        if($rc == 0) {
                            $return = $this->EnableAccess($conn_name, $active_conn['ip']);
                            if($return['rc'] != 0) {
                                $count_reconn_errors++;
                            }
                        } else {
                            $count_reconn_errors++;
                        }
                    } else {
                        $count_reconn_errors++;
                    }
                } else {
                    $count_reconn_errors++;
                }
            }

            if($count_reconn_errors == 0) {
                $message = "$conn_name successfully reconnected";
                $rc = 0;
            } else {
                $message = "Unable to reconnect all access for $conn_name";
                $rc = 1;
            }
        }
        exit($rc);
    }
}
?>
