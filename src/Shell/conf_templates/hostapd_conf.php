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
"# General
ssid=".$this->hostapd_ssid."
interface=".$this->hostapd_interface."
bridge=".$this->hostapd_bridge."
country_code=".$this->hostapd_country_code."
hw_mode=".$this->hostapd_hw_mode."
channel=".$this->hostapd_channel."
wmm_enabled=".$this->hostapd_wmm_enabled."
macaddr_acl=".$this->hostapd_macaddr_acl."

# Security
ignore_broadcast_ssid=".$this->hostapd_ignore_broadcast_ssid."
".$params['auth_algs']."
".$params['wpa']."
".$params['wpa_key_mgmt']."
".$params['wpa_pairwise']."
".$params['rsn_pairwise']."
".$params['wpa_passphrase']."
"
?>
