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

	dnssec-validation auto;
	pid-file \"$this->bind_root_dir/var/run/named/".$params['pidfile']."\";

	// external DNS queries are disabled for not connected users and devices
    recursion no;

	auth-nxdomain no;    # conform to RFC1035
	//listen-on-v6 { any; };
};

controls {
	inet 127.0.0.1 port ".$params['rndc_port']." allow {localhost;};
};

zone \"keexybox\" {
	type master;
	file \"keexybox.zone\";
	allow-query { any; };
};

// Android Captive Portal Check domains
zone \"connectivitycheck.gstatic.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

zone \"clients1.google.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

zone \"clients3.google.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

// Android XIAOMI
zone \"connect.rom.miui.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

// Apple Captive Portal Check domains
zone \"captive.apple.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

zone \"airport.us\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

zone \"thinkdifferent.us\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

// Windows Captive Portal Check domains
zone \"msftconnecttest.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};

zone \"www.msftconnecttest.com\" {
	type master;
	file \"catchall.zone\";
	allow-query { any; };
};
"

?>
