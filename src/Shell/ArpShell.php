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

/**
 * This class allow to run ARP scan commands
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class ArpShell extends BoxShell
{
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * This function override Cakephp function. Leave it here.
     * It needed to get a clean serialized output for ArpScan. 
     *
     * @return void
     */
    public function startup(){}

    /**
     * This function update OUI file for arp-scan 
     * It should be run as root during keexybox installation
     *
     * @return void
     */
    public function UpdateOui()
    {
        exec($this->bin_getoui);
    }

    /**
     * This function gets subnets of input ans output confugured networks.
     * It is use by others function in this class.
     *
     * @return array id[0]=input subnet and id[1] = ouput subnet
     */
    private function GetSubnets()
    {
        // Retrieve network input IP host settings
        $host_ip_input = $this->Config->get('host_ip_input')->value;
        $host_netmask_input = $this->Config->get('host_netmask_input')->value;
        $hiIP = new IP4Calc($host_ip_input, $host_netmask_input);
        $hiNet = $hiIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
        $hiMaskDec = $hiIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);

        $input_network_mask = "$hiNet/$hiMaskDec";

        // Retrieve network output IP host settings
        $host_ip_output = $this->Config->get('host_ip_output')->value;
        $host_netmask_output = $this->Config->get('host_netmask_output')->value;
        $hoIP = new IP4Calc($host_ip_output, $host_netmask_output);
        $hoNet = $hoIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
        $hoMaskDec = $hoIP->get(IP4Calc::NETMASK, IP4Calc::DECIMAL);

        $output_network_mask = "$hoNet/$hoMaskDec";

        return array($input_network_mask, $output_network_mask);

    }

    /**
     * This function to an ARP scan on input/internal and output networks to detect available devices.
     * It is call when admin scan device from WEBUI (http://keexybox:8001/devices/scan)
     *
     * @return serialized output of found devices
     */
    public function ArpScan()
    {
        //Retrieve network subnets
        $input_network_mask = $this->GetSubnets()[0];
        $output_network_mask = $this->GetSubnets()[1];

        // Run arp-scan and arp commands
        exec("$this->bin_sudo $this->bin_arpscan -O $this->arp_scan_oui_file $input_network_mask | $this->bin_grep -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'", $scan_res1);
        exec("$this->bin_sudo $this->bin_arpscan -O $this->arp_scan_oui_file $output_network_mask | $this->bin_grep -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'", $scan_res2);
        exec("$this->bin_sudo $this->bin_arp -an | $this->bin_grep -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}'", $scan_res3);

        // Merge arp-scan results
        $scan_res = array_merge($scan_res1, $scan_res2);
        
        # var of scan-arp devices
        $devices1 = null;
        if(isset($scan_res))
        {
            foreach($scan_res as $key => $device)
            {    
                $scan_res_split = preg_split('/\s+/', $device);
                $devices1[$key]['ip'] = $scan_res_split[0];
                $devices1[$key]['mac'] = $scan_res_split[1];
                $devices1[$key]['name'] = null;
        
                // Get all value above key 2 to get full device name
                foreach($scan_res_split as $key2 => $scan_res_v) {
                    if($key2 > 1) {
                        $devices1[$key]['name'] = $devices1[$key]['name']." ".$scan_res_v;
                        $devices1[$key]['name'] = trim($devices1[$key]['name']);
                    }
                }
            }
        }
        
        # var of arp devices
        $devices2 = null;
        if(isset($scan_res3))
        {
            foreach($scan_res3 as $key => $device)
            {    
                $scan_res_split = preg_split('/\s+/', $device);
        
                $devices2[$key]['ip'] = str_replace(")","", str_replace('(','',$scan_res_split[1]));
                $devices2[$key]['mac'] = $scan_res_split[3];
                $devices2[$key]['name'] = null;
        
                $mac_search_str=substr(strtoupper(str_replace(":", "", $devices2[$key]['mac'])), 0, 6);
        
                $dev_name = null;
                exec("$this->bin_grep $mac_search_str $this->arp_scan_oui_file", $dev_name);
        
                if(isset($dev_name[0])) {
                    $devices2[$key]['name'] = substr($dev_name[0], 7);
                } else {
                    $devices2[$key]['name'] = __("Unknown");
                }
            }
        }
        $all_devices = array_merge($devices1, $devices2);
        
        $dedup_devices = array();
        foreach ($all_devices as $v) {
            if (isset($dedup_devices[$v['mac']])) {
                // found duplicate
                continue;
            }
            // remember unique item
            $dedup_devices[$v['mac']] = $v;
        }
        
        $dedup_devices = array_values($dedup_devices);
        $s_devices = serialize($dedup_devices);
        print($s_devices);
    }

    /**
     * This function gets IPs addresses from given MAC address
     * It is called when the administrator activates the connection of the device
     * It can also be called by a background task to check if the Ips addresses attached to the MAC address has changed
     *
     * @return array of found IPs
     */
    public function GetMacIps($mac)
    {
        parent::initialize();

        //Retrieve network subnets
        $input_network_mask = $this->GetSubnets()[0];
        $output_network_mask = $this->GetSubnets()[1];

        $mac = strtolower($mac);

        exec("$this->bin_sudo $this->bin_arpscan -O $this->arp_scan_oui_file $input_network_mask --destaddr=$mac| $this->bin_grep $mac", $arp1);
        exec("$this->bin_sudo $this->bin_arpscan -O $this->arp_scan_oui_file $output_network_mask --destaddr=$mac| $this->bin_grep $mac", $arp2);

        $arp = array_merge($arp1,$arp2);

        if(isset($arp)) {
            foreach($arp as $key => $ip)
            {
                $ips[$key] = preg_split('/\s+/', $ip);
            }
              if(isset($ips)) {
                  return $ips;
              }
        }
    }
}
