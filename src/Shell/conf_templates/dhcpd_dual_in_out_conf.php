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
"
ddns-update-style none;

option domain-name \"keexybox\";

default-lease-time 600;
max-lease-time 7200;

log-facility local7;

class \"external_host\" {
	    match hardware;
}

class \"internal_host\" {
	    match hardware;
}

include \"$this->dhcp_reservations_conffile\";

# INTERNET ROUTER SIDE
subnet ".$params['dhcp_subnet_output']." netmask $this->host_netmask_output {
    option routers $this->host_gateway;
    option domain-name-servers $this->host_ip_output;
    pool {
        range $this->dhcp_start_ip_output $this->dhcp_end_ip_output;
            allow members of \"external_host\";
            allow unknown-clients;
        }
}

# USER AND DEVICES SIDE
subnet ".$params['dhcp_subnet_input']." netmask $this->host_netmask_input {
    option routers $this->host_ip_input;
    option domain-name-servers $this->host_ip_input;
    pool {
        range $this->dhcp_start_ip_input $this->dhcp_end_ip_input;
            allow members of \"internal_host\";
            allow unknown-clients;
        }
}
";
