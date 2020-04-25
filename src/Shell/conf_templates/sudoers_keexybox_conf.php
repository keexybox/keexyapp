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
"keexybox       ALL = NOPASSWD: $this->bin_ping, $this->bin_echo, $this->bin_arp, $this->bin_arpscan, $this->bin_grep, $this->bin_iptables, $this->bin_openssl, $this->bin_iptables_save, $this->bin_date, $this->bin_hwclock, $this->tor_init, $this->ntp_init, $this->bind9_init, $this->bin_reboot, $this->bin_halt, $this->dhcp_init, $this->bin_sysctl -w net.ipv4.ip_forward=1, /bin/chmod 664 $this->logrotate_conf_file, /bin/chmod 644 $this->logrotate_conf_file
"
?>
