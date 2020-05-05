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
include \"$this->bind_root_dir/etc/conf.d/acl_profile_".$params['profile_id'].".conf\";

view view_profile_".$params['profile_id']." {
    include \"$this->bind_root_dir/etc/named.conf.default-zones\";

    match-clients { acl_profile_".$params['profile_id']."; };

    allow-recursion { any; };

    // Shared cache between View (help to save memory)
    attach-cache \"shared_cache\";
    max-cache-size 64M;

    // Zone for blocked domains
    zone \"keexybox\"{type master;file \"keexybox.zone\";};

    // Disable DNS over HTTPS
	zone \"doh.rpz\"{type master;file \"doh.zone\";};
    response-policy {
       zone \"doh.rpz\" policy nxdomain;
    };

    ".$params['safesearch']."

    dlz \"blacklist zones\" {
        database \"mysql
            {host=".$this->blacklist_db_config['host']." dbname=".$this->blacklist_db_config['database']." user=".$this->blacklist_db_config['username']." pass=".$this->blacklist_db_config['password']."}
            {SELECT zone FROM blacklist WHERE zone = '\$zone\$'".$params['category_search_string']."}
            {SELECT NULL as ttl, 'CNAME' as type, NULL as mx_priority, 'keexybox.' as data FROM blacklist WHERE zone = '\$zone\$' AND host LIKE '%\$record\$%'}
            {SELECT 86400 as ttl, 'SOA' as type, 'localhost.' as data, 'root.localhost.' as resp_person, 1 as serial, 3600 as refresh, 200 as retry, 3600000 as expire, 3600 as minimum FROM `blacklist` WHERE zone = '\$zone\$' UNION SELECT NULL as ttl, 'NS' as type, 'localhost.' as data, NULL as resp_person, NULL as serial, NULL as refresh, NULL as retry, NULL as expire, NULL as minimum FROM `blacklist` WHERE zone = '\$zone\$'} 
        \";
    };
};
"
?>
