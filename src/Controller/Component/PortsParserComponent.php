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

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * This component is used to manage TCP/IP Ports for Profile Firewall 
 *
 * @author Benoit SAGLIETTO bsaglietto[@]keexybox.org
 */
class PortsParserComponent extends Component
{
    /**
     * This function convert array of ports to a valid Iptables string for destination ports
     * It also list port that are not valid
     *
     * @param Array $ports_array: Example :
     *    [ 
     *        1 => ['port' => '50', 'last_port' => '60'
     *        2 => ['port' => '84', 'last_port' => '80',
     *        3 => ['port' => '22', 'last_port' => '',
     *     ],
     * 
     * @return Array that contains string of accepted and invalid ports 
     *    [
     *        'accepted' => '50:60,22',
     *        'invalid' => '84:80'
     *    ]
     */
    public function set_ports_string($ports_array)
    {
        //$dest_ports['string'] = null;
        $dest_ports['accepted'] = null;
        $dest_ports['invalid'] = null;

        foreach($ports_array as $port_item)
        {
            $port = null;
            $last_port = null;
            $port_item['port'] = trim($port_item['port']);
            $port_item['last_port'] = trim($port_item['last_port']);

            // Convert port or port name to numeric 
            if($port_item['port'] != '') {
                // Set port if numeric
                if(is_numeric($port_item['port'])) {
                    $port = $port_item['port'];
                // Else translate port name to numeric
                } else {
                    $port_number = $this->get_port_number($port_item['port']);
                    if(isset($port_number)) {
                        $port = $port_number;
                    } 
                }
            } 
        
            // Convert last port or last port name to numeric 
            if($port_item['last_port'] != '') {
                if(is_numeric($port_item['last_port'])) {
                    $last_port = $port_item['last_port'];
                } else {
                    $last_port_number = $this->get_port_number($port_item['last_port']);
                    if(isset($last_port_number)) {
                        $last_port = $last_port_number;
                    }
                }
            }

            // Set port or ports range that are accepted or invalid
            if(isset($port) and !isset($last_port) and $port > 0 and $port < 65535) {
                $single_ports[] = $port;
            } elseif(isset($port) and isset($last_port) and $port < $last_port and $port > 0 and $port < 65535 and $last_port > 0 and $last_port < 65535) {
                $range_ports[] = "$port:$last_port";
            } else {
                $invalid_ports[] = $port_item;
            }
        }
        

        // Concatenate string of accepted single port
        if(isset($single_ports)) {
            foreach($single_ports as $single_port) {
                $dest_ports['accepted'] .= "$single_port,";
            }
        }

        // Concatenate string of accepted ports range
        if(isset($range_ports)) {
            foreach($range_ports as $range_port) {
                $dest_ports['accepted'] .= "$range_port,";
            }
        }
        $dest_ports['accepted'] = rtrim($dest_ports['accepted'], ",");

        // Concatenate string of invalid ports
        if(isset($invalid_ports)) {
            foreach($invalid_ports as $invalid_port) {
                if($invalid_port['port'] != '' or $invalid_port['last_port'] != '') {
                    $dest_ports['invalid'] .= $invalid_port['port']."-".$invalid_port['last_port'].",";
                }
            }
            $dest_ports['invalid'] = str_replace("-,", ",", $dest_ports['invalid']);
            $dest_ports['invalid'] = str_replace(",-", ",", $dest_ports['invalid']);
            $dest_ports['invalid'] = preg_replace("/^-/", "", $dest_ports['invalid']);
            $dest_ports['invalid'] = preg_replace("/,$/", "", $dest_ports['invalid']);
        }


        return $dest_ports;
    }

    /**
     * This function translate port number to port name
     * for example: 80 to http
     *
     * @param Integer $port_number: example '80'
     * 
     * @return String: example 'http'
     */
    private function get_port_name($port_number)
    {
        $ports_list = $this->ports_list();
        $key = array_search($port_number, array_column($ports_list, 'port_number'));
        if($key != '') {
            $value = $ports_list[$key]['port_name'];
            return $value;
        } else {
            return null;
        }
    }

