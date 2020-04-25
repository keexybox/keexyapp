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
 * This class load rules for a profile that will be used by users or devices.
 * It is also use to reset default profile setting in database.
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class ProfilesShell extends BoxShell
{
    /**
     * Create default profile or reset it
     *     This function is used when installing Keexybox or to recover admin password
     *
     * @return void
     */
    public function ResetDefaultProfile()
    {
        $profile = $this->Profiles->findById(1)->toArray();
        if($profile == []) {
            $profile = $this->Profiles->newEntity();
        } else {
            $profile = $this->Profiles->get(1);
        }

        $profile_data = [
            'id' => 1,
            'profilename' => 'default',
            'enabled' => 1,
            'default_routing' => 'direct',
            'default_ipfilter' => 'ACCEPT',
            'log_enabled' => 0,
            'use_ttdnsd' => 0,
            'safesearch_google' => 0,
            'safesearch_bing' => 0,
            'safesearch_youtube' => 0,
            ];

        $profile = $this->Profiles->patchEntity($profile, $profile_data);
        if($this->Profiles->save($profile)) {
            $this->out("Success! The default profile has been reset.");
            exit(0);
        } else {
            $this->out("Failed! Unable to reset default profile");

            // Show errors messages from Model
            $errors = $profile->errors();
            foreach($errors as $error) {
                $error = array_values($error);
                $this->out($error);
            }
            exit(1);
        }
    }

    /** 
     * This function retrieves default profile connection settings of given profile_id 
     * and set the rules templates to use for them.
     *
     * @param $profile_id: profile ID to define templates for.
     *
     * @return array: contains rule templates to use
     */
    private function SetDefaultRulesTemplates($profile_id)
    {
        parent::initialize();
        $profile = $this->Profiles->findById($profile_id)->First();
        if(isset($profile['id'])) {
            $access_data = [];

            // Define DNS NAT
            if($profile['log_enabled'] == 1 and $profile['use_ttdnsd'] == 0) {
                array_push($access_data, ['rules_template' => 'profile_default_prerouting_dnslog']);
            }
            elseif($profile['log_enabled'] == 0 and $profile['use_ttdnsd'] == 0) {
                array_push($access_data, ['rules_template' => 'profile_default_prerouting_dnsnolog']);
            }
            elseif($profile['log_enabled'] == 1 and $profile['use_ttdnsd'] == 1) {
                array_push($access_data, ['rules_template' => 'profile_default_prerouting_dnstorlog']);
            }
            elseif($profile['log_enabled'] == 0 and $profile['use_ttdnsd'] == 1) {
                array_push($access_data, ['rules_template' => 'profile_default_prerouting_dnstornolog']);
            }
            else {
                array_push($access_data, ['rules_template' => 'profile_default_prerouting_dnslog']);
            }

            // Define if Tor Access by default
            if($profile['default_routing'] == 'tor') {
                array_push($access_data, ['rules_template' => 'profile_default_prerouting_tor']);
            }

            array_push($access_data, ['rules_template' => 'profile_default_input']);

            if ($profile['default_ipfilter'] == 'ACCEPT') {
                array_push($access_data, ['rules_template' => 'profile_default_forward_accept']);
            } elseif ($profile['default_ipfilter'] == 'DROP') {
                array_push($access_data, ['rules_template' => 'profile_default_forward_drop']);
            } else {
                array_push($access_data, ['rules_template' => 'profile_default_forward_drop']);
            }

            array_push($access_data, ['rules_template' => 'profile_default_output']);

            return $access_data;
        } 
    }

    /** 
     * This function retrieves all domains routing for given profile_id 
     * and set the rules templates to use for them with additionnal settings.
     *
     * @param $profile_id: profile ID to define templates for.
     *
     * @return array: contains rule templates to use
     */
    private function SetRoutingRulesTemplates($profile_id)
    {
        parent::initialize();

        $dns = new DomainsRoutingCacheShell();
        $rules = new RulesShell();

        $profile = $this->Profiles->findById($profile_id)->contain(['ProfilesRouting'])->first();

        if(isset($profile['id'])) {

            // Force DNS resolution before returing websites list
            foreach($profile['profiles_routing'] as $website) {
                $dns->ResolveHost($website['address']);
            }

            // Request profile routing and exclude access that are the same as the default routing
            // Note : Table Routes is a view that merge multiple table
            $routing = $this->Routes->find('all', ['conditions' => [
                    'Routes.profile_id' => $profile_id,
                    'not' => ['Routes.routing' => $profile['default_routing']]
                    ]]);

            $access_data = [];

            // Set data for Website routing
            foreach($routing as $route) {
                array_push($access_data, ['rules_template' => "profile_routing_".$route['routing'], 'dst_ip' => $route['dstip']]);
            }

            return $access_data;
        }
    }

    /** 
     * This function retrieves all Firewall rules (profile_ipfilters) for given profile_id 
     * and set the rules templates to use for them with additionnal settings
     *
     * @param $profile_id: profile ID to define templates for.
     *
     * @return array: contains rule templates to use
     */
    private function SetFirewallRulesTemplates($profile_id)
    {
        $profile = $this->Profiles->findById($profile_id)->contain(['ProfilesIpfilters'])->first();

        $dns = new DomainsRoutingCacheShell();
        $rules = new RulesShell();

        if(isset($profile['id'])) {
    
            $fw_rules = $this->FwRules->find('all', [
                    'conditions' => ['FwRules.profile_id' => $profile_id],
                    'order' => ['FwRules.rule_number' => 'DESC']
                    //'order' => ['FwRules.rule_number' => 'ASC']
                ]);
    
            $access_data = [];

            foreach($fw_rules as $fw_rule) {

                $fw_rule_data = [];

                $fw_rule_data['rule_number'] = $fw_rule['rule_number'];
                $fw_rule_data['protocol'] = $fw_rule['protocol'];
                $fw_rule_data['dest_ports'] = $fw_rule['dest_ports'];
                $fw_rule_data['target'] = $fw_rule['target'];
    
                if($fw_rule['dest_ip_type'] == 'net') {
                    $fw_rule_data['rules_template'] = 'profile_firewall_net_'.$fw_rule_data['protocol']; 
                    $fw_rule_data['dest_ip'] = $fw_rule['dest_ip'];
                    $fw_rule_data['dest_ip_mask'] = $fw_rule['dest_ip_mask'];
                }
    
                elseif($fw_rule['dest_ip_type'] == 'range') {
                    $fw_rule_data['rules_template'] = 'profile_firewall_range_'.$fw_rule_data['protocol']; 
                    $fw_rule_data['dest_iprange_first'] = $fw_rule['dest_iprange_first'];
                    $fw_rule_data['dest_iprange_last'] = $fw_rule['dest_iprange_last'];
                }

                array_push($access_data, $fw_rule_data);
            }

            return $access_data;
        }
    }

    /** 
     * This function will activates Default, Routing and Firewall rules for given profile_id.
     * The steps are as follows :
     *  - CreateProfileChains: Create empty new iptables Chains for the profile
     *  - LoadProfileDefaultRules: Load default profile rules using retrieved data from SetDefaultRulesTemplates()
     *  - InsertProfileRules: Load Routing and Firewall rules using retrieved data from SetRoutingRulesTemplates() SetFirewallRulesTemplates()
     *
     * and set the rules templates to use for them with additionnal settings
     *
     * @param $profile_id: profile ID to create rules for.
     *
     * @return array: It contains return code, and counters about loaded rules.
     */
    public function CreateAccess($profile_id)
    {
        parent::initialize();
        $default_access_data = $this->SetDefaultRulesTemplates($profile_id);

        $access_data = array_merge($this->SetRoutingRulesTemplates($profile_id), $this->SetFirewallRulesTemplates($profile_id));

        if(isset($default_access_data)) {
            $router = new RulesShell();
            $router->CreateProfileChains($profile_id, $default_access_data);
            $router->LoadProfileDefaultRules($profile_id, $default_access_data);

            if(isset($access_data)) {
                $router->InsertProfileRules($profile_id, $access_data);
            }

            $return = $router->GetRulesCount();
        } else {
            $return['rc'] = 1;
        }
        return $return;
    }

    /**
     * This function removes all profiles rules but keeps the profile chains
     * It allow to keep profile chains loaded for devices and users while allowing to remove all rules in the profile chains
     * 
     * It is use called by ProfilesShell before reloading all rules for a profile.
     *
     * @param $profile_id
     * 
     * @return void (but increments counters)
     */
    public function RemoveAccess($profile_id)
    {
        parent::initialize();
        $router = new RulesShell();
        $router->FlushProfileRules($profile_id);
    }

    /**
     * This function completely delete all Rules of a profile including chains
     *  Not used today but it will be used futher by DeamonShell to cleanup unused profiles rules
     *
     * @param $profile_id
     * 
     */
    public function DeleteAccess($profile_id)
    {
        parent::initialize();
        $router = new RulesShell();
        $router->DeleteProfileRules($profile_id);
    }

    /**
     * This function removes and reload all rules of given profile_id
     *
     * @param $profile_id
     * 
     */
    public function ResetAccess($profile_id)
    {
        $this->RemoveAccess($profile_id);
        $this->CreateAccess($profile_id);
    }
}

