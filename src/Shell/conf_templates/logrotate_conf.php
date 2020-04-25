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
$this->keexyboxlogs/devices.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	create 644 keexybox keexybox
	dateext
}
$this->keexyboxlogs/dnscache.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	create 644 keexybox keexybox
	dateext
}
$this->keexyboxlogs/iptables.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	create 644 keexybox keexybox
	dateext
}
$this->keexyboxlogs/service.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	create 644 keexybox keexybox
	dateext
}
$this->keexyboxlogs/users.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	create 644 keexybox keexybox
	dateext
}
$this->keexyboxlogs/tor.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	create 644 keexybox keexybox
	dateext
}
$this->keexyboxlogs/bind_*.log {
	daily
	rotate $this->log_retention
	compress
	delaycompress
	missingok
	notifempty
	copytruncate
	create 644 keexybox keexybox
	dateext
}
"
?>
