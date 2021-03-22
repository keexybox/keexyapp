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
 * This class generates the configuration files
 *
 * @author Benoit SAGLIETTO <bsaglietto[AT]keexybox.org>
 *
 */
class ConfigShell extends BoxShell
{
    /**
     * This function override Cakephp function. Leave it here.
     * It needed to get a clean serialized output for ArpScan. 
     *
     * @return void
     */
    public function startup(){}

    public function keexyboxVersion() {
        $keexybox_ver_script = $this->scripts_dir . 'keexybox-version.sh';
        exec($keexybox_ver_script, $keexybox_version);
        $this->out($keexybox_version[0]);
        
    }

    /**
     * BEGIN OF BASIC FUNCTIONS TO MANAGE CONFIGURATION FILES
     */

    /**
     * This function make a backup of a configuration file
     *
     * @param $conffile: configuration file to save
     *
     * @return void
     *
     */
    private function BackupConf($conffile)
    {
        $max_backup = 7;
        if(file_exists($conffile)) {
            $filename = explode("/", $conffile);
            $filename = end($filename);
            $backupid = $max_backup;
            if(file_exists($this->backupconf_dir."/".$filename."_".$backupid)) {
                unlink($this->backupconf_dir."/".$filename."_".$backupid);
            }
                $backupid--;
                while($backupid != 0) {
                    if(file_exists($this->backupconf_dir."/".$filename."_".$backupid)) {
                    $newbackupid = $backupid + 1;
                    copy($this->backupconf_dir."/".$filename."_".$backupid, $this->backupconf_dir."/".$filename."_".$newbackupid);
                }
                    $backupid--;
                }
                $this->out("Copying $conffile to ".$this->backupconf_dir."/".$filename."_1");
                copy($conffile, $this->backupconf_dir."/".$filename."_1");
            } else {
            $this->out("no such conf file : $conffile");
        }
    }

    /**
       * This function restore a configuration file
     *
     * @param $conffile: configuration file
     * @param $version: version of file to restore
     *
     * @return void
     *
     */
    public function RestoreConf($conffile, $version)
    {
        $filename = explode("/", $conffile);
        $filename = end($filename);
        if(file_exists($this->backupconf_dir."/".$filename."_".$version)) {
            $this->out("Restoring ".$this->backupconf_dir."/".$filename."_".$current_date." to ".$conffile);
            copy($this->backupconf_dir."/".$filename."_".$version, $conffile);
        } else {
            $this->out("no such file : ".$this->backupconf_dir."/".$filename."_".$version);
        }
    }

    /**
       * This function List available backups that can be restored
     *
     * @param $conffile: configuration file
     *
     * @return void
     *
     */
    public function ListBackupConf($conffile = null)
    {
        if(isset($conffile)) {
            $filename = explode("/", $conffile);
            $filename = end($filename);
            //$backups = scandir($this->backupconf_dir);
            foreach(glob($this->backupconf_dir."/".$filename."*") as $file)
            {
                $file = explode("/", $file);
                $file = end($file);
                $backups[] = $file;
            }
            print_r($backups);
            return $backups;
        } else {
            $backups = scandir($this->backupconf_dir);
            unset($backups[0]);
            unset($backups[1]);
            print_r($backups);
            return $backups;
        }
    }

    /**
     * This function backup and reset configuration.
     * It delete all data in the configuration file 
     * and just add header that warn the configuration file was generated automatically
     *
     * @param $conffile: configuration file to reset
     * @param $comment: characters to use for comments 
     *
     * @return void
     *
     */
    private function ResetConf($conffile, $comment = null)
    {
        if($comment == null) {
            $comment = "#";
        }

        if(!file_exists($conffile)) {
            //$this->out("Backing Up config file $conffile");
            $this->BackupConf($conffile);
            //$this->out("Creating config file $conffile");
            $this->createFile($conffile, "$comment GENERATED BY KEEXYBOX WEBUI\n$comment DO NOT EDIT THIS FILE\n\n");
        } else {
            //$this->out("Resetting config file $conffile");
            file_put_contents($conffile, "$comment GENERATED BY KEEXYBOX WEBUI\n$comment DO NOT EDIT THIS FILE\n\n");
        }
    }

