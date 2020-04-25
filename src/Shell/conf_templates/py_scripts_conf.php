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

//{host=".$this->blacklist_db_config['host']." dbname=".$this->blacklist_db_config['database']." user=".$this->blacklist_db_config['username']." pass=".$this->blacklist_db_config['password']."}

$conf_data =  
"#!/usr/bin/python

#------ DB Settings -------------------#
mysql_host = \"".$this->keexybox_db_config['host']."\"
mysql_user = \"".$this->keexybox_db_config['username']."\"
mysql_pass = \"".$this->keexybox_db_config['password']."\"

# Keexybox DB
mysql_kxydb = \"".$this->keexybox_db_config['database']."\"

# Blacklist DB
mysql_bldb = \"".$this->blacklist_db_config['database']."\"

# Logs DB
mysql_logdb = \"".$this->logs_db_config['database']."\"

#------ Common settings ---------------#
working_dir = \"/tmp/\"

#------ Import blacklist settings -----#
domains_per_query = 10000

#------ Import Logs settings ----------#
logs_per_query = 10000
first_line = 0
access_denied_log_table = \"access_denied_log\"
dns_log_table = \"dns_log\"
"
?>
