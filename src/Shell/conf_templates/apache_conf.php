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
"<VirtualHost *:$this->apache_redirect_port>
	RewriteEngine On
	RewriteCond %{SERVER_PORT} !^$this->apache_port$
	#RewriteRule ^/?(.*)/ http://%{SERVER_NAME}:$this->apache_port/$1 [R,L]
	RewriteRule ^/?(.*)/ http://$this->host_ip:$this->apache_port/$1 [R,L]
	ErrorLog \${APACHE_LOG_DIR}/error.log
	CustomLog \${APACHE_LOG_DIR}/access.log combined
	#LogLevel warn mod_rewrite.c:trace3
</VirtualHost>

<VirtualHost *:$this->apache_port>
	ServerAdmin webmaster@localhost
	DocumentRoot ".WWW_ROOT."

        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>

	<Directory ".WWW_ROOT.">
                Options FollowSymLinks MultiViews
                AllowOverride All
                Order deny,allow
                allow from all
                Require all granted
	</Directory>

	ErrorLog \${APACHE_LOG_DIR}/error.log
	CustomLog \${APACHE_LOG_DIR}/access.log combined

</VirtualHost>


<VirtualHost *:443>

	ServerAdmin webmaster@localhost
	DocumentRoot ".WWW_ROOT."

        <Directory />
                Options FollowSymLinks
                AllowOverride None
        </Directory>

	<Directory ".WWW_ROOT.">
                Options FollowSymLinks MultiViews
                AllowOverride All
                Order deny,allow
                allow from all
                Require all granted
	</Directory>

	ErrorLog \${APACHE_LOG_DIR}/error.log
	CustomLog \${APACHE_LOG_DIR}/access.log combined

        SSLEngine on
        SSLCipherSuite ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP:+eNULL
        SSLCertificateFile \"/etc/ssl/private/server.crt\"
        SSLCertificateKeyFile \"/etc/ssl/private/server.key\"

</VirtualHost>
"
?>
