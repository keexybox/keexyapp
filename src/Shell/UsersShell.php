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
 * This class manage Internet connection for Users and also reset Admin password
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class UsersShell extends BoxShell
{
    public function startup(){}

    /**
     * Create admin account or reinitialize admin password
     *     This function is used when installing Keexybox or to recover admin password
     *
     * @param password : cleartext password
     * 
     * @return void
     */
    public function UpdateAdminPassword($password = null)
    {
        if(!isset($password)) {
            $this->out("Please enter new admin password: ");
            system('stty -echo');
            $password = trim(fgets(STDIN));
            system('stty echo');

            $this->out("Confirm password: ");
            system('stty -echo');
            $password_confirm = trim(fgets(STDIN));
            system('stty echo');
        } else {
            $password_confirm = $password;
        }

        $user = $this->Users->findById(1)->toArray();
        if($user == []) {
            $user = $this->Users->newEntity();
            $user_data = [
                'id' => 1,
                'username' => 'admin',
                'displayname' => 'Keexybox Admin',
                'password' => $password,
                'confirm_password' => $password_confirm,
                'profile_id' => 1,
                'enabled' => 1,
                'admin' => 1
                ];

            $user = $this->Users->patchEntity($user, $user_data);
            if($this->Users->save($user)) {
                $this->out("Success! The admin account did not exist and has been created with the given password.");
                exit(0);
            } else {
                $this->out("Failed! Unable to create admin account");

                // Show errors messages from Model
                $errors = $user->getErrors();
                foreach($errors as $error) {
                    $error = array_values($error);
                    $this->out($error);
                }
                exit(1);
            }
        } else {
            $user = $this->Users->get(1);
            $user_data = [
                'password' => $password,
                'confirm_password' => $password_confirm,
                ];

            $user = $this->Users->patchEntity($user, $user_data);
            if($this->Users->save($user)) {
                $this->out("Success! $user->username account updated with the specified password.");
                exit(0);
            } else {
                $this->out("Failed! Unable to update $user->username account");

                // Show errors messages from Model
                $errors = $user->getErrors();
                foreach($errors as $error) {
                    $error = array_values($error);
                    $this->out($error);
                }
                exit(1);
            }
        }
    }

    /**
     * This function check if connection is registred in table keexybox.actives_connections
     * and get SQL query that can retrieve connection information
     *
     * @param $conn_name: It expect to be the user login
     * @param $ip: It is user IP address, required to reconnect a user
     * 
     * @return Object of SQL Query that can retrieve user connection informations from database
     */
    public function GetRegistered($conn_name, $ip = null)
    {
        if(isset($ip)) {
            $actives_conns = $this->ActivesConnections->find('all', ['conditions' => [
                'ActivesConnections.name' => $conn_name,
                'type' => 'usr',
                'ip' => $ip
            ]]);
        } else {
            $actives_conns = $this->ActivesConnections->find('all', ['conditions' => [
                'ActivesConnections.name' => $conn_name,
                'type' => 'usr',
            ]]);
        }
        return $actives_conns;
    }

    /**
     * This function register a user into the table keexybox.actives_connections
     * This step is required before loading or unloading rules for a profile
     * It also add user IP in DNS ACL for domains filtering
     *
     * @param $conn_name: It expect to be the user login
     * @param $ip: It is user IP address. It can be use to force IP address for user.
     * 
     * @return interger: 0 on success else it is an error
     */
    public function Register($conn_name, $ip, $duration, $client_details = null)
    {
        $user = $this->Users->findByUsername($conn_name)->first();

        date_default_timezone_set("UTC");
        $start_time = time();
        $duration = $duration * 60;
        $end_time = $start_time + $duration;

        // Do not record UserAgent details if disabled
        if($this->cportal_record_useragent == false) {
            $client_details = null;
        }

        // Get and Record Mac Address of client if enabled
        $mac = null;
        if($this->cportal_record_mac == true) {
            $arp = new ArpShell();
            $mac = $arp->GetMac($ip);
        }

        // Return code to know if user ip regestred sucessfully to load routes
        $rc = 0;

        if(!$user['id']) {
            $message = "No user match $conn_name";
            $rc = 1;
        } else {
            if($user['enabled'] == 1) {
                // if single ip is set, connect user
                $session_data = [
                    'ip' => $ip,
                    'name' => $user['username'],
                    //'name_id' => $user['id'],
                    'user_id' => $user['id'],
                    'profile_id' => $user['profile_id'],
                    'type' => 'usr',
                    'status' => 'running',
                    'mac' => $mac,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'client_details' => $client_details,
                    'display_start_time' => date('Y-m-d H:i:s', $start_time),
                    'display_end_time' => date('Y-m-d H:i:s', $end_time)
                ];
                
                $user_session = $this->ActivesConnections->newEntity();
                $user_session = $this->ActivesConnections->patchEntity($user_session, $session_data);

                if(!$user_session->getErrors()) {
                    $this->ActivesConnections->save($user_session); 
                    $message = "User ".$user['username']." with profile ID ".$user['profile_id']." registred with IP address : $ip";

                    // UPDATE DNS BIND ACL 
                    $config = new ConfigShell();
                    $bind_conf_rc = $config->bind('update_acl', null, $exit = 'no');
                    if($bind_conf_rc == 100) {
                        $service = new ServiceShell();
                        $service->bind('reload', $exit = 'no');
                    }

                    $rc = 0;
                } else {
                    $message = "Unable to register user ".$user_session['name']." or user already registred";
                    $rc = 1;
                }
            } else {
                $message = "User ".$user['username']." not registred because account is disabled";
                $rc = 1;
            }
        }
        $this->out($message);
        $this->LogMessage($message, 'users');
        return $rc;
    }

    /**
     * This function unregister a user into the table keexybox.actives_connections
     * It also remove user IP in DNS ACL for domains filtering
     *
     * @param $conn_name: It expect to be the user login
     * @param $ip: It is user IP address. It can be use to force IP address for user.
     * 
     * @return interger: 0 on success else it is an error
     */
    public function Unregister($conn_name, $ip = null)
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
                'user_id' => $active_conn->user_id,
                'profile_id' => $active_conn->profile_id,
                'type' => $active_conn->type,
                'mac' => $active_conn->mac,
                'client_details' => $active_conn->client_details,
                'start_time' => $active_conn->start_time,
                'end_time' => $end_time,
                'duration' => $end_time - $active_conn->start_time,
                'display_start_time' => $active_conn->display_start_time,
                'display_end_time' => date('Y-m-d H:i', $end_time),
                ];

            if($this->ActivesConnections->delete($active_conn)) {

                // UPDATE DNS BIND ACL FIRST 
                $config = new ConfigShell();
                $bind_conf_rc = $config->bind('update_acl', null, $exit = 'no');
                if($bind_conf_rc == 100) {
                    $service = new ServiceShell();
                    $service->bind('reload', $exit = 'no');
                }

                // Add to history
                $connection_history = $this->ConnectionsHistory->newEntity();
                $connection_history = $this->ConnectionsHistory->patchEntity($connection_history, $history_data);
                $this->ConnectionsHistory->save($connection_history);

                $rc = 0;
                $this->LogMessage("User ".$active_conn['name']." with ip address ".$active_conn['ip']." unregistred successfully", 'users');
            }
        }
        return $rc;
    }

    /**
     * This function is use to change the user connection status into the table keexybox.actives_connections
     *
     * param $status: It can be "pause" or "running"
     * @param $conn_name: It expect to be the user login
     * @param $ip: It is user IP address. It can be use to force IP address for user.
     * 
     * @return void
     *
     */
    public function UpdateStatus($status, $conn_name, $ip = null)
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
     * This function load rules to connect user to Internet
     * EnableAccess is possible only if connection is registred and in running status
     *
     * @param $conn_name: It expect to be the user login
     * @param $ip: It is user IP address. 
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
                    $message = "Enabling access for user ".$active_conn['name']." with profile ID ".$active_conn['profile_id']." and IP ".$active_conn['ip'];
                    $this->out($message);
                    $this->LogMessage($message, 'users');
        
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
    
                    // Lookup if there are some time conditions for user access
                    $conn_times = $this->ProfilesTimes->find('all', ['conditions' => [
                        'ProfilesTimes.profile_id' => $active_conn['profile_id']
                    ]]);
    
                    if($conn_times->isEmpty()) {
                        // If no time conditions are set, Access is enabled for 24/7 access
                        $router->LoadUserRules($active_conn['profile_id'], $active_conn['ip']);
                    } else {
                        foreach($conn_times as $conn_time) {
                            $router->LoadUserRules($active_conn['profile_id'], $active_conn['ip'], $conn_time['daysofweek'], $conn_time['timerange']);
                        }
                    }
    
                    // Check if user rules loaded successfully
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
                    $message = "User $conn_name is in pause status. Access was not enabled for this user.";
                    $return['rc'] = 4;
                }

                $this->out($message);
                $this->LogMessage($message, 'users');
                return $return;
            }
        } else {
            $message = "Unable to enable access : User $conn_name is not registred";
            $this->out($message);
            $this->LogMessage($message, 'users');
            $return['rc'] = 1;
            return $return;
        }
    }

    /**
     * This function unload rules to disconnect user from Internet
     *
     * @param $conn_name: It expect to be the user login
     * @param $ip: It is user IP address. 
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
            $message = "User $conn_name must be registred before unloading rules";
            $return['rc'] = 1;
        } else {


            // LOADING ROUTER
            $router = new RulesShell;
            foreach($actives_conns as $active_conn) {
                $message = "Removing access for user ".$active_conn['name']." with profile ID ".$active_conn['profile_id']." and IP ".$active_conn['ip'];
                $router->UnloadUserRules($active_conn['profile_id'], $active_conn['ip']);
            }

            $return = $router->GetRulesCount();

            if($return['count_critical_errors'] == 0 and $return['count_warning_errors'] == 0) {
                $message = "All rules for user $conn_name unloaded successfully";
                $return['rc'] = 0;
            }
            elseif($return['count_warning_errors'] > 0 and $return['count_critical_errors'] == 0) {
                $message = $return['count_warning_errors']." warning error(s) on unloading rules for user $conn_name, check log files";
                $return['rc'] = 2;
            }
            elseif($return['count_critical_errors'] > 0) {
                $message = $return['count_critical_errors']." critical error(s) on unloading rules for user $conn_name, check log files";
                $return['rc'] = 1;
            }
            else {
                $message = "Unloading rules status for user $conn_name is unknown";
                $return['rc'] = 3;
            }
        }

        $this->out($message);
        $this->LogMessage($message, 'users');
        return $return;
    }

    /**
     * This function is called in CLI from Controllers to connect a user to Internet. 
     * The connect steps are as follows:
     *  - Register:      Register user into the database
     *  - EnableAccess:  Connect user to Internet
     *
     * @param $args[0]: It expect to be the user login
     * @param $args[1]: It is user IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function connect()
    {
        $rc = null;
        $message = null;
        if (empty($this->args[0]) or empty($this->args[1]) or empty($this->args[2])) {
            $message = "Unable to connect username, ip or duration arguments are missing";
            $rc = 1;
            exit($rc);
        } else {
            $conn_name = $this->args[0];
            $ip = $this->args[1];
            $duration = $this->args[2];
            if(isset($this->args[3])) {
                $client_details = $this->args[3];
            }
        }

        if(isset($conn_name) and isset($ip) and isset($duration)) {
            $rc = $this->Register($conn_name, $ip, $duration, $client_details);
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
            $message = "Unable to connect username, ip or duration arguments are missing";
            $rc = 1;
        }

        $this->out($message);
        $this->LogMessage($message, 'users');
        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to disconnect a user from Internet. 
     * The disconnect steps are as follows:
     *  - DisableAccess: Disconnect user from Internet
     *  - Unregister:    Unregister user from the database
     *
     * @param $args[0]: It expect to be the user login
     * @param $args[1]: It is user IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function disconnect($conn_name, $ip = null)
    {
        $rc = null;
        $message = null;

        if(isset($ip)) {
            $return = $this->DisableAccess($conn_name, $ip);
            if($return['rc'] == 0) {
                $rc = $this->Unregister($conn_name, $ip);
                if($rc == 0 ) {
                    $message = "$conn_name with IP $ip successfully disconnected";
                    $rc = 0;
                } else {
                    $message = "Unable to disconnect $conn_name, or user is not connected";
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
                    $message = "Unable to disconnect $conn_name, or user is not connected";
                    $rc = 1;
                }
            } else {
                $message = "Unable to unload rules for $conn_name, unregister abord";
                $rc = 1;
            }
        }

        $this->out($message);
        $this->LogMessage($message, 'users');
        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to pause Internet connection for a user. 
     * The pause steps are as follows:
     *  - GetRegistered: Retrieve connection informations from database
     *  - DisableAccess: Disconnect user from Internet
     *  - UpdateStatus:  User connection status is set to "pause" into the database
     *
     * @param $args[0]: It expect to be the user login
     * @param $args[1]: It is user IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function pause()
    {
        if(empty($this->args[0])) {
            $message = "Username argument is missing";
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
                $message = "Access for user ".$active_conn['name']." with IP ".$active_conn['ip']." paused";
            } else {
                $message = "Unable to pause access for user ".$active_conn['name']." with IP ".$active_conn['ip'];
                $count_reconn_errors++;
            }
            $this->out($message);
            $this->LogMessage($message, 'users');
        }

        if($count_reconn_errors == 0) {
            $rc = 0;
        } else {
            $rc = 1;
        }

        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to resume Internet connection for a user. 
     * The run steps are as follows:
     *  - GetRegistered: Retrieve connection informations from database
     *  - UpdateStatus:  User connection status is set to "running" into the database
     *  - EnableAccess:  Connect user to Internet
     *
     * @param $args[0]: It expect to be the user login
     * @param $args[1]: It is user IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function run()
    {
        if(empty($this->args[0])) {
            $message = "Username argument is missing";
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

        // Update that status to running

        foreach($actives_conns as $active_conn) {
            $this->UpdateStatus('running', $active_conn['name'], $active_conn['ip']);

            if(isset($active_conn['ip'])) {
                $return = $this->EnableAccess($active_conn['name'], $active_conn['ip']);
            } else {
                $return = $this->EnableAccess($active_conn['name']);
            }

            if($return['rc'] == 0) {
                $message = "Access for user ".$active_conn['name']." with IP ".$active_conn['ip']." now running";
            } else {
                $message = "Unable to run access for user ".$active_conn['name']." with IP ".$active_conn['ip'];
                $count_reconn_errors++;
            }
            $this->out($message);
            $this->LogMessage($message, 'users');
        }

        if($count_reconn_errors == 0) {
            $rc = 0;
        } else {
            $rc = 1;
        }

        exit($rc);
    }

    /**
     * This function is called in CLI by Controllers to disconnect and reconnect a user to Internet. 
     * The reconnect steps are as follows:
     *  - GetRegistered: Retrieve connection informations from database
     *  - DisableAccess: Disconnect user from Internet
     *  - Unregister:    Unregister user from the database
     *  - Register:      Register user into the database
     *  - EnableAccess:  Connect user to Internet
     *
     * @param $args[0]: It expect to be the user login
     * @param $args[1]: It is user IP address. 
     * 
     * @return Integer: 0 on success else it is an error
     *
     */
    public function reconnect()
    {
        if(empty($this->args[0])) {
            $message = "Username argument is missing";
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
            $this->LogMessage($message, 'users');
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
