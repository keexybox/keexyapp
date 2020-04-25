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

$conf_data = "
DataDirectory /opt/keexybox/tor/var/lib

PidFile /opt/keexybox/tor/var/run/tor.pid
#RunAsDaemon 1
User keexybox

ControlSocket /opt/keexybox/tor/var/run/control
ControlSocketsGroupWritable 1

CookieAuthentication 1
CookieAuthFileGroupReadable 1
CookieAuthFile /opt/keexybox/tor/var/run/control.authcookie

Log notice file /opt/keexybox/logs/tor.log
SOCKSPort $this->tor_host:$this->tor_port # what port to open for local application connections
DNSPort 0.0.0.0:$this->tor_dns_port #Resolv DNS over Tor

AutomapHostsOnResolve 1
TransPort 0.0.0.0:$this->tor_trans_port
";
?>

