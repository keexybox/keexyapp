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

$conf_data =
"# This file describes the network interfaces available on your system
# and how to activate them. For more information, see interfaces(5).

# Auto load interfaces
auto lo $this->host_interface_output $this->host_interface_input
iface lo inet loopback

# Hotplug interfaces
allow-hotplug $this->host_interface_output $this->host_interface_input


# Output network configuration
iface $this->host_interface_output inet static
    address $this->host_ip_output
    netmask $this->host_netmask_output
    gateway $this->host_gateway
    dns-nameservers $this->host_dns1 $this->host_dns2
    ".$params['wpa_config_out']."
    ".$params['bridge_ports']."
    ".$params['bridge_stp']."
    ".$params['bridge_waitport']."
    ".$params['bridge_waitport']."

# Internal network configuration
iface $this->host_interface_input inet static
    address $this->host_ip_input
    netmask $this->host_netmask_input
    ".$params['wpa_config_in']."
";
?>