    /**
     * Add configuration for applications needed by keexybox
     *
     * @param $template: is the file to use and located in src/Shell/conf_templates
     * @param $conffile: configuration file to write
     * @param $params (Array) : Additionnal settings for the configuration file
     *
     * @return 0 on success, 1 on error
     *
     */
    private function AddConf($template, $conffile, $params = null)
    {
        require(ROOT .DS. $this->conf_templates . DS . $template);
        
        // Uncomment for debug
        //$this->out("---- Adding below content in $conffile ----");
        //$this->out("");
        //$this->out($conf_data);

        if(file_put_contents($conffile, $conf_data, FILE_APPEND)) {
            $rc = 0;
        } else {
            $rc = 1;
        }
        return $rc;
    }

    /**
     * This function backup configuration file and removes given pattern from configuration
     *
     * @param $template: file located in Shell/conf_templates
     * @param $pattern: string to remove
     *
     */
    private function RemoveConf($conffile, $pattern)
    {
        $data = file($conffile);

        $this->BackupConf($conffile);
        $this->out("---- Removing below content from $conffile ----");
        foreach( $data as $key => $line ) {
            if (strpos($line, $pattern) !== false) {
                echo "$line";
                unset($data[$key]);
            }
        }
        file_put_contents($conffile, $data);
    }

    /**
     * END OF BASIC FUNCTIONS TO MANAGE CONFIGURATION FILES
     */

    /**
     * BEGIN OF FUNCTIONS THAT GENERATE THE APPLICATION CONFIGURATION FILES
     */

    /**
     * This function generate configuration files for Apache2
     *
     * @param $part: Part of configuration file to generate
     *  - ports: part that contains port configuration for Apache2
     *  - vhosts: file that contains all website configurations
     *  - envvars: part that contains environement variable for apache2
     *  - all: generate all above parts 
     *
     * @param $exit: exit the script or just return.
     *  use $exit = false if you need to generate more configuration files
     *
     * @return or @exit number: 0 for success else it is an error
     * 
     */
    public function apache($part, $exit = true) {
        if(isset($part)) {
            if($part == 'all') {
                $rc = 0;
                $rc = $rc + $this->apache('ports', false);
                $rc = $rc + $this->apache('vhosts', false);
                $rc = $rc + $this->apache('envvars', false);
                exit($rc);
            }
            if($part == 'ports') {
                $this->ResetConf($this->apache_ports_conf_file);
                $rc = $this->AddConf("apache_ports_conf.php", $this->apache_ports_conf_file);
            }
            if($part == 'vhosts') {
                $cportal_test_domains = explode(',', $this->cportal_test_domains);
                $params['cportal_test_domains'] = null;
                foreach ($cportal_test_domains as $cportal_test_domain) {
                    $params['cportal_test_domains'] .= "    ServerAlias $cportal_test_domain\n";
                }
                $this->ResetConf($this->apache_vhosts_conf_file);
                $rc = $this->AddConf("apache_vhosts_conf.php", $this->apache_vhosts_conf_file, $params);
            }
            if($part == 'envvars') {
                $this->ResetConf($this->apache_envvars_conf_file);
                $rc = $this->AddConf("apache_envvars_conf.php", $this->apache_envvars_conf_file);
            }

            if($exit == true) {
                exit($rc);
            } else {
                return($rc);
            }
        }
    }

