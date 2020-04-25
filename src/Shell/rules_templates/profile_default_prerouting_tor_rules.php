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

// NETWORKS THAT WILL NOT BE ROUTED TO TOR
array_push($rules['NAT_PREROUTING']['rules'], " -d 0.0.0.0/8 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 10.0.0.0/8 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 127.0.0.0/8 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 169.254.0.0/16 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 172.16.0.0/12 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 192.168.0.0/16 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 224.0.0.0/4 -j RETURN");
array_push($rules['NAT_PREROUTING']['rules'], " -d 240.0.0.0/4 -j RETURN");

// REDIRECT ALL TRAFFIC TO TOR
array_push($rules['NAT_PREROUTING']['rules'], " -p tcp -j DNAT --to $this->host_ip_input:$this->tor_trans_port");

?>
