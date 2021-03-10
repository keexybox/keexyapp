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

require_once(APP .DS. 'Controller' . DS . 'Component' . DS . 'IP4Calc.php');
use Cake\Console\Shell;
use Cake\Core\Configure;
use keexybox\IP4Calc;
use Cake\Log\Log;

/**
 * This class organize rules and submit them to IptablesShell()
 * This class is mainly use by ProfilesShell(), UsersShell(), DevicesShell(), DaemonShell(). 
 * It an interface for Profiles, Users and Devices to load Iptables rules.
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class RulesShell extends BoxShell
{
    private $count_rules = 0;
    private $count_critical_errors = 0;
    private $count_warning_errors = 0;

    /** 
     * This function get counters about number a rules that were loaded, number of warning/critical rules that were not loaded.
     * Any function that loads rules increments these counters. 
     *
     * @return array: contains :
     *  - Total of rules that expected to be loaded
     *  - Total of warning rules that were not loaded
     *  - Total of critical rules that were not loaded
     *  - A return code that gives the severity of errors. 0 = ok, 1 = critical, 2 = warning
     *
     */
    public function GetRulesCount()
    {
        $return = [
            'count_rules' => $this->count_rules,
            'count_critical_errors' => $this->count_critical_errors,
            'count_warning_errors' => $this->count_warning_errors,
            ];

        if($this->count_critical_errors == 0 and $this->count_warning_errors == 0) {
            $return['rc'] = 0;
        }

        if($this->count_critical_errors > 0) {
            $return['rc'] = 1;
        }

        if($this->count_warning_errors > 0) {
            $return['rc'] = 2;
        }

        return $return;
    }

    /**
     * This function Set data structure for iptables chains parameters for routing and filter rules 
     *  It is use by SetRules() to create data structure for iptables before adding rules.  
     *  It is use by (Un)LoadUserRules() (Un)LoadUserRules() 
     *
     * @param $chain_id : Chain Identifier use as prefix for all chains name. It is the profile_id.
     *
     * @return array for each builtin chain available on iptable
     *        name : name of the chain
     *        table : corresponding table of the chain (filter, nat...)
     *        builtin : corresponding builtin chain of the chain (INPUT, OUTPUT, FORWARD, PREROUTING...)
     *        rules : By default it is empty. Rules are added later by SetRules()
     */
    public function SetChains($chain_id) {
        $chains = [
            'NAT_PREROUTING' => ['name' => $chain_id."_PREROUTING", 'table' => 'nat', 'builtin' => 'PREROUTING', 'rules' => []],
            'FILTER_INPUT' => ['name' => $chain_id."_INPUT", 'table' => 'filter', 'builtin' => 'INPUT', 'rules' => []],
            'FILTER_FORWARD' => ['name' => $chain_id."_FORWARD", 'table' => 'filter', 'builtin' => 'FORWARD', 'rules' => []],
            ];
        return $chains;
    }

    /**
     * This function set iptables rules to load for a profile
     *
     * @param $chain_id : Chain Identifier use as prefix for all chains name. It expected to be the profile_id.
     * @param $rules_data : array that contains rule templates to use, and some additionnal settings 
     *    example : [
     *                 (int) 0 => ['rules_template' => 'profile_default_input']
     *                (int) 1 => ['rules_template' => 'profile_default_output']
     *                (int) 2 => ['rules_template' => 'profile_routing_direct', 'dst_ip' => '10.10.1.1']
     *                (int) 3 => ['rules_template' => 'profile_routing_tor', 'dst_ip' => '10.10.1.1']
     *                ]
     *
     * @return array same as SetChains() + rules
     *   See files inside rules_templates for more information
     */
    public function SetRules($chain_id, $rules_data)
    {
        parent::initialize();

        $rules = $this->SetChains($chain_id);

        foreach($rules_data as $rule_data) {
            if(isset($rule_data['rules_template'])) {
                if(file_exists(ROOT .DS. $this->rules_templates . DS . $rule_data['rules_template'] . '_rules.php')) {
                    require(ROOT .DS. $this->rules_templates . DS . $rule_data['rules_template'] . '_rules.php');
                } else {
                    echo(ROOT .DS. $this->rules_templates . DS . $rule_data['rules_template'] . '_rules.php not found');
                }
            }
        }

        return $rules;
    }

    /**
     * This function Set iptables rules to load main Keexybox rules 
     * It is use once when service rules start
     *
     * @param $rules_data array that contains rules templates to use, and additionnal settings for templates
     *   example : [(int) 0 => ['rules_template' => 'init_start', 'out_subnet' => '192.168.1.0/24', 'in_subnet' => '192.168.2.0/24']]
     *
     * @return array for each builtin chain available on iptable
     *        name : name of the chain
     *        table : corresponding table of the chain (filter, nat...)
     *        builtin : corresponding builtin chain of the chain (INPUT, OUTPUT, FORWARD, PREROUTING...)
     *        rules : By default it is empty. Rules are added later by SetRules()
     */
    public function SetStartupRules($rules_data)
    {
        parent::initialize();

        $rules = [
            'NAT_PREROUTING' => ['name' => "PREROUTING", 'table' => 'nat', 'builtin' => 'PREROUTING', 'rules' => []],
            'FILTER_INPUT' => ['name' => "INPUT", 'table' => 'filter', 'builtin' => 'INPUT', 'rules' => []],
            'FILTER_FORWARD' => ['name' => "FORWARD", 'table' => 'filter', 'builtin' => 'FORWARD', 'rules' => []],
            'FILTER_OUTPUT' => ['name' => "OUTPUT", 'table' => 'filter', 'builtin' => 'OUTPUT', 'rules' => []],
            'NAT_POSTROUTING' => ['name' => "POSTROUTING", 'table' => 'nat', 'builtin' => 'POSTROUTING', 'rules' => []],
            ];


        foreach($rules_data as $rule_data) {
            if(isset($rule_data['rules_template'])) {
                if(file_exists(ROOT .DS. $this->rules_templates . DS . $rule_data['rules_template'] . '_rules.php')) {
                    require(ROOT .DS. $this->rules_templates . DS . $rule_data['rules_template'] . '_rules.php');
                } else {
                    echo(ROOT .DS. $this->rules_templates . DS . $rule_data['rules_template'] . '_rules.php not found');
                }
            }
        }

        return $rules;
    }

    /**
     * This function verify if one or more chains exists for the chain identifier
     *
     * @param chain_id : it should be the profile_id
     *
     * @return : return 0 if all chains exists else it return 1
     */
    public function CheckChainsExists($chain_id)
    {
        parent::initialize();

        $iptables = new IptablesShell();
        $chains = $this->SetChains($chain_id);

        $rc = 0;

        foreach($chains as $chain) {
            $rc = $iptables->VerifyChain($chain['table'], $chain['name']);
            if($rc != 0) {
                return(1);
            }
        }
        return(0);
    }

    /**
     * This function is used to check if keexybox rules service is started
     *
     * @return : return 0 if all defualt profile rules are well loaded. Else 1 that mean one or more rules were not loaded
     */
    public function status()
    {
        parent::initialize();

        $iptables = new IptablesShell();

        $oIP = new IP4Calc($this->host_ip_output, $this->host_netmask_output);
        $out_net = $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
        $out_mask_dec = $oIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);
        
        $iIP = new IP4Calc($this->host_ip_input, $this->host_netmask_input);
        $in_net = $iIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
        $in_mask_dec = $iIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);

        $rules_data = [
             ['rules_template' => 'init_start', 'out_subnet' => "$out_net/$out_mask_dec", 'in_subnet' => "$in_net/$in_mask_dec"],
        ];
        $rules_settings = $this->SetStartupRules($rules_data);

        foreach($rules_settings as $rules_setting) {
            foreach($rules_setting['rules'] as $rule) {
                $rc = $iptables->VerifyRule($rules_setting['table'], $rules_setting['name'], $rule);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }

            $this->count_rules++;
            if($rc != 0) {
                $this->count_critical_errors++;
            }
        }
        if($this->count_critical_errors == 0) {
            exit(0);
        } else {
            exit(1);
        }
    }

    /**
     * This function load rules when keexybox rules service start
     *
     * @return void (but increments counters)
     */
    public function start()
    {
        parent::initialize();
        // Activate routing on Linux
        $this->RunCmd("$this->bin_sudo $this->bin_sysctl -w net.ipv4.ip_forward=1");

        $iptables = new IptablesShell();

        $rc = $iptables->Start();

        $oIP = new IP4Calc($this->host_ip_output, $this->host_netmask_output);
        $out_net = $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
        $out_mask_dec = $oIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);
        
        $iIP = new IP4Calc($this->host_ip_input, $this->host_netmask_input);
        $in_net = $iIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
        $in_mask_dec = $iIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);

        if($rc == 0) {
            $rules_data = [
                 ['rules_template' => 'init_start', 'out_subnet' => "$out_net/$out_mask_dec", 'in_subnet' => "$in_net/$in_mask_dec"],
            ];

            $rules_settings = $this->SetStartupRules($rules_data);

            foreach($rules_settings as $rules_setting) {

                //$iptables->CreateChain($rules_setting['table'], $rules_setting['name']);
                foreach($rules_setting['rules'] as $rule) {
                    $rc = $iptables->AddRule($rules_setting['table'], $rules_setting['name'], "$rule");

                    $this->count_rules++;
                    if($rc != 0) {
                        $this->count_critical_errors++;
                    }
                }

                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        } else {
            $this->count_rules++;
            $this->count_critical_errors++;
        }
    }

    /**
     * This function load rules when keexybox rules service stop
     *
     */
    public function stop()
    {
        parent::initialize();
        $iptables = new IptablesShell();
        $rc = $iptables->Stop();
        return $rc;
    }

    /**
     * This function creates new empty chains for a profile
     *
     * @param chain_id : expected to be the profile_id
     *
     * @return void (but increments counters)
     */
    public function CreateProfileChains($chain_id)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $chains = $this->SetChains($chain_id);

        $rc = $this->CheckChainsExists($chain_id);
        if($rc != 0) {
            foreach($chains as $chain) {
                $rc = $iptables->CreateChain($chain['table'], $chain['name']);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function creates iptables chains and add rules for profile.
     * It should be run once when user or device is the first to use the profile when it connect
     *
     * @param chain_id : expected to be the profile_id
     * @param $rules_data : Contains data to use for creating rules
     * 
     * @return void (but increments counters)
     */
    public function LoadProfileDefaultRules($chain_id, $rules_data)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $rules_settings = $this->SetRules($chain_id, $rules_data);

        foreach($rules_settings as $rules_setting) {
            //$iptables->CreateChain($rules_setting['table'], $rules_setting['name']);
            foreach($rules_setting['rules'] as $rule) {
                $rc = $iptables->AddRule($rules_setting['table'], $rules_setting['name'], "$rule");
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function insert rules on the top of profile chains. CreateProfileRules() have to be run once before using this function.
     *  It is use by DaemonShell, 
     *     It could be run every time by keexybox daemon to insert new rules for websites added by admin, or ip discovered by dns lookup
     *
     * @param chain_id : chain identifier 
     * @param $rules_data : Contains data to create rules
     * 
     * @return void (but increments counters)
     */
    public function InsertProfileRules($chain_id, $rules_data, $position = null)
    {
        parent::initialize();

        $iptables = new IptablesShell();

        $rules_settings = $this->SetRules($chain_id, $rules_data);

        foreach($rules_settings as $rules_setting) {
            foreach($rules_setting['rules'] as $rule) {
                if(isset($position)) {
                    $rc = $iptables->InsertRule($rules_setting['table'], $rules_setting['name'], "$rule", $position);
                } else {
                    $rc = $iptables->InsertRule($rules_setting['table'], $rules_setting['name'], "$rule");
                }
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_warning_errors++;
                }
            }
        }
    }

    /**
     * This function removes all profiles rules but keeps the profile chains
     * It allow to keep profile chains loaded for devices and users while allowing to remove all rules in the profile chains
     * 
     * It is use called by ProfilesShell before reloading all rules for a profile.
     *
     * @param chain_id : name of the chain to create
     * 
     * @return void (but increments counters)
     */
    public function FlushProfileRules($chain_id) {
        parent::initialize();
        $iptables = new IptablesShell();
        if(isset($chain_id)) {
            $chains = $this->SetChains($chain_id);
            foreach($chains as $chain) {
                $rc = $iptables->FlushChain($chain['table'], $chain['name']);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function completely delete all Rules of a profile including chains
     *
     * @param chain_id : name of the chain identifier to remove
     * 
     */
    public function DeleteProfileRules($chain_id) {
        parent::initialize();
        $iptables = new IptablesShell();
        if(isset($chain_id)) {
            $chains = $this->SetChains($chain_id);
            foreach($chains as $chain) {
                $rc = $iptables->UnloadAndDeleteChain($chain['table'], $chain['name'], $chain['builtin']);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function remove one or more rules for a profile
     * It could be run every time by keexybox daemon to remove rules for routing access that are not used anymore (dns cache expiration or routing removed by admin)
     *
     * @param chain_id : name of the chain to create
     * @param $rules_data : Contains data to create rules
     * 
     * @return void (but increments counters)
     */
    public function RemoveProfileRules($chain_id, $rules_data)
    {
        parent::initialize();

        $iptables = new IptablesShell();

        $rules_settings = $this->SetRules($chain_id, $rules_data);

        foreach($rules_settings as $rules_setting) {
            foreach($rules_setting['rules'] as $rule) {
                $rc = $iptables->DeleteRule($rules_setting['table'], $rules_setting['name'], "$rule");
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_warning_errors++;
                }
            }
        }
    }

    /**
     * This function remove one or more rules accross rules of all profiles
     * It could be run every time by keexybox daemon to remove rules for routing that are not used anymore (dns cache expiration or website removed by admin)
     *
     * @param $conditions : See IptablesShell() to know how to set conditions
     * 
     * @return void (but increments counters)
     */
    public function RemoveRules($conditions)
    {
        parent::initialize();
        $iptables = new IptablesShell();
        $chains = $this->SetChains($chain_id);

        $tables = [];

        foreach($chains as $chain) {
            $tables[] = $chain['table'];
        }

        $tables = array_unique($tables);

        foreach($tables as $table) {
            $rc = $iptables->FindAndDeleteRules($table, $conditions);
            $this->count_rules++;
            if($rc != 0) {
                $this->count_warning_errors++;
            }
        }
    }

    /**
     * This function load profile rules for given user IP address
     * In case of time conditions, profile rules must be loaded as many times as there are defined time conditions for the profile.
     *
     * @param chain : name of the chain to create
     * @param src_ip : user IP address
     * @param daysofweek : Days of week in Squid format (MTWHFAS)
     * @param timerange : Time range in Squid format (e.g. 08:00-18:00)
     * 
     * @return void (but increments counters)
     */
    public function LoadUserRules($chain_id, $src_ip, $daysofweek=null, $timerange=null)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $chains = $this->SetChains($chain_id);

        // This chains are not use to load access
        unset($chains['FILTER_OUTPUT']);

        if(isset($daysofweek) and isset($timerange)) {
            foreach($chains as $chain) {
                $rc = $iptables->LoadChain($chain['table'], $chain['name'], $chain['builtin'], "-s $src_ip", $daysofweek, $timerange);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        } else {
            foreach($chains as $chain) {
                $rc = $iptables->LoadChain($chain['table'], $chain['name'], $chain['builtin'], "-s $src_ip");
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function unload profile rules for given user IP address
     *
     * @param $chain_id : expected to be the profile_id
     * @param $src_mac : user IP address
     * 
     * @return void (but increments counters)
     */
    public function UnloadUserRules($chain_id, $src_ip)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $chains = $this->SetChains($chain_id);
        foreach($chains as $chain) {
            $rc = $iptables->UnloadChain($chain['table'], $chain['name'], $chain['builtin'], "$src_ip");
            $this->count_rules++;
            if($rc != 0) {
                $this->count_critical_errors++;
            }
        }
    }

    /**
     * This function load profile rules for given MAC Address
     * In case of time contitions, profile rules must be loaded as many times as there are defined time conditions for the profile.
     *
     * @param $chain_id : expected to be the profile_id
     * @param $src_mac : MAC Address of device
     * @param daysofweek : Days of week format (MTWHFAS)
     * @param timerange : Time range in Squid format (e.g. 08:00-18:00)
     * 
     * @return void (but increments counters)
     */
    public function LoadDeviceRules($chain_id, $src_mac, $daysofweek=null, $timerange=null)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $chains = $this->SetChains($chain_id);

        // This iptables built-in chains don't works when source is MAC. 
        unset($chains['FILTER_OUTPUT']);

        if(isset($daysofweek) and isset($timerange)) {
            foreach($chains as $chain) {
                $rc = $iptables->LoadChain($chain['table'], $chain['name'], $chain['builtin'], "-m mac --mac-source $src_mac", $daysofweek, $timerange);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        } else {
            foreach($chains as $chain) {
                $rc = $iptables->LoadChain($chain['table'], $chain['name'], $chain['builtin'], "-m mac --mac-source $src_mac");
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function load profile rules for all devices
     * It is used only if Captive portal is disabled 'cportal_register_allowed' = 3
     * In case of time contitions, profile rules must be loaded as many times as there are defined time conditions for the profile.
     *
     * @param $chain_id : expected to be the profile_id
     * @param daysofweek : Days of week format (MTWHFAS)
     * @param timerange : Time range in Squid format (e.g. 08:00-18:00)
     * 
     * @return void (but increments counters)
     */
    public function LoadDefaultDeviceRules($chain_id, $daysofweek=null, $timerange=null)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $chains = $this->SetChains($chain_id);

        // This iptables built-in chains don't works when source is MAC. 
        unset($chains['FILTER_OUTPUT']);

        if(isset($daysofweek) and isset($timerange)) {
            foreach($chains as $chain) {
                $rc = $iptables->LoadChain($chain['table'], $chain['name'], $chain['builtin'], null, $daysofweek, $timerange);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        } else {
            foreach($chains as $chain) {
                $rc = $iptables->LoadChain($chain['table'], $chain['name'], $chain['builtin'], null);
                $this->count_rules++;
                if($rc != 0) {
                    $this->count_critical_errors++;
                }
            }
        }
    }

    /**
     * This function unload profile rules for given MAC Address
     *
     * @param $chain_id : expected to be the profile_id
     * @param $src_mac : MAC Address of device
     * 
     * @return void (but increments counters)
     */
    public function UnloadDeviceRules($chain_id, $src_mac)
    {
        parent::initialize();
        $iptables = new IptablesShell();

        $chains = $this->SetChains($chain_id);
        foreach($chains as $chain) {
            $rc = $iptables->UnloadChain($chain['table'], $chain['name'], $chain['builtin'], null, "$src_mac");
            $this->count_rules++;
            if($rc != 0) {
                $this->count_critical_errors++;
            }
        }
    }
}