    /**
     * This function generate configuration files for Logrotate
     *
     * @param $part: Part of configuration file to generate
     *  - main/all: log rotation definition for all keexybox logs
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function logrotate($part) {
        if(isset($part)) {
            if($part == 'main' or $part == 'all') {
                // Log rotate have to be writable by group to allow the change
                exec("$this->bin_sudo chmod 664 $this->logrotate_conf_file");
                $this->ResetConf($this->logrotate_conf_file);
                $rc = $this->AddConf("logrotate_conf.php", $this->logrotate_conf_file);
                // Log rotate have to be read only by group and others to work
                exec("$this->bin_sudo chmod 644 $this->logrotate_conf_file");
            }
            exit($rc);
        }
    }

    /**
     * This function generate configuration files for sudoers file
     *  It only can be call as root during Keexybox installation
     *
     * @param $part: Part of configuration file to generate
     *  - main: define include dir to main sudoers file
     *  - keexybox: generate sudo command definition for keexybox
     *  - all: generate all above parts 
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function sudoers($part) {
        if(isset($part)) {
            $rc = null;

            if($part == 'main' or $part == 'all') {
                $str_to_check = "^#includedir /etc/sudoers.d/";
                $strl = strlen($str_to_check) - 1;
                passthru("$this->bin_grep \"$str_to_check\" $this->sudoers_conf_file", $return_var);
                if($return_var != 0) {
                    $nb_written_char = file_put_contents($this->sudoers_conf_file, "#includedir /etc/sudoers.d/", FILE_APPEND);
                    if($nb_written_char == $strl) {
                        $rc = 0;
                    } else {
                        $rc = 1;
                    }
                } else {
                    $rc = 0;
                }
            }
            if($part == 'keexybox' or $part == 'all') {
                $this->ResetConf($this->sudoers_keexybox_conf_file);
                $rc = $this->AddConf("sudoers_keexybox_conf.php", $this->sudoers_keexybox_conf_file);
            }

            exit($rc);
        }
    }

    /**
     * This function generate network configuration file
     *
     * @param $part: Part of configuration file to generate
     *  - main/all: define include dir to main sudoers file
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function network($part)
    {
        if(isset($part)) {
            $rc = 0;
            if($part == 'main' or $part == 'all') {
                $this->ResetConf($this->network_conffile);

                // Detect wireless devices and set param
                $params['wpa_config_in'] = null;
                if(is_dir($this->nic_path."/".$this->host_interface_input."/wireless")) {
                    $params['wpa_config_in'] = "wpa-conf $this->wpa_config_file";
                }

                $params['wpa_config_out'] = null;
                if(is_dir($this->nic_path."/".$this->host_interface_output."/wireless")) {
                    $params['wpa_config_out'] = "wpa-conf $this->wpa_config_file";
                }

                // Additionnal setting if hostapd is enabled
                $params['bridge_ports'] = null;
                $params['bridge_stp'] = null;
                $params['bridge_waitport'] = null;
                $params['bridge_waitport'] = null;
                if($this->hostapd_enabled == 1) {
                    $params['bridge_ports'] = "bridge_ports $this->hostapd_bridge_ports";
                    $params['bridge_stp'] = "bridge_stp off";
                    $params['bridge_waitport'] = "bridge_waitport 0";
                    $params['bridge_fd'] = "bridge_fd 0";
                }

                $rc = $rc + $this->AddConf("network_conf.php", $this->network_conffile, $params);
            }
            exit($rc);
        }
    }

    /**
     * This function generate configuration file for tor
     *
     * @param $part: Part of configuration file to generate
     *  - main/all: generate main tor configuration file
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function tor($part)
    {
        if(isset($part)) {
            if($part == 'main' or $part == 'all') {
                $params['ExitNodes'] = null;
                $params['StrictNodes'] = null;
                if ("" != $this->tor_exitnodes_countries) {
                    $params['ExitNodes'] = "ExitNodes ".$this->tor_exitnodes_countries;
                    $params['StrictNodes'] = "StrictNodes 1";
                }

                $this->ResetConf($this->tor_conffile);
                $rc = $this->AddConf("tor_conf.php", $this->tor_conffile, $params);
            }
            exit($rc);
        }
    }

    /**
     * This function generate configuration file for hostapd
     *
     * @param $part: Part of configuration file to generate
     *  - main/all: generate main tor configuration file
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function hostapd($part)
    {
        if(isset($part)) {
            if($part == 'main' or $part == 'all') {
                $params['auth_algs'] = null;
                $params['wpa'] = null;
                $params['wpa_key_mgmt'] = null;
                $params['wpa_pairwise'] = null;
                $params['rsn_pairwise'] = null;
                $params['wpa_passphrase'] = null;
                if ($this->hostapd_auth_algs == 1) {
                    $params['auth_algs'] = "auth_algs=".$this->hostapd_auth_algs;
                    $params['wpa'] = "wpa=".$this->hostapd_wpa;
                    $params['wpa_key_mgmt'] = "wpa_key_mgmt=".$this->hostapd_wpa_key_mgmt;
                    $params['wpa_pairwise'] = "wpa_pairwise=".$this->hostapd_wpa_pairwise;
                    $params['rsn_pairwise'] = "rsn_pairwise=".$this->hostapd_rsn_pairwise;
                    $params['wpa_passphrase'] = "wpa_passphrase=".$this->hostapd_wpa_passphrase;
                }
                $this->ResetConf($this->hostapd_conf_file);
                $rc = $this->AddConf("hostapd_conf.php", $this->hostapd_conf_file, $params);
            }
            exit($rc);
        }
    }

    /**
     * This function generate configuration files for ISC Bind9
     *
     * @param $part: Part of configuration file to generate
     *  - named: generate main bind configuration files
     *  - set_acl: Create ACL file for each profiles that exist
     *  - update_acl: Update the ACL file, it contains authorized IP
     *  - set_logging: logging definition part of bind9
     *  - set_default_zone: default keexybox DNS zone file
     *  - set_safesearch: safesearch definition
     *  - set_profiles: define includes of profile configurations and create DNS views for each profile
     *  - update_profile_view: Update profile DNS view for given $profile_id
     *  - remove_profile_view: Remove profile DNS view for given $profile_id
     *  - remove_profile: remove include definition for a profile for given $profile_id
     *  - remove_profile_acl: Remove ACL for given $profile_id
     *  - all: generate named, set_acl, set_logging, set_safesearch, set_default_zone and set_profiles configuration files.
     *
     * @param $exit: exit the script or just return.
     *  use $exit = false if you need to generate more configuration files
     *
     * @return or @exit number: 
     *  - 0 for success, 
     *  - 100 if bind need to be reloaded,
     *  - 255 if profile_id is wrong or not given,
     *  - others errors
     * 
     */
    public function bind($part, $profile_id = null, $exit = true)
    {
        parent::initialize();
        $rc = 0;
        if(isset($part)) {
            if($part == 'all') {
                $rc = 0;
                $rc = $rc + $this->bind('named', null, false);
                $rc = $rc + $this->bind('set_acl' , null, false);
                $rc = $rc + $this->bind('set_logging', null, false);
                $rc = $rc + $this->bind('set_safesearch', null, false);
                $rc = $rc + $this->bind('set_default_zone', null, false);
                $rc = $rc + $this->bind('set_profiles', null, false);
                exit($rc);
            }
            if($part == 'named') {
                $rc = 0;
                // Set common named.conf and named_nolog.cong configs
                if($this->bind_use_redirectors == true) {
                    $forwarders_dns_string = null;
                    foreach([$this->host_dns1, $this->host_dns2] as $dns) {
                        if(!empty($dns) and $dns != '' and $dns != '127.0.0.1' and $dns != $this->host_ip_output and $dns != $this->host_ip_input) {
                            $forwarders_dns_string .= "$dns; ";
                        }
                    }

                    if($forwarders_dns_string != null) {
                        $params['redirectors'] = "forwarders { $forwarders_dns_string};";
                    } else {
                        $params['redirectors'] = null;
                    }
                } else {
                    $params['redirectors'] = null;
                }

                // Set named.conf
                $params['named_port'] = $this->named_port;
                $params['rndc_port'] = $this->rndc_port;
                $params['logging'] = "include \"$this->bind_root_dir/etc/conf.d/logging.conf\";";
                $params['pidfile'] = 'named.pid';
                $this->ResetConf($this->bind_root_dir."/etc/named.conf", '//');
                $rc = $rc + $this->AddConf("bind_named_conf.php", $this->bind_root_dir."/etc/named.conf", $params);

                // Set named_nolog.conf
                $params['named_port'] = $this->named_port_nolog;
                $params['rndc_port'] = $this->rndc_port_nolog;
                $params['logging'] = "include \"$this->bind_root_dir/etc/conf.d/nologging.conf\";";
                $params['pidfile'] = 'named_nolog.pid';
                $this->ResetConf($this->bind_root_dir."/etc/named_nolog.conf", '//');
                $rc = $rc + $this->AddConf("bind_named_conf.php", $this->bind_root_dir."/etc/named_nolog.conf", $params);

                // Set named_tor.conf
                $params['named_port'] = $this->named_port_tor;
                $params['rndc_port'] = $this->rndc_port_tor;
                $params['logging'] = "include \"$this->bind_root_dir/etc/conf.d/logging.conf\";";
                $params['pidfile'] = 'named_tor.pid';
                $params['redirectors'] = "forwarders { 127.0.0.1 port $this->tor_dns_port;};";
                $this->ResetConf($this->bind_root_dir."/etc/named_tor.conf", '//');
                $rc = $rc + $this->AddConf("bind_named_conf.php", $this->bind_root_dir."/etc/named_tor.conf", $params);

                // Set named_tor_nolog.conf
                $params['named_port'] = $this->named_port_tor_nolog;
                $params['rndc_port'] = $this->rndc_port_tor_nolog;
                $params['logging'] = "include \"$this->bind_root_dir/etc/conf.d/nologging.conf\";";
                $params['pidfile'] = 'named_tor_nolog.pid';
                $params['redirectors'] = "forwarders { 127.0.0.1 port $this->tor_dns_port;};";
                $this->ResetConf($this->bind_root_dir."/etc/named_tor_nolog.conf", '//');
                $rc = $rc + $this->AddConf("bind_named_conf.php", $this->bind_root_dir."/etc/named_tor_nolog.conf", $params);

                // Set named_portal.conf
                $params['named_port'] = $this->named_port_portal;
                $params['rndc_port'] = $this->rndc_port_portal;
                $params['logging'] = null;
                $params['pidfile'] = 'named_portal.pid';
                $cportal_test_domains = explode(',', $this->cportal_test_domains);
                $params['cportal_test_domains'] = null;
                foreach ($cportal_test_domains as $cportal_test_domain) {
                    $params['cportal_test_domains'] .= "zone \"$cportal_test_domain\" {type master; file \"catchall.zone\"; allow-query { any; };};\n";
                }
                $this->ResetConf($this->bind_root_dir."/etc/named_portal.conf", '//');
                $rc = $rc + $this->AddConf("bind_named_portal_conf.php", $this->bind_root_dir."/etc/named_portal.conf", $params);
            }
            // Update ACL file and return special return code 100 to tell that bind need to be reloaded
            if($part == 'update_acl') {
                $profiles = $this->Profiles->find('all');
                $acl_files = null;
                foreach($profiles as $profile) {
                    $acl_files .= file_get_contents($this->bind_root_dir."/etc/conf.d/acl_profile_".$profile['id'].".conf");
                }
                $hash_before_update = hash('md5', $acl_files);

                $rc = $this->bind('set_acl', null, $exit = false);

                $acl_files = null;
                foreach($profiles as $profile) {
                    $acl_files .= file_get_contents($this->bind_root_dir."/etc/conf.d/acl_profile_".$profile['id'].".conf");
                }
                $hash_after_update = hash('md5', $acl_files);

                if($rc == 0 and $hash_before_update != $hash_after_update) {
                    $rc = 100;
                }
            }
            if($part == 'set_acl') {
                // Create ACL file for each profiles that exist
                $rc = 0;
                $profiles = $this->Profiles->find('all');

                foreach($profiles as $profile) {
                    $profile_id = $profile['id'];
                    $params['ips'] = null;
                    $params['profile_id'] = $profile['id'];
                    $this->ResetConf($this->bind_root_dir."/etc/conf.d/acl_profile_".$profile_id.".conf", '//');
                    $rc = $rc + $this->AddConf("bind_acl_profile_conf.php", $this->bind_root_dir."/etc/conf.d/acl_profile_".$profile_id.".conf", $params);
                }

                // Replace ACL file for each profiles that exist
                $actives_connecions = $this->ActivesConnections->find('all');

                // By default localhost DNS resolv map to profile ID 1
                $acl[1][] = "127.0.0.1/32";

                // If internet access conditions is set to "None" allows all devices using the DNS 
                if ( $this->cportal_register_allowed == 3 ) {
                      $acl[$this->cportal_default_profile_id][] = "0.0.0.0/0";
                }       

                foreach($actives_connecions as $active_connecion) {
                    $acl[$active_connecion['profile_id']][] = $active_connecion['ip']."/32";
                }

                if(isset($acl)) {
                    foreach($acl as $profile_id => $IPs) {
                        foreach($IPs as $ip) {
                            $params['ips'] .= "\t".$ip.";\n";
                        }

                        $params['profile_id'] = $profile_id;
                        $this->ResetConf($this->bind_root_dir."/etc/conf.d/acl_profile_".$profile_id.".conf", '//');
                        $rc = $rc + $this->AddConf("bind_acl_profile_conf.php", $this->bind_root_dir."/etc/conf.d/acl_profile_".$profile_id.".conf", $params);
                        // Reset values for next profile iteration - fix bug #11
                        $params['ips'] = null;
                    }
                }
            }
            if($part == 'set_logging') {
                $this->ResetConf($this->bind_root_dir."/etc/conf.d/logging.conf", '//');
                $rc = $this->AddConf("bind_logging_conf.php", $this->bind_root_dir."/etc/conf.d/logging.conf");
                $this->ResetConf($this->bind_root_dir."/etc/conf.d/nologging.conf", '//');
                $rc = $this->AddConf("bind_nologging_conf.php", $this->bind_root_dir."/etc/conf.d/nologging.conf");
            }

            if($part == 'set_default_zone') {
                $this->ResetConf($this->bind_root_dir."/etc/zones/keexybox.zone", ';;');
                $rc1 = $this->AddConf("bind_keexybox_zone_conf.php", $this->bind_root_dir."/etc/zones/keexybox.zone");

                $this->ResetConf($this->bind_root_dir."/etc/zones/catchall.zone", ';;');
                $rc2 = $this->AddConf("bind_catchall_zone_conf.php", $this->bind_root_dir."/etc/zones/catchall.zone");

                $this->ResetConf($this->bind_root_dir."/etc/zones/doh.zone", ';;');
                $rc3 = $this->AddConf("bind_doh_zone_conf.php", $this->bind_root_dir."/etc/zones/doh.zone");
                $rc = $rc1 + $rc2 + $rc3;
            }
            if($part == 'set_safesearch') {

                $rc = 0;

                $safesearch_domains = [
                    'google' => 'forcesafesearch.google.com',
                    'bing' => 'strict.bing.com',
                    'youtube' => 'restrict.youtube.com',
                    ];

                foreach($safesearch_domains as $safesearch_param => $safesearch_domain) {

                    $this->ResetConf($this->bind_root_dir."/etc/conf.d/safesearch_$safesearch_param.conf", '//');
                    $rc = $rc + $this->AddConf("bind_safesearch_".$safesearch_param."_conf.php", $this->bind_root_dir."/etc/conf.d/safesearch_$safesearch_param.conf");

                    $safesearch_ips = gethostbynamel($safesearch_domain);
                    $params['records'] = null;
                    foreach($safesearch_ips as $ip) {
                        $params['records'] .= "@        IN    A    $ip\n";
                    }
                    $this->ResetConf($this->bind_root_dir."/etc/zones/safesearch_$safesearch_param.zone", ';;');
                    $rc = $rc + $this->AddConf("bind_safesearch_zone.php", $this->bind_root_dir."/etc/zones/safesearch_$safesearch_param.zone", $params);
                }
            }

            if($part == 'set_profiles') {
                // Set all bind conf for all profile in database
                $rc = 0;
                $profiles = $this->Profiles->find('all');
        
                $this->ResetConf($this->bind_root_dir."/etc/conf.d/profiles.conf", '//');
        
                // The default profile set for captive portal have to be set at the bottom of bind config file (lowest priority for bind ACLs)
                $last_params = null;
                foreach ($profiles as $profile) {
                    if($this->cportal_default_profile_id == $profile['id']) {
                        $last_params['profile_id'] = $profile['id'];
                    } else {
                        $params['profile_id'] = $profile['id'];
                        $this->bind('update_profile_view', $profile['id'], false);
                        $rc = $rc + $this->AddConf("bind_profiles_conf.php", $this->bind_root_dir."/etc/conf.d/profiles.conf", $params);
                    }
                }
                if ($last_params != null) {
                    $this->bind('update_profile_view', $profile['id'], false);
                    $rc = $rc + $this->AddConf("bind_profiles_conf.php", $this->bind_root_dir."/etc/conf.d/profiles.conf", $last_params);
                }
            }

            if($part == 'update_profile_view') {
                if(isset($profile_id)) {
                    $profile = $this->Profiles->get($profile_id, ['contain' => ['ProfilesBlacklists']]);
                    if(isset($profile['id'])) {
                        //AND (category = 'adult' OR category = 'astrology')
                        $params['profile_id'] = $profile['id'];
                        // DNS filter need at least one category for query wich is "None"
                        $category_search_string = " AND (category = 'None' OR ";

                        //$blacklists = explode(",", $profile['blacklists']);
                        $blacklists = $profile->profiles_blacklists;

                        if($blacklists != '') {
                            foreach($blacklists as $blacklist) {
                                $category_search_string .= "category = '".$blacklist['category']."' OR ";
                            }
                        }
                        // Remove last OR and close with )
                        $params['category_search_string'] = preg_replace('/ OR $/', ')', $category_search_string);

                        $safesearch_domains = [
                            'google' => 'forcesafesearch.google.com',
                            'bing' => 'strict.bing.com',
                            'youtube' => 'restrict.youtube.com',
                            ];

                        $params['safesearch'] = null;
                        foreach($safesearch_domains as $safesearch_param => $safesearch_domain) {
                            if($profile['safesearch_'.$safesearch_param] == true) {
                                $params['safesearch'] .= "include \"".$this->bind_root_dir."/etc/conf.d/safesearch_$safesearch_param.conf\";\n";
                            }
                        }

                        $this->ResetConf($this->bind_root_dir."/etc/conf.d/view_profile_".$profile['id'].".conf", '//');
                        $rc = $this->AddConf("bind_view_profile_conf.php", $this->bind_root_dir."/etc/conf.d/view_profile_".$profile['id'].".conf", $params);
                    } else {
                        $rc = 255;
                        $this->out("No profile match $profile_id !");
                    }
                } else {
                    $rc = 255;
                    $this->out("profile_id required !");
                }
            }
            if($part == 'remove_profile_view') {
                if(isset($profile_id)) {
                    unlink($this->bind_root_dir."/etc/conf.d/view_profile_".$profile_id.".conf");
                } else {
                    $rc = 255;
                    $this->out("profile_id required !");
                }
            }
            if($part == 'remove_profile_acl') {
                if(isset($profile_id)) {
                    unlink($this->bind_root_dir."/etc/conf.d/acl_profile_".$profile_id.".conf");
                } else {
                    $rc = 255;
                    $this->out("profile_id required !");
                }
            }
            if($part == 'remove_profile') {
                if(isset($profile_id)) {
                    $rc = 0;
                    $rc = $rc + $this->bind('remove_profile_view', $profile_id, false);
                    $rc = $rc + $this->bind('remove_profile_acl', $profile_id, false);
                    $rc = $rc + $this->bind('set_profiles', null, false);
                    $rc = $rc + $this->bind('update_acl',null, false);
                    exit($rc);
                } else {
                    $rc = 255;
                    $this->out("profile_id required !");
                }

            }
            if($exit == true) {
                exit($rc);
            } else {
                return($rc);
            }
        }
    }

