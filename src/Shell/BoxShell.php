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
use Cake\Datasource\ConnectionManager;

/*
 * This class is the parent class of all Shells
 * It defines a set of properties and functions used by all shells
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class BoxShell extends Shell
{
    public function startup()
    {
    }

    /**
     * This function do the folling actions :
     *  - Load All Models
     *  - Define timezone to use by shells
     *  - Load all Keexybox settings from keexybox.config table
     *  - Load all Keexybox settings hard coded in this function
     *  - Define connection managers for keexybox, keexybox_logs and keexybox_blacklist databases
     * 
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('Config');
        $this->loadModel('Users');
        $this->loadModel('Devices');
        $this->loadModel('Profiles');
        $this->loadModel('ProfilesRouting');
        $this->loadModel('ProfilesTimes');
        $this->loadModel('DnsCache');
        $this->loadModel('ActivesConnections');
        $this->loadModel('ConnectionsHistory');
        $this->loadModel('Routes');
        $this->loadModel('FwRules');
        $this->loadModel('Blacklist');
    
        // Set Shell Time Zone
        $host_timezone = $this->Config->get('host_timezone', ['contain' => []]);
        date_default_timezone_set($host_timezone['value']);
    
        // Load Keexybox config from Database and set variables for child ShellClass
        $dbconfigs = $this->Config->find('all');
        foreach($dbconfigs as $config)
        {
            Configure::write($config['param'], $config['value']);
            $this->{$config['param']} = Configure::read($config['param']);
        }

        // Config set in code for General variables for Keexybox CLI
        // You can override DB values
        $hardconfigs = [
            // Path were configuration template are
            ['conf_templates', 'src/Shell/conf_templates'],
            // Path were iptables rules template are
            ['rules_templates', 'src/Shell/rules_templates'],
            // Path were the configuration files are saved
            ['backupconf_dir', 'src/Shell/backupconf/']
        ];
    
        // Load Keexybox config from $hardconfigs and set variables for child ShellClass
        foreach($hardconfigs as $config)
        {
            Configure::write($config[0], $config[1]);
            $this->{$config['0']} = Configure::read($config['0']);
        }

        $keexybox_source = ConnectionManager::get('default');
        $this->keexybox_db_config = $keexybox_source->config();

        $blacklist_source = ConnectionManager::get('keexyboxblacklist');
        $this->blacklist_db_config = $blacklist_source->config();

        $logs_source = ConnectionManager::get('keexyboxlogs');
        $this->logs_db_config = $logs_source->config();
    }

    /**
     * This function is use to write data to given log file
     * 
     * @return void
     */
    public function kxylog($logfile, $logdata)
    {
        $logfile = $this->keexyboxlogs."/".$logfile.".log";
        if(!file_exists($logfile)) {
             $this->createFile($logfile, null);
        }
        file_put_contents($logfile, date('Y-m-d H:i:s - ').$logdata."\n", FILE_APPEND);
    }

    /**
     * Alias of kxylog()
     * 
     * @return void
     */
    public function LogMessage($message, $logfile) {
        $this->kxylog($logfile, $message);
    }

    /**
     * This function is use by shell to run a command and write result to log file
     * 
     * @return void
     */
    public function RunCmd($cmd, $log = null)
    {
        $return['cmd'] = $cmd;
        exec($cmd." 2>&1", $return['output'], $return['rc']);
        if(isset($log)) {
            $this->kxylog($log, $cmd);
            //$this->dispatchShell('log', 'add', $log, $cmd);
            foreach($return['output'] as $output) {
                $this->kxylog($log, $output);
            }
        }
        return($return);
    }
}
