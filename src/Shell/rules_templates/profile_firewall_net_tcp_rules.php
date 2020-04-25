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

$dest_ip_arg = null;
if($rule_data['dest_ip'] != '') {
    $dest_ip_arg = " -d ".$rule_data['dest_ip']."/".$rule_data['dest_ip_mask'];
}

$dest_ports_arg = null;
if($rule_data['dest_ports'] != '') {
    $dest_ports_arg = " -m multiport --dport ".$rule_data['dest_ports'];
}

array_push($rules['FILTER_FORWARD']['rules'], " -p tcp $dest_ip_arg $dest_ports_arg -j ".$rule_data['target']);
?>