    /**
     * This function generate configuration file for python scripts
     *  Keexybox use python scripts to import or exports logs or blacklist
     *
     * @param $part: Part of configuration file to generate
     *  - pyhton/all: generate configuration file for python scripts
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function scripts($part, $exit = true) {
        parent::initialize();
        $rc = 0;
        if(isset($part)) {
            if($part == 'all') {
                $rc = $this->scripts('python', $exit);
            }
            if($part == 'python') {
                $this->ResetConf($this->scripts_dir . "/config.py");
                $rc = $this->AddConf("py_scripts_conf.php", $this->scripts_dir . "/config.py");
            }
        }
        if($exit == true) {
            exit($rc);
        } else {
            return($rc);
        }
    }

    /**
     * This function generate configuration file for NTP
     *
     * @param $part: Part of configuration file to generate
     *  - main/all: generate main NTP configuration file
     *
     * @exit number: 0 for success else it is an error
     * 
     */
    public function ntp($part)
    {
        if(isset($part)) {
            if($part == 'main' or $part == 'all') {
                $this->ResetConf($this->ntp_conffile);
                $rc = $this->AddConf("ntp_conf.php", $this->ntp_conffile);
            }
            exit($rc);
        }
    }

    /**
     * This function set the system date.
     * The time to set must be for UTC only
     * 
     * @param $date : format must be YYYY-MM-DD, example: 2019-02-28
     * @param $time : format must be h:mm:ss, example: 11:55:45
     *
     */
    public function setDateTime ($date, $time)
    {
        $date_array = explode('-', $date);
        $year = $date_array[0];
        $month = $date_array[1];
        $day = $date_array[2];

        // Set date and time to system
        //passthru("$this->bin_sudo $this->bin_date -s '$month/$day/$year $time'");
        $return = $this->RunCmd("$this->bin_sudo $this->bin_date -s '$month/$day/$year $time'", 'service');
        exit($return['rc']);

        // Sync system clock to hardware clock
        //passthru("$this->bin_hwclock --systohc");
    }