    /**
     * This function translate port name to port number
     * for example: http to 80
     *
     * @param String $port_name: example 'http'
     * 
     * @return Integer: example '80'
     */
    private function get_port_number($port_name)
    {
        $ports_list = $this->ports_list();
        $key = array_search($port_name, array_column($ports_list, 'port_name'));
        if($key != '') {
            $value = $ports_list[$key]['port_number'];
            return $value;
        } 
    }

    /**
     * This function lists the port number and port name matches
     * The array in this function was generated from /etc/services
     *
     * @return Array
     */
    private function ports_list()
    {
        $services = array(
            ['port_number' => '1', 'port_name' => 'tcpmux'],
            ['port_number' => '7', 'port_name' => 'echo'],
            ['port_number' => '9', 'port_name' => 'discard'],
            ['port_number' => '11', 'port_name' => 'systat'],
            ['port_number' => '13', 'port_name' => 'daytime'],
            ['port_number' => '15', 'port_name' => 'netstat'],
            ['port_number' => '17', 'port_name' => 'qotd'],
            ['port_number' => '18', 'port_name' => 'msp'],
            ['port_number' => '19', 'port_name' => 'chargen'],
            ['port_number' => '20', 'port_name' => 'ftp-data'],
            ['port_number' => '21', 'port_name' => 'fsp'],
            ['port_number' => '21', 'port_name' => 'ftp'],
            ['port_number' => '22', 'port_name' => 'ssh'],
            ['port_number' => '23', 'port_name' => 'telnet'],
            ['port_number' => '25', 'port_name' => 'smtp'],
            ['port_number' => '37', 'port_name' => 'time'],
            ['port_number' => '39', 'port_name' => 'rlp'],
            ['port_number' => '42', 'port_name' => 'nameserver'],
            ['port_number' => '43', 'port_name' => 'whois'],
            ['port_number' => '49', 'port_name' => 'tacacs'],
            ['port_number' => '50', 'port_name' => 're-mail-ck'],
            ['port_number' => '53', 'port_name' => 'domain'],
            ['port_number' => '57', 'port_name' => 'mtp'],
            ['port_number' => '65', 'port_name' => 'tacacs-ds'],
            ['port_number' => '67', 'port_name' => 'bootps'],
            ['port_number' => '68', 'port_name' => 'bootpc'],
            ['port_number' => '69', 'port_name' => 'tftp'],
            ['port_number' => '70', 'port_name' => 'gopher'],
            ['port_number' => '77', 'port_name' => 'rje'],
            ['port_number' => '79', 'port_name' => 'finger'],
            ['port_number' => '80', 'port_name' => 'http'],
            ['port_number' => '87', 'port_name' => 'link'],
            ['port_number' => '88', 'port_name' => 'kerberos'],
            ['port_number' => '95', 'port_name' => 'supdup'],
            ['port_number' => '98', 'port_name' => 'linuxconf'],
            ['port_number' => '101', 'port_name' => 'hostnames'],
            ['port_number' => '102', 'port_name' => 'iso-tsap'],
            ['port_number' => '104', 'port_name' => 'acr-nema'],
            ['port_number' => '105', 'port_name' => 'csnet-ns'],
            ['port_number' => '106', 'port_name' => 'poppassd'],
            ['port_number' => '107', 'port_name' => 'rtelnet'],
            ['port_number' => '109', 'port_name' => 'pop2'],
            ['port_number' => '110', 'port_name' => 'pop3'],
            ['port_number' => '111', 'port_name' => 'sunrpc'],
            ['port_number' => '113', 'port_name' => 'auth'],
            ['port_number' => '115', 'port_name' => 'sftp'],
            ['port_number' => '117', 'port_name' => 'uucp-path'],
            ['port_number' => '119', 'port_name' => 'nntp'],
            ['port_number' => '123', 'port_name' => 'ntp'],
            ['port_number' => '129', 'port_name' => 'pwdgen'],
            ['port_number' => '135', 'port_name' => 'loc-srv'],
            ['port_number' => '137', 'port_name' => 'netbios-ns'],
            ['port_number' => '138', 'port_name' => 'netbios-dgm'],
            ['port_number' => '139', 'port_name' => 'netbios-ssn'],
            ['port_number' => '143', 'port_name' => 'imap2'],
            ['port_number' => '161', 'port_name' => 'snmp'],
            ['port_number' => '162', 'port_name' => 'snmp-trap'],
            ['port_number' => '163', 'port_name' => 'cmip-man'],
            ['port_number' => '164', 'port_name' => 'cmip-agent'],
            ['port_number' => '174', 'port_name' => 'mailq'],
            ['port_number' => '177', 'port_name' => 'xdmcp'],
            ['port_number' => '178', 'port_name' => 'nextstep'],
            ['port_number' => '179', 'port_name' => 'bgp'],
            ['port_number' => '191', 'port_name' => 'prospero'],
            ['port_number' => '194', 'port_name' => 'irc'],
            ['port_number' => '199', 'port_name' => 'smux'],
            ['port_number' => '201', 'port_name' => 'at-rtmp'],
            ['port_number' => '202', 'port_name' => 'at-nbp'],
            ['port_number' => '204', 'port_name' => 'at-echo'],
            ['port_number' => '206', 'port_name' => 'at-zis'],
            ['port_number' => '209', 'port_name' => 'qmtp'],
            ['port_number' => '210', 'port_name' => 'z3950'],
            ['port_number' => '213', 'port_name' => 'ipx'],
            ['port_number' => '220', 'port_name' => 'imap3'],
            ['port_number' => '345', 'port_name' => 'pawserv'],
            ['port_number' => '346', 'port_name' => 'zserv'],
            ['port_number' => '347', 'port_name' => 'fatserv'],
            ['port_number' => '369', 'port_name' => 'rpc2portmap'],
            ['port_number' => '370', 'port_name' => 'codaauth2'],
            ['port_number' => '371', 'port_name' => 'clearcase'],
            ['port_number' => '372', 'port_name' => 'ulistserv'],
            ['port_number' => '389', 'port_name' => 'ldap'],
            ['port_number' => '406', 'port_name' => 'imsp'],
            ['port_number' => '427', 'port_name' => 'svrloc'],
            ['port_number' => '443', 'port_name' => 'https'],
            ['port_number' => '444', 'port_name' => 'snpp'],
            ['port_number' => '445', 'port_name' => 'microsoft-ds'],
            ['port_number' => '464', 'port_name' => 'kpasswd'],
            ['port_number' => '465', 'port_name' => 'urd'],
            ['port_number' => '487', 'port_name' => 'saft'],
            ['port_number' => '500', 'port_name' => 'isakmp'],
            ['port_number' => '512', 'port_name' => 'biff'],
            ['port_number' => '512', 'port_name' => 'exec'],
            ['port_number' => '513', 'port_name' => 'login'],
            ['port_number' => '513', 'port_name' => 'who'],
            ['port_number' => '514', 'port_name' => 'shell'],
            ['port_number' => '514', 'port_name' => 'syslog'],
            ['port_number' => '515', 'port_name' => 'printer'],
            ['port_number' => '517', 'port_name' => 'talk'],
            ['port_number' => '518', 'port_name' => 'ntalk'],
            ['port_number' => '520', 'port_name' => 'route'],
            ['port_number' => '525', 'port_name' => 'timed'],
            ['port_number' => '526', 'port_name' => 'tempo'],
            ['port_number' => '530', 'port_name' => 'courier'],
            ['port_number' => '531', 'port_name' => 'conference'],
            ['port_number' => '532', 'port_name' => 'netnews'],
            ['port_number' => '533', 'port_name' => 'netwall'],
            ['port_number' => '538', 'port_name' => 'gdomap'],
            ['port_number' => '540', 'port_name' => 'uucp'],
            ['port_number' => '543', 'port_name' => 'klogin'],
            ['port_number' => '544', 'port_name' => 'kshell'],
            ['port_number' => '546', 'port_name' => 'dhcpv6-client'],
            ['port_number' => '547', 'port_name' => 'dhcpv6-server'],
            ['port_number' => '548', 'port_name' => 'afpovertcp'],
            ['port_number' => '549', 'port_name' => 'idfp'],
            ['port_number' => '554', 'port_name' => 'rtsp'],
            ['port_number' => '556', 'port_name' => 'remotefs'],
            ['port_number' => '563', 'port_name' => 'nntps'],
            ['port_number' => '587', 'port_name' => 'submission'],
            ['port_number' => '607', 'port_name' => 'nqs'],
            ['port_number' => '610', 'port_name' => 'npmp-local'],
            ['port_number' => '611', 'port_name' => 'npmp-gui'],
            ['port_number' => '612', 'port_name' => 'hmmp-ind'],
            ['port_number' => '623', 'port_name' => 'asf-rmcp'],
            ['port_number' => '628', 'port_name' => 'qmqp'],
            ['port_number' => '631', 'port_name' => 'ipp'],
            ['port_number' => '636', 'port_name' => 'ldaps'],
            ['port_number' => '655', 'port_name' => 'tinc'],
            ['port_number' => '706', 'port_name' => 'silc'],
            ['port_number' => '749', 'port_name' => 'kerberos-adm'],
            ['port_number' => '750', 'port_name' => 'kerberos4'],
            ['port_number' => '751', 'port_name' => 'kerberos-master'],
            ['port_number' => '752', 'port_name' => 'passwd-server'],
            ['port_number' => '754', 'port_name' => 'krb-prop'],
            ['port_number' => '760', 'port_name' => 'krbupdate'],
            ['port_number' => '765', 'port_name' => 'webster'],
            ['port_number' => '775', 'port_name' => 'moira-db'],
            ['port_number' => '777', 'port_name' => 'moira-update'],
            ['port_number' => '779', 'port_name' => 'moira-ureg'],
            ['port_number' => '783', 'port_name' => 'spamd'],
            ['port_number' => '808', 'port_name' => 'omirr'],
            ['port_number' => '871', 'port_name' => 'supfilesrv'],
            ['port_number' => '873', 'port_name' => 'rsync'],
            ['port_number' => '901', 'port_name' => 'swat'],
            ['port_number' => '989', 'port_name' => 'ftps-data'],
            ['port_number' => '990', 'port_name' => 'ftps'],
            ['port_number' => '992', 'port_name' => 'telnets'],
            ['port_number' => '993', 'port_name' => 'imaps'],
            ['port_number' => '994', 'port_name' => 'ircs'],
            ['port_number' => '995', 'port_name' => 'pop3s'],
            ['port_number' => '1001', 'port_name' => 'customs'],
            ['port_number' => '1080', 'port_name' => 'socks'],
            ['port_number' => '1093', 'port_name' => 'proofd'],
            ['port_number' => '1094', 'port_name' => 'rootd'],
            ['port_number' => '1099', 'port_name' => 'rmiregistry'],
            ['port_number' => '1109', 'port_name' => 'kpop'],
            ['port_number' => '1127', 'port_name' => 'supfiledbg'],
            ['port_number' => '1178', 'port_name' => 'skkserv'],
            ['port_number' => '1194', 'port_name' => 'openvpn'],
            ['port_number' => '1210', 'port_name' => 'predict'],
            ['port_number' => '1214', 'port_name' => 'kazaa'],
            ['port_number' => '1236', 'port_name' => 'rmtcfg'],
            ['port_number' => '1241', 'port_name' => 'nessus'],
            ['port_number' => '1300', 'port_name' => 'wipld'],
            ['port_number' => '1313', 'port_name' => 'xtel'],
            ['port_number' => '1314', 'port_name' => 'xtelw'],
            ['port_number' => '1352', 'port_name' => 'lotusnote'],
            ['port_number' => '1433', 'port_name' => 'ms-sql-s'],
            ['port_number' => '1434', 'port_name' => 'ms-sql-m'],
            ['port_number' => '1524', 'port_name' => 'ingreslock'],
            ['port_number' => '1525', 'port_name' => 'prospero-np'],
            ['port_number' => '1529', 'port_name' => 'support'],
            ['port_number' => '1645', 'port_name' => 'datametrics'],
            ['port_number' => '1646', 'port_name' => 'sa-msg-port'],
            ['port_number' => '1649', 'port_name' => 'kermit'],
            ['port_number' => '1677', 'port_name' => 'groupwise'],
            ['port_number' => '1701', 'port_name' => 'l2f'],
            ['port_number' => '1812', 'port_name' => 'radius'],
            ['port_number' => '1813', 'port_name' => 'radius-acct'],
            ['port_number' => '1863', 'port_name' => 'msnp'],
            ['port_number' => '1957', 'port_name' => 'unix-status'],
            ['port_number' => '1958', 'port_name' => 'log-server'],
            ['port_number' => '1959', 'port_name' => 'remoteping'],
            ['port_number' => '2000', 'port_name' => 'cisco-sccp'],
            ['port_number' => '2003', 'port_name' => 'cfinger'],
            ['port_number' => '2010', 'port_name' => 'pipe-server'],
            ['port_number' => '2010', 'port_name' => 'search'],
            ['port_number' => '2049', 'port_name' => 'nfs'],
            ['port_number' => '2053', 'port_name' => 'knetd'],
            ['port_number' => '2086', 'port_name' => 'gnunet'],
            ['port_number' => '2101', 'port_name' => 'rtcm-sc104'],
            ['port_number' => '2102', 'port_name' => 'zephyr-srv'],
            ['port_number' => '2103', 'port_name' => 'zephyr-clt'],
            ['port_number' => '2104', 'port_name' => 'zephyr-hm'],
            ['port_number' => '2105', 'port_name' => 'eklogin'],
            ['port_number' => '2111', 'port_name' => 'kx'],
            ['port_number' => '2119', 'port_name' => 'gsigatekeeper'],
            ['port_number' => '2121', 'port_name' => 'frox'],
            ['port_number' => '2121', 'port_name' => 'iprop'],
            ['port_number' => '2135', 'port_name' => 'gris'],
            ['port_number' => '2150', 'port_name' => 'ninstall'],
            ['port_number' => '2401', 'port_name' => 'cvspserver'],
            ['port_number' => '2430', 'port_name' => 'venus'],
            ['port_number' => '2431', 'port_name' => 'venus-se'],
            ['port_number' => '2432', 'port_name' => 'codasrv'],
            ['port_number' => '2433', 'port_name' => 'codasrv-se'],
            ['port_number' => '2583', 'port_name' => 'mon'],
            ['port_number' => '2600', 'port_name' => 'zebrasrv'],
            ['port_number' => '2601', 'port_name' => 'zebra'],
            ['port_number' => '2602', 'port_name' => 'ripd'],
            ['port_number' => '2603', 'port_name' => 'ripngd'],
            ['port_number' => '2604', 'port_name' => 'ospfd'],
            ['port_number' => '2605', 'port_name' => 'bgpd'],
            ['port_number' => '2606', 'port_name' => 'ospf6d'],
            ['port_number' => '2607', 'port_name' => 'ospfapi'],
            ['port_number' => '2608', 'port_name' => 'isisd'],
            ['port_number' => '2628', 'port_name' => 'dict'],
            ['port_number' => '2792', 'port_name' => 'f5-globalsite'],
            ['port_number' => '2811', 'port_name' => 'gsiftp'],
            ['port_number' => '2947', 'port_name' => 'gpsd'],
            ['port_number' => '2988', 'port_name' => 'afbackup'],
            ['port_number' => '2989', 'port_name' => 'afmbackup'],
            ['port_number' => '3050', 'port_name' => 'gds-db'],
            ['port_number' => '3130', 'port_name' => 'icpv2'],
            ['port_number' => '3260', 'port_name' => 'iscsi-target'],
            ['port_number' => '3306', 'port_name' => 'mysql'],
            ['port_number' => '3493', 'port_name' => 'nut'],
            ['port_number' => '3632', 'port_name' => 'distcc'],
            ['port_number' => '3689', 'port_name' => 'daap'],
            ['port_number' => '3690', 'port_name' => 'svn'],
            ['port_number' => '4031', 'port_name' => 'suucp'],
            ['port_number' => '4094', 'port_name' => 'sysrqd'],
            ['port_number' => '4190', 'port_name' => 'sieve'],
            ['port_number' => '4224', 'port_name' => 'xtell'],
            ['port_number' => '4353', 'port_name' => 'f5-iquery'],
            ['port_number' => '4369', 'port_name' => 'epmd'],
            ['port_number' => '4373', 'port_name' => 'remctl'],
            ['port_number' => '4500', 'port_name' => 'ipsec-nat-t'],
            ['port_number' => '4557', 'port_name' => 'fax'],
            ['port_number' => '4559', 'port_name' => 'hylafax'],
            ['port_number' => '4569', 'port_name' => 'iax'],
            ['port_number' => '4600', 'port_name' => 'distmp3'],
            ['port_number' => '4691', 'port_name' => 'mtn'],
            ['port_number' => '4899', 'port_name' => 'radmin-port'],
            ['port_number' => '4949', 'port_name' => 'munin'],
            ['port_number' => '5002', 'port_name' => 'rfe'],
            ['port_number' => '5050', 'port_name' => 'mmcc'],
            ['port_number' => '5051', 'port_name' => 'enbd-cstatd'],
            ['port_number' => '5052', 'port_name' => 'enbd-sstatd'],
            ['port_number' => '5060', 'port_name' => 'sip'],
            ['port_number' => '5061', 'port_name' => 'sip-tls'],
            ['port_number' => '5151', 'port_name' => 'pcrd'],
            ['port_number' => '5190', 'port_name' => 'aol'],
            ['port_number' => '5222', 'port_name' => 'xmpp-client'],
            ['port_number' => '5269', 'port_name' => 'xmpp-server'],
            ['port_number' => '5308', 'port_name' => 'cfengine'],
            ['port_number' => '5353', 'port_name' => 'mdns'],
            ['port_number' => '5354', 'port_name' => 'noclog'],
            ['port_number' => '5355', 'port_name' => 'hostmon'],
            ['port_number' => '5432', 'port_name' => 'postgresql'],
            ['port_number' => '5555', 'port_name' => 'rplay'],
            ['port_number' => '5556', 'port_name' => 'freeciv'],
            ['port_number' => '5666', 'port_name' => 'nrpe'],
            ['port_number' => '5667', 'port_name' => 'nsca'],
            ['port_number' => '5671', 'port_name' => 'amqps'],
            ['port_number' => '5672', 'port_name' => 'amqp'],
            ['port_number' => '5674', 'port_name' => 'mrtd'],
            ['port_number' => '5675', 'port_name' => 'bgpsim'],
            ['port_number' => '5680', 'port_name' => 'canna'],
            ['port_number' => '5688', 'port_name' => 'ggz'],
            ['port_number' => '6000', 'port_name' => 'x11'],
            ['port_number' => '6001', 'port_name' => 'x11-1'],
            ['port_number' => '6002', 'port_name' => 'x11-2'],
            ['port_number' => '6003', 'port_name' => 'x11-3'],
            ['port_number' => '6004', 'port_name' => 'x11-4'],
            ['port_number' => '6005', 'port_name' => 'x11-5'],
            ['port_number' => '6006', 'port_name' => 'x11-6'],
            ['port_number' => '6007', 'port_name' => 'x11-7'],
            ['port_number' => '6346', 'port_name' => 'gnutella-svc'],
            ['port_number' => '6347', 'port_name' => 'gnutella-rtr'],
            ['port_number' => '6444', 'port_name' => 'sge-qmaster'],
            ['port_number' => '6445', 'port_name' => 'sge-execd'],
            ['port_number' => '6446', 'port_name' => 'mysql-proxy'],
            ['port_number' => '6514', 'port_name' => 'syslog-tls'],
            ['port_number' => '6566', 'port_name' => 'sane-port'],
            ['port_number' => '6667', 'port_name' => 'ircd'],
            ['port_number' => '7000', 'port_name' => 'afs3-fileserver'],
            ['port_number' => '7001', 'port_name' => 'afs3-callback'],
            ['port_number' => '7002', 'port_name' => 'afs3-prserver'],
            ['port_number' => '7003', 'port_name' => 'afs3-vlserver'],
            ['port_number' => '7004', 'port_name' => 'afs3-kaserver'],
            ['port_number' => '7005', 'port_name' => 'afs3-volser'],
            ['port_number' => '7006', 'port_name' => 'afs3-errors'],
            ['port_number' => '7007', 'port_name' => 'afs3-bos'],
            ['port_number' => '7008', 'port_name' => 'afs3-update'],
            ['port_number' => '7009', 'port_name' => 'afs3-rmtsys'],
            ['port_number' => '7100', 'port_name' => 'font-service'],
            ['port_number' => '8021', 'port_name' => 'zope-ftp'],
            ['port_number' => '8080', 'port_name' => 'http-alt'],
            ['port_number' => '8081', 'port_name' => 'tproxy'],
            ['port_number' => '8088', 'port_name' => 'omniorb'],
            ['port_number' => '8990', 'port_name' => 'clc-build-daemon'],
            ['port_number' => '9098', 'port_name' => 'xinetd'],
            ['port_number' => '9101', 'port_name' => 'bacula-dir'],
            ['port_number' => '9102', 'port_name' => 'bacula-fd'],
            ['port_number' => '9103', 'port_name' => 'bacula-sd'],
            ['port_number' => '9359', 'port_name' => 'mandelspawn'],
            ['port_number' => '9418', 'port_name' => 'git'],
            ['port_number' => '9667', 'port_name' => 'xmms2'],
            ['port_number' => '9673', 'port_name' => 'zope'],
            ['port_number' => '10000', 'port_name' => 'webmin'],
            ['port_number' => '10050', 'port_name' => 'zabbix-agent'],
            ['port_number' => '10051', 'port_name' => 'zabbix-trapper'],
            ['port_number' => '10080', 'port_name' => 'amanda'],
            ['port_number' => '10081', 'port_name' => 'kamanda'],
            ['port_number' => '10082', 'port_name' => 'amandaidx'],
            ['port_number' => '10083', 'port_name' => 'amidxtape'],
            ['port_number' => '10809', 'port_name' => 'nbd'],
            ['port_number' => '11112', 'port_name' => 'dicom'],
            ['port_number' => '11201', 'port_name' => 'smsqp'],
            ['port_number' => '11371', 'port_name' => 'hkp'],
            ['port_number' => '13720', 'port_name' => 'bprd'],
            ['port_number' => '13721', 'port_name' => 'bpdbm'],
            ['port_number' => '13722', 'port_name' => 'bpjava-msvc'],
            ['port_number' => '13724', 'port_name' => 'vnetd'],
            ['port_number' => '13782', 'port_name' => 'bpcd'],
            ['port_number' => '13783', 'port_name' => 'vopied'],
            ['port_number' => '15345', 'port_name' => 'xpilot'],
            ['port_number' => '17001', 'port_name' => 'sgi-cmsd'],
            ['port_number' => '17002', 'port_name' => 'sgi-crsd'],
            ['port_number' => '17003', 'port_name' => 'sgi-gcd'],
            ['port_number' => '17004', 'port_name' => 'sgi-cad'],
            ['port_number' => '17500', 'port_name' => 'db-lsp'],
            ['port_number' => '20011', 'port_name' => 'isdnlog'],
            ['port_number' => '20012', 'port_name' => 'vboxd'],
            ['port_number' => '22125', 'port_name' => 'dcap'],
            ['port_number' => '22128', 'port_name' => 'gsidcap'],
            ['port_number' => '22273', 'port_name' => 'wnn6'],
            ['port_number' => '24554', 'port_name' => 'binkp'],
            ['port_number' => '27374', 'port_name' => 'asp'],
            ['port_number' => '30865', 'port_name' => 'csync2'],
            ['port_number' => '57000', 'port_name' => 'dircproxy'],
            ['port_number' => '60177', 'port_name' => 'tfido'],
            ['port_number' => '60179', 'port_name' => 'fido'],
        );
        return $services;
    }
}
?>
