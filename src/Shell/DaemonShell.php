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
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

/**
 * This class contains all functions that run regularly in background for Keexybox
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class DaemonShell extends BoxShell
{

    /**
     * This function refresh Domains Routing Cache 
     * and check if new check if new IP addresses have been found for domains routing and dynamically add/remove routing rules 
     *
     * @return void
     */
    public function UpdateActiveAccess()
    {
        parent::initialize();
        $dns = new DomainsRoutingCacheShell();
        $rules_shell = new RulesShell();

        // Removing expired Domains Routing Cache
        $removed_hosts = $dns->DeleteExpiredDnsCache();

        if(isset($removed_hosts)) {
            foreach($removed_hosts as $removed_host) {
                $conditions = [
                    'dst_ip' => $removed_host['ip'],
                ];

                $rules_shell->RemoveRules($conditions);
            }
        }

        // Adding new profiles routing
        $actives_profiles = $this->ActivesConnections->find()
            //->hydrate(false)
            ->enableHydration(false)
            ->select(['ActivesConnections.profile_id'])
            ->distinct(['ActivesConnections.profile_id']);

        if(isset($actives_profiles)) {
            foreach($actives_profiles as $active_profile) {
                $profile = $this->Profiles->find('all', ['conditions' => ['id' => $active_profile['profile_id']]])->contain(['ProfilesRouting'])->first();

                // Update DNS for each domain to route and add new route
                $rules_data = [];
                foreach($profile['profiles_routing'] as $profile_route) {
                    $ips = $dns->ResolveHostDaemon($profile_route['address']);
                    if(isset($ips['new'])) {
                        foreach($ips['new'] as $ip) {
                            if($profile_route['routing'] == 'direct') {
                                array_push($rules_data, ['rules_template' => 'direct', 'dst_ip' => $ip]);
                            }
                            elseif($profile_route['routing'] == 'tor') {
                                array_push($rules_data, ['rules_template' => 'tor', 'dst_ip' => $ip]);
                            }
                        }
                    }
                }
                //$access_data = $rules_shell->SetRules($profile['id'], $rules_data);
                $rules_shell->InsertProfileRules($profile['id'], $rules_data);
            }
            $return = $rules_shell->GetRulesCount();
            return $return;
        }
    }

    /**
     * This function reactivate access of all users and devices that are registred 
     * It is run in case on reboot or when keexybox is restarted
     *
     * @return void
     */
    public function ReconnectRegistred()
    {
        parent::initialize();
        $actives_connections = $this->ActivesConnections->find('all');
        $users_shell = new UsersShell;
        $devices_shell = new DevicesShell;
        foreach($actives_connections as $active_connection) {
            if($active_connection->type == 'usr') {
                $users_shell->EnableAccess($active_connection->name, $active_connection->ip);
            }
            elseif($active_connection->type == 'dev') {
                $devices_shell->EnableAccess($active_connection->name, $active_connection->ip);
            }
        }
    }

    /**
     * This function update ActivesConnections IPs for devices that may have change their IP
     *
     * @return void
     */
    public function UpdateRegistredDevicesIP()
    {
        parent::initialize();
        $arp = new ArpShell();
        $registred_devices = $this->ActivesConnections->find('all', ['conditions' => [
                'ActivesConnections.type' => 'dev',
                ]]);

        $need_to_update_bind_acl = false;

        foreach ($registred_devices as $registred_device) 
        {
            $arps = $arp->GetMacIps($registred_device['mac']);
            
            if(is_array($arps)) {
                foreach($arps as $a) 
                {
                    $ip = $a[0];
    
                    // Check if registred IP still the same and update if not
                    if($ip != $registred_device['ip']) {
                        $need_to_update_bind_acl = true;
                        $device_session = $this->ActivesConnections->get($registred_device['ip']);
                        // Keep everything and change IP
                        $session_data = [
                                'ip' => $ip,
                                'name' => $device_session->name,
                                'user_id' => $device_session->user_id,
                                'device_id' => $device_session->device_id,
                                'profile_id' => $device_session->profile_id,
                                'name_id' => $device_session->name_id,
                                'type' => $device_session->type,
                                'status' => $device_session->status,
                                'mac' => $device_session->mac,
                                'start_time' => $device_session->start_time,
                                'end_time' => $device_session->end_time,
                                'display_start_time' => $device_session->display_start_time,
                                'display_end_time' => $device_session->display_end_time,
                        ];
                        
                        $new_device_session = $this->ActivesConnections->newEntity();
                        $new_device_session = $this->ActivesConnections->patchEntity($new_device_session, $session_data);
                        if(!$new_device_session->errors()) {
                            if($this->ActivesConnections->save($new_device_session)) {
                                $this->out($registred_device['ip']." replaced by $ip for device ".$registred_device['name']." with MAC ".$registred_device['mac']);
                                $this->ActivesConnections->delete($device_session);
                            }
                        }
                    }
                }
            }
        }

        // Update bind ACL if some IP changed for devices
        if($need_to_update_bind_acl == true) {
            // UPDATE DNS BIND ACL 
            $config = new ConfigShell();
            $bind_conf_rc = $config->bind('update_acl', null, $exit = 'no');
            if($bind_conf_rc == 100) {
                $service = new ServiceShell();
                $service->bind('reload', $exit = 'no');
            }
        }
    }

    /**
     * This function terminates Users connections that have expired
     *
     * @return void
     */
    public function DisconnectExpiredConnections()
    {
        parent::initialize();
        $expired_connections = $this->ActivesConnections->find('all', ['conditions' => [ 
            'ActivesConnections.end_time <' => time(),
            'ActivesConnections.type' => 'usr'
            ]]);

        $usersShell = new UsersShell;

        foreach($expired_connections as $expired_connection)
        {
            $usersShell->disconnect($expired_connection['name'], $expired_connection['ip']);

        }
    }

    /**
     * This function import DNS Queries log into keexybox_logs database
     *
     * @return void
     */
    public function UpdateLogs()
    {
        parent::initialize();
        $logshell = new LogShell();

        // Import last logs
        $logshell->ImportLastDnsLog();

        // Import rotated logs in scheduled time

        // Get timezone
        $this->loadModel('Config');
        $timezone = $this->Config->get('host_timezone');
        $timezone = $timezone['value'];

        // Get current time
        $now = Time::now();
        $now_time = $now->format('Hi');

        $sched_time_start = new Time($this->log_import_schedule_time, $timezone);

        $sched_time_end = new Time($this->log_import_schedule_time, $timezone);
        $sched_time_end = $sched_time_end->modify('+10 minutes');

        $sched_timerange_start = $sched_time_start->format('Hi');
        $sched_timerange_end = $sched_time_end->format('Hi');

        if($now_time >= $sched_timerange_start and $now_time <= $sched_timerange_end) {
            // Import last rotated logs
            $logshell->UpdateRotatedDnsLog();
            // Remove older imported log from database
            $logshell->PurgeLog();
        }
    }
    /*
     * This function refresh the Blacklist
     * @return void
     */
    public function UpdateBlacklist()
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

    /**
     * This function simply run all above functions
     *
     * @return void
     */
    public function main()
    {
        $this->DisconnectExpiredConnections();
        $this->UpdateActiveAccess();
        $this->UpdateRegistredDevicesIP();
        $this->UpdateBlacklist();
        $this->UpdateLogs();
    }
}
