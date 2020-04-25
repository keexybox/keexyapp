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
"options {
	directory \"$this->bind_root_dir/etc/zones\";

	listen-on port ".$params['named_port']." { any; };

	".
	$params['redirectors']
	."

	dnssec-validation no;
	pid-file \"$this->bind_root_dir/var/run/named/".$params['pidfile']."\";

	auth-nxdomain no;    # conform to RFC1035
	//listen-on-v6 { any; };
};

controls {
	inet 127.0.0.1 port ".$params['rndc_port']." allow {localhost;};
};

include \"$this->bind_root_dir/etc/conf.d/profiles.conf\";
".$params['logging']."\n"
?>
