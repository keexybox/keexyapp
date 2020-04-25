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
 * along with Keexybox.	If not, see <http://www.gnu.org/licenses/>.
 *
 */

/******************
 * NAT PREROUTING *
 ******************/
// NAT 80 and 443 port to redirect to HTTP web server port that display "Drop by keexybox" 
array_push($rules['NAT_PREROUTING']['rules'], " -d $this->host_ip_input,$this->host_ip_output -p tcp --dport 80 -j DNAT --to-destination $this->host_ip_output:$this->apache_denied_access_http_port");
array_push($rules['NAT_PREROUTING']['rules'], " -d $this->host_ip_input,$this->host_ip_output -p tcp --dport 443 -j DNAT --to-destination $this->host_ip_output:$this->apache_denied_access_https_port");
array_push($rules['NAT_PREROUTING']['rules'], " -p udp --dport 53 -j REDIRECT --to-ports $this->named_port_portal");

/****************
 * FILTER INPUT *
 ****************/
// ACCEPT DHCP, SSH, HTTP, HTTPS AND CUSTOM PORT TO KEEXYBOX 
array_push($rules['FILTER_INPUT']['rules'], " -d $this->host_ip_input,$this->host_ip_output -p tcp -m multiport --dport 67,22,80,443,$this->apache_admin_port,$this->apache_admin_https_port,$this->apache_denied_access_http_port,$this->apache_denied_access_https_port -j ACCEPT");
array_push($rules['FILTER_INPUT']['rules'], " -d $this->host_ip_input,$this->host_ip_output -p udp -m multiport --dport 67,53,$this->named_port_portal -j ACCEPT");
// ACCEPT PING TO KEEXYBOX
array_push($rules['FILTER_INPUT']['rules'], " -d $this->host_ip_input,$this->host_ip_output -p icmp -j ACCEPT");
// ACCEPT ESTABLISHED AND RELATED CONNECTIONS
array_push($rules['FILTER_INPUT']['rules'], " -m state --state ESTABLISHED,RELATED -j ACCEPT");
// ACCEPT ANY CONNECTIONS FROM LOCALHOST TO LOCALHOST
array_push($rules['FILTER_INPUT']['rules'], " -i lo -j ACCEPT");
// DROP EVERYTHING ELSE
array_push($rules['FILTER_INPUT']['rules'], " -d $this->host_ip_input,$this->host_ip_output -j DROP");

/******************
 * FILTER FORWARD *
 ******************/
// ACCEPT CONNECTION BETWEEN INTERNAL AND OUTPUT NETWORK
array_push($rules['FILTER_FORWARD']['rules'], " -s ".$rule_data['out_subnet']." -d ".$rule_data['in_subnet']." -j ACCEPT");
array_push($rules['FILTER_FORWARD']['rules'], " -s ".$rule_data['in_subnet']." -d ".$rule_data['out_subnet']." -j ACCEPT");
// ACCEPT ESTABLISHED AND RELATED CONNECTIONS
array_push($rules['FILTER_FORWARD']['rules'], " -m state --state ESTABLISHED,RELATED -j ACCEPT");

/*****************
 * FILTER OUTPUT *
 *****************/
// ACCEPT CONNECTIONS FROM KEEXYBOX TO ANY
array_push($rules['FILTER_OUTPUT']['rules'], " -s $this->host_ip_input,$this->host_ip_output -j ACCEPT");
// ACCEPT ANY CONNECTION FROM LOCALHOST TO LOCALHOST
array_push($rules['FILTER_OUTPUT']['rules'], " -o lo -j ACCEPT");
// ACCEPT ESTABLISHED AND RELATED CONNECTIONS
array_push($rules['FILTER_OUTPUT']['rules'], " -m state --state ESTABLISHED,RELATED -j ACCEPT");

/******************
 * NAT POSTROUTING *
 ******************/
// REQUIRED TO ROUTE TRAFFIC TRHU OUTPUT INTERFACE
array_push($rules['NAT_POSTROUTING']['rules'], " -o $this->host_interface_output -j MASQUERADE");
//array_push($rules['NAT_POSTROUTING']['rules'], " -j MASQUERADE");

?>
