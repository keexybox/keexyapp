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
use Cake\ORM\TableRegistry;
use Cake\ORM\Entity;
use Cake\Network\Exception\NotFoundException;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;

/**
 * This class provides tools to Keexybox 
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ToolsController extends AppController
{
    /**
     * Show services status and allows to do actions on services
     *
     * @return void
     */
    public function services()
    {
        $services = [
            ['name' => 'bind', 'description' => __('DNS Service for domain filtering')],
            ['name' => 'dhcp', 'description' => __('Service which assigns automatically an IP address on devices')],
            ['name' => 'hostapd', 'description' => __('Service for Wireless Access Point')],
            ['name' => 'ntp', 'description' => __('Service for time synchronization')],
            ['name' => 'rules', 'description' => __('Service for routing and firewall rules')],
            ['name' => 'tor', 'description' => __('Anonymization service')],
            ];

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($this->request->data['action'] == 'reboot') {
                exec($this->kxycmd("service reboot"), $output, $rc);

            } elseif ($this->request->data['action'] == 'halt') {
                exec($this->kxycmd("service halt"), $output, $rc);

            } elseif (isset($this->request->data['check'])) {
                foreach($this->request->data['check'] as $service) {
                    exec($this->kxycmd("service $service ".$this->request->data['action']), $output, $rc);
                }
            } else {
                $this->Flash->warning(__('Nothing was selected.'));
            }
        }

        $services_status = array();
        foreach($services as $service) {
            exec($this->kxycmd("service ".$service['name']." status"), $output, $rc);
            $services_status[] = ['name' => $service['name'], 'description' => $service['description'], 'status' => $rc];
        }
        $this->viewBuilder()->setLayout('adminlte');
        $this->set('services_status', $services_status);
    }

    /**
     * Launch an action on a service
     *
     * @param string $service (bind, tor, ntp...)
     * @param string $action (start, stop, restart...)
     *
     * @return void Redirects to referer
     */
    public function launchService($service, $action)
    {
        $this->autoRender = false;
        $rc = 1;
        exec($this->kxycmd("service $service $action"), $output, $rc);
        if($rc == 0) {
            $this->Flash->success(__("Service {0} {1} successfull.", h($service), h($action)));
        } else {
            $this->Flash->error(__("Service {0} {1} failed.", h($service), h($action)));
        }
        return $this->redirect($this->referer());
    }
    /**
     * This function allow to do a domain disgnostic
     * It used when a domain is blocked by Keexybox that should not
     *
     * @return void
     */
    public function domainIssue()
    {
        // Built categories list with domains counts per category and store in cache
        $categories = null;
        $this->loadComponent('BlacklistCache');
        if(($categories = $this->BlacklistCache->ReadCache('bl_categories_list')) === false) {
            $this->BlacklistCache->WriteCache();
            $categories = $this->BlacklistCache->ReadCache('bl_categories_list');
        }

        $search_domain = null;
        $suggested_domain_to_remove = null;

        if(null !== $this->request->getQuery('domain')) {

            $search_domain = $this->request->getQuery('domain');
            //$dns_results = null;

            $this->loadComponent('Urlparser');
            $parsedurl = $this->Urlparser->Parseurl($search_domain);
            $search_domain = $parsedurl['fqdn'];

            // Requested domain will be checked first
            $domains_to_check_in_bl[] = $search_domain; 

            // Build a list of CNAME records for related $search_domain
            $dns_results = dns_get_record($search_domain, DNS_CNAME);
            while($dns_results != null) {
                foreach($dns_results as $dns_result) {
                    if(isset($dns_result['type']) and $dns_result['type'] == 'CNAME') {
                        $domains_to_check_in_bl[] = $dns_result['target'];
                        $dns_results = dns_get_record($dns_result['target'], DNS_CNAME);
                    }
                }
            }

            $this->loadModel('Blacklist');

            // Build a list of CNAME records that can be in the Blacklist
            if(isset($domains_to_check_in_bl)) {
                foreach($domains_to_check_in_bl as $domain) {
                    // For each domain, checking if it is in BL from ROOT dns name
                    //   for example : check .org then keexybox.com and the www.keexybox.com
                    $split_domain = array_reverse(explode(".", $domain));
                    $nb_sub_domain = count($split_domain);
                    $i = 0;
                    $chk_sub_domain = $split_domain[0];
                    while($i < $nb_sub_domain) {
                        $bl_domain = $this->Blacklist->findByZone($chk_sub_domain)->first();
                        if(isset($bl_domain)) {
                            $suggested_domain_to_remove[] = $bl_domain;
                        }
                        $i++;
                        if($i < $nb_sub_domain) {
                            $chk_sub_domain = $split_domain[$i].".".$chk_sub_domain;
                        }
                    }
                }
            }
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            if(isset($this->request->data['check'])) {

                // ACTION CHANGE CATEGORY 
                if($this->request->data['action'] == 'setcategory') {
                    if($this->request->data['category'] != '' or $this->request->data['newcategory'] != '') {
                        if($this->request->data['newcategory'] == '') {
                            $data = ['category' => $this->request->data['category']];
                        } else {
                            $data = ['category' => $this->request->data['newcategory']];
                        }
    
                        foreach($this->request->data['check'] as $check) {
                            $zone = $this->Blacklist->get($check);
                            $zone = $this->Blacklist->patchEntity($zone, $data);
                            $this->Blacklist->save($zone);
                        }
                    } else {
                        $this->Flash->warning(__('Please define a category'));
                    }
                    return $this->redirect($this->referer());
                }

                // ACTION DELETE
                if($this->request->data['action'] == 'delete') {
                    foreach($this->request->data['check'] as $check) {
                        $zone = $this->Blacklist->get($check);
                        $this->Blacklist->delete($zone);
                    }
                    return $this->redirect($this->referer());
                }
            }
        }

        $this->set('categories', $categories);
        $this->set('search_domain', $search_domain);
        $this->set('bl_domains', $suggested_domain_to_remove);

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * This function show the output of iptables -L -v -n -t nat or -t filter
     *
     * @return void
     */
    public function iptablesStatus()
    {
        $this->loadModel('Config');

        // Get iptables command
        $iptables_cmd = $this->Config->get('bin_iptables')['value'];

        // Retrieve iptables status
        //exec($iptables_cmd." -L -n -v -t filter", $filter_status);
        //exec($iptables_cmd." -L -n -v -t nat", $nat_status);

        $filter_status = shell_exec($iptables_cmd." -L -n -v -t filter");
        $nat_status = shell_exec($iptables_cmd." -L -n -v -t nat");

        // Set to view
        $this->set('filter_status', $filter_status);
        $this->set('nat_status', $nat_status);

        $this->viewBuilder()->setLayout('adminlte');
    }

    /**
     * This function show the system state
     * Shows uptime, memory and storage state...
     *
     * @return void
     */
    public function systemState()
    {
        /////////// MEMORY AND SWAP STATUS /////////
        // Retrieve memory informations from system
        $meminfo = null;
        exec('/bin/cat /proc/meminfo', $memdata);

        // Building an array with memory informations
        foreach($memdata as $info) {
            $expl = explode(" ", preg_replace('!\s+!', ' ', $info));
            $temp_meminfo[str_replace(":", "", $expl[0])] = $expl[1];
        }

        // Keep only required info
        $meminfo['MemTotal'] = $temp_meminfo['MemTotal'];
        $meminfo['MemFree'] = $temp_meminfo['MemFree'];
        $meminfo['Buffers'] = $temp_meminfo['Buffers'];
        $meminfo['Cached'] = $temp_meminfo['Cached'];
        $meminfo['MemUsed'] = $temp_meminfo['MemTotal'] - ($meminfo['MemFree'] + $temp_meminfo['Buffers'] + $temp_meminfo['Cached']);
        $meminfo['SwapTotal'] = $temp_meminfo['SwapTotal'];
        $meminfo['SwapFree'] = $temp_meminfo['SwapFree'];
        $meminfo['SwapUsed'] = $temp_meminfo['SwapTotal'] - $meminfo['SwapFree'];

        $this->set('meminfo', $meminfo);

        //////// LOAD AVERAGE STATUS ////////
        $loadinfo = null;
        $loaddata = null;
        exec('/bin/cat /proc/loadavg' , $loaddata);
        exec('/bin/cat /proc/cpuinfo | /bin/grep processor | /usr/bin/wc -l', $nbcpu);
        if($loaddata != null) {
            $expl_loaddata = explode(" ", preg_replace('!\s+!', ' ', $loaddata[0]));
            $loadinfo['01min'] = $expl_loaddata[0];
            $loadinfo['05min'] = $expl_loaddata[1];
            $loadinfo['15min'] = $expl_loaddata[2];
            $loadinfo['nbcpu'] = $nbcpu[0];
        }

        $this->set('loadinfo', $loadinfo);

        //////// DISK USE STATUS ////////
        $diskinfo = null;
        $diskdata = null;
        exec('/bin/df -m -t ext3 -t ext4 -t vfat 2> /dev/null', $diskdata);
        if($diskdata != null) {
            foreach($diskdata as $k => $disk) {
                if ($k < 1) continue;
                $diskinfo_tmp[] = explode(" ", preg_replace('!\s+!', ' ', $disk));
            }
        }

        foreach($diskinfo_tmp as $info) {
            $diskinfo[] = [
                'mount' => $info[5], 
                'total' => $info[1], 
                'used' => $info[2], 
                'free' => $info[3], 
                'percent_used' => str_replace('%', '', $info[4]),
                'percent_free' => 100 - str_replace('%', '', $info[4]),
            ];
        }
        $this->set('diskinfo', $diskinfo);

        $this->viewBuilder()->setLayout('adminlte');
    }
    /**
     * This function allow admin to check updates and install them
     *
     * @return void
     */
    public function update()
    {
        $update_data = null;
        $this->set('uptodate', null);

        if ( $this->request->getQuery('check_update') == 1) {
            //debug($this->Config->get('update_check_url')->value);
            //debug($this->Config->get('version')->value);
            $update_url = $this->Config->get('update_check_url')->value.$this->Config->get('version')->value;
            $update_data = json_decode(file_get_contents($update_url));
            if (isset($update_data)) {
                $this->set('update_data', $update_data);
            } else {
                $this->set('uptodate', true);
            }
        }
        $this->viewBuilder()->setLayout('adminlte');
    }
}
?>