    /**
     * This function generate configuration files for ISC DHCPD
     *  Configuration files are generated only if DHCP is enabled
     *
     * @param $part: Part of configuration file to generate
     *  - main: main DHCP configuration. Configurations may be different depend of network topology choosen.
     *  - reservations: file that contains IP reservations for devices. Configurations may be different depend of network topology choosen.
     *  - all: generate all above parts 
     *
     * @param $exit: exit the script or just return.
     *  use $exit = false if you need to generate more configuration files
     *
     * @return or @exit number: 0 for success else it is an error
     * 
     */
    public function dhcp($part, $exit = true)
    {
        if($this->dhcp_enabled == true) {
            if(isset($part)) {
    
                // Get and set subnet of output interface
                $oIP = new IP4Calc($this->host_ip_output, $this->host_netmask_output);
                $params['dhcp_subnet_output'] = $oIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
    
                // Get and set subnet of input interface
                $iIP = new IP4Calc($this->host_ip_input, $this->host_netmask_input);
                $params['dhcp_subnet_input'] = $iIP->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
    
                if($part == 'all') {
                    $rc = 0;
                    $rc = $rc + $this->dhcp('main', false);
                    $rc = $rc + $this->dhcp('reservations', false);
                    exit($rc);
                }
    
                if($part == 'main') {
                    if ($this->dhcp_external == true) {
                        $this->ResetConf($this->dhcp_conffile);
                        $rc = $this->AddConf("dhcpd_single_conf.php", $this->dhcp_conffile, $params);
    
                    } else {
                        $this->ResetConf($this->dhcp_conffile);
                        $rc = $this->AddConf("dhcpd_dual_conf.php", $this->dhcp_conffile, $params);
                    }
                }
    
                if($part == 'reservations') {
                    if ($this->dhcp_external == true) {
                        $rc = 0;
                        $this->ResetConf($this->dhcp_reservations_conffile);
                        $devices = $this->Devices->find('all', ['conditions' => ['not' => ['dhcp_reservation_ip' => '']]]);
                        //$devices = $this->Devices->find('all');
                        foreach($devices as $device) {
                            $params['devicename'] = $device->devicename;
                            $params['mac'] = $device->mac;
                            $params['ip'] = $device->dhcp_reservation_ip;
        
                            $rc = $rc + $this->AddConf("dhcp_reservation_conf.php", $this->dhcp_reservations_conffile, $params);
                        }
                    } else {
                        $rc = 0;
                        $this->ResetConf($this->dhcp_reservations_conffile);
                        $devices = $this->Devices->find('all', ['conditions' => ['not' => ['dhcp_reservation_ip' => '']]]);
                        //$devices = $this->Devices->find('all');
                        foreach($devices as $device) {
                            $params['devicename'] = $device->devicename;
                            $params['mac'] = $device->mac;
                            $params['ip'] = $device->dhcp_reservation_ip;
    
                            // define possible subnet for device given IP using host_netmask_output and host_netmask_input
                            $dev_output = new IP4Calc($params['ip'], $this->host_netmask_output);
                            $dev_subnet_output = $dev_output->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
                            $dev_input = new IP4Calc($params['ip'], $this->host_netmask_input);
                            $dev_subnet_input = $dev_input->get(IP4Calc::NETWORK, IP4Calc::QUAD_DOTTED);
    
                            // Set DHCP setting that will classify IP
                            if($dev_subnet_output == $params['dhcp_subnet_output']) {
                                $params['subclass'] = 'external_host';
                            } elseif ($dev_subnet_input == $params['dhcp_subnet_input']) {
                                $params['subclass'] = 'internal_host';
                            }
        
                            $rc = $rc + $this->AddConf("dhcp_reservation_dual_conf.php", $this->dhcp_reservations_conffile, $params);
                        }
                    }
                }
            // if DHCP disable return 0
            } else {
                $rc = 0;
            }

            if($exit == true) {
                exit($rc);
            } else {
                return($rc);
            }
        }
    }

