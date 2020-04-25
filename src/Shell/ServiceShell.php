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
 * This class is use to stop, start, restart services required bt keexybox
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class ServiceShell extends BoxShell
{

    public function main(){}
    public function startup(){}

    /**
     * This function just display a starting message in the console
     *
     * @param $service : Service name to display (for example: bind, dhcp, tor...)
     * @param $action : Action of service to display (start, stop...)
     *
     */
    private function service_begin($service, $action)
    {
        parent::initialize();
        echo "Keexybox $service $action... ";
    }

    /**
     * This function display the result for service action and exit or return
     *
     * @param $return : Array that contains the return code of the service action command
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return integer: 0 on success, else the service action command failed
     */
    private function service_exit($return, $exit = null)
    {
        parent::initialize();
        if($return['rc'] == 0) {
            echo "\033[32mOK\033[0m\n";
        } else {
            echo "\033[31mFAILED\033[0m\n";
        }

        if(!isset($exit)) {
            exit($return['rc']);
        } else {
            return $return['rc'];
        }
    }

    /**
     * This function start, stop network service
     *  It is not really used, You may have to reboot the box on Network configuration change
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function network($action, $exit = null)
    {
        $this->service_begin(__FUNCTION__, $action);
        $return = $this->RunCmd("$this->bin_sudo $this->network_init $action", 'service');
        $this->service_exit($return, $exit);
    }

    /**
     * This function start, stop bind service
     *  ISC Bind is DNS server used for domains filtering
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function bind($action, $exit = null)
    {
        parent::initialize();
        $this->service_begin(__FUNCTION__, $action);
        $return = $this->RunCmd("$this->bin_sudo $this->bind9_init $action", 'service');
        $this->service_exit($return, $exit);
    }

    /**
     * This function start, stop tor service
     *  Tor allow to connect to Tor network for anonymity
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function tor($action, $exit = null)
    {
        parent::initialize();
        $this->service_begin(__FUNCTION__, $action);
        $return = $this->RunCmd("$this->bin_sudo $this->tor_init $action", 'service');
        $this->service_exit($return, $exit);
    }

    /**
     * This function start, stop rules service
     *  Rules service is an internal service to load or unload Iptables required by Keexybox
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function rules($action, $exit = null)
    {
        parent::initialize();
        $rules = new RulesShell;
        $daemon = new DaemonShell;
        $this->service_begin(__FUNCTION__, $action);
        // Actions on status
        if($action == "status") {
            $return = $rules->$action();
            $this->service_exit($return, $exit);
        // Actions on start
        } elseif($action == "start"){
            $rules->$action();
            $return = $rules->GetRulesCount();

            // This force to rescan devices and update IP from their MAC address
            // but this task delays the startup process. So this is commented.
            // This task is later also started by background task
            //$daemon->UpdateRegistredDevicesIP();

            // Reconnect registred devices
            $daemon->ReconnectRegistred();

            $this->service_exit($return, $exit);
        } elseif($action == "reload") {
            $this->rules('stop', 'no');
            $this->rules('start');

        // Actions on stop
        } else {
            $rules->$action();
            $return = $rules->GetRulesCount();
            $this->service_exit($return, $exit);
        }
    }

    /**
     * This function start, stop apache service
     *  It is the HTTP server used for KeexyApp
     *  It is not really used, You may have to reboot the box on Apache configuration change
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function apache($action, $exit = null)
    {
        parent::initialize();
        $this->service_begin(__FUNCTION__, $action);
        $return = $this->RunCmd("$this->bin_sudo $this->apache_init $action", 'service');
        $this->service_exit($return, $exit);
    }

    /**
     * This function start, stop dhcp service
     *  The start action will only work if DHCP is enabled
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function dhcp($action, $exit = null)
    {
        parent::initialize();
        if($this->dhcp_enabled == false and $action == 'start') {
            $this->service_begin(__FUNCTION__, $action);
            $return = [
                'output' => [__('DHCP service is disabled')],
                'rc' => 1,
                ];
            $this->service_exit($return, $exit);
        } elseif($this->dhcp_enabled == false and $action == 'restart') {
            $this->service_begin(__FUNCTION__, $action);
            $this->RunCmd("$this->bin_sudo $this->dhcp_init stop", 'service');
            $return = [
                'output' => [__('DHCP service is disabled')],
                'rc' => 1,
                ];
            $this->service_exit($return, $exit);
        } else {
            $this->service_begin(__FUNCTION__, $action);
            $return = $this->RunCmd("$this->bin_sudo $this->dhcp_init $action", 'service');
            $this->service_exit($return, $exit);
        }
    }

    /**
     * This function start, stop ntp service
     *  NTP is used to synchronize time on Internet
     *
     * @param $action : Action of service to execute. It can be every actions implemented by the init script
     * @param $exit : if is set, this function will exit instead of return
     *
     * @return void, but run service_exit() to exit or return the command status
     */
    public function ntp($action, $exit = null)
    {
        parent::initialize();
        $this->service_begin(__FUNCTION__, $action);
        $return = $this->RunCmd("$this->bin_sudo $this->ntp_init $action", 'service');
        $this->service_exit($return, $exit);
    }

    /**
     * This function reboot the box
     */
    public function reboot()
    {
        $return = $this->RunCmd("$this->bin_sudo $this->bin_reboot", 'service');
    }

    /**
     * This function poweroff the box
     */
    public function halt()
    {
        $return = $this->RunCmd("$this->bin_sudo $this->bin_halt", 'service');
    }
}