    /**
     * This function generate autosigned SSL certificate for HTTPS server (Apache2)
     *
     * @param $part: Part of configuration file to generate
     * 
     */
    public function certificate($part)
    {
        if($part == "generate") {
            $subj = "/C=$this->ssl_csr_c/ST=$this->ssl_csr_st/L=$this->ssl_csr_l/O=$this->ssl_csr_o/OU=$this->ssl_csr_ou/CN=$this->ssl_csr_cn"; 

            passthru("$this->bin_openssl genrsa -out $this->ssl_keyfile $this->ssl_keysize");
            passthru("$this->bin_openssl req -new -sha256 -key $this->ssl_keyfile -out $this->ssl_csr_file -subj \"$subj\"");
            passthru("$this->bin_openssl x509 -req -days $this->ssl_validity -in $this->ssl_csr_file -signkey $this->ssl_keyfile -out $this->ssl_crtfile");
        }
    }
    /**
     * This function retrieves database connection settings.
     * It is use when updating Keexybox
     *
     * @param $db: db config to get: keexybox_db_config, blacklist_db_config, logs_db_config
     * @param $item: host, username, username, password, database
     *
     * @return the value of the item
     * @exit with return code
     * 
     */
    public function GetDbConfig($db = null, $item = null) {
        if (isset($db) && isset($item)) {
            if (isset($this->$db[$item])) {
                $this->out($this->$db[$item]);
                exit(0);    
            } else {
                exit(1);    
            }
        } else {
            exit(2);    
        }
    }
}
