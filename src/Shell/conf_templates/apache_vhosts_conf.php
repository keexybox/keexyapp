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
"
# vHost to display blocked HTTPS domain/website
<VirtualHost *:$this->apache_denied_access_https_port>
	RewriteEngine On
	RewriteCond %{HTTP} !=on
	RewriteRule ^/?(.*) http://%{SERVER_NAME}/$1 [R,L]

	LogFormat \"%{Host}i %h %l %u %t \\\"%r\\\" %>s %O \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"\" combined-host
	ErrorLog $this->apache_log_dir/blocked-sites-error.log
	CustomLog $this->apache_log_dir/blocked-sites-access.log combined-host

	ErrorDocument 404 /index.html
	ErrorDocument 400 /index.html

    SSLEngine on
    SSLCipherSuite ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP:+eNULL
    SSLCertificateFile \"$this->ssl_crtfile\"
    SSLCertificateKeyFile \"$this->ssl_keyfile\"
</VirtualHost>

# vHost to display blocked HTTP domain/website
<VirtualHost *:$this->apache_denied_access_http_port>
	ServerAdmin webmaster@localhost
	DocumentRoot /opt/keexybox/keexyapp/webroot/error-pages

    <Directory />
           Options FollowSymLinks
           AllowOverride None
    </Directory>

	<Directory /opt/keexybox/keexyapp/webroot/error-pages>
                Options FollowSymLinks MultiViews
                AllowOverride None
                Order allow,deny
                allow from all
                Require all granted
	</Directory>

	LogFormat \"%{Host}i %h %l %u %t \\\"%r\\\" %>s %O \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"\" combined-host

	ErrorLog $this->apache_log_dir/blocked-sites-error.log
	CustomLog $this->apache_log_dir/blocked-sites-access.log combined-host

	ErrorDocument 404 /index.html
	ErrorDocument 400 /index.html
</VirtualHost>

# vHost to redirect none connected users to captive portal
<VirtualHost *:$this->apache_denied_access_http_port>
    # Android Captive Portal Check domains
    Servername connectivitycheck.gstatic.com
    ServerAlias clients1.google.com
    ServerAlias clients3.google.com
    ServerAlias connect.rom.miui.com

    # Windows Captive Portal Check domains
    ServerAlias www.msftconnecttest.com
    ServerAlias msftconnecttest.com

    # Apple Captive Portal Check domains
    ServerAlias captive.apple.com
    ServerAlias airport.us
    ServerAlias thinkdifferent.us

    ServerAdmin webmaster@localhost

	RewriteEngine On
	RewriteCond %{SERVER_PORT} !^$this->apache_admin_port$
	RewriteRule ^/?(.*)/ http://$this->host_ip_output:$this->apache_admin_port/$1 [L,R=302]

	LogFormat \"%{Host}i %h %l %u %t \\\"%r\\\" %>s %O \\\"%{Referer}i\\\" \\\"%{User-Agent}i\\\"\" combined-host

    ErrorLog $this->apache_log_dir/captive-portal-error.log
    CustomLog $this->apache_log_dir/captive-portal-access.log combined-host
</VirtualHost>

# vHost for HTTP KeexyBox admin interface
<VirtualHost *:$this->apache_admin_port>
	ServerAdmin webmaster@localhost
	DocumentRoot /opt/keexybox/keexyapp/webroot

    <Directory />
        Options FollowSymLinks
        AllowOverride None
    </Directory>

	<Directory /opt/keexybox/keexyapp/webroot>
        Options FollowSymLinks MultiViews
        AllowOverride All
        Order deny,allow
        allow from all
        Require all granted
	</Directory>

    php_value post_max_size 50M
    php_value upload_max_filesize 50M

	ErrorLog $this->apache_log_dir/keexybox_error.log
	CustomLog $this->apache_log_dir/keexybox_access.log combined
</VirtualHost>

# vHost for HTTPS KeexyBox admin interface
<VirtualHost *:$this->apache_admin_https_port>
	ServerAdmin webmaster@localhost
	DocumentRoot /opt/keexybox/keexyapp/webroot

    <Directory />
        Options FollowSymLinks
        AllowOverride None
    </Directory>

	<Directory /opt/keexybox/keexyapp/webroot>
        Options FollowSymLinks MultiViews
        AllowOverride All
        Order deny,allow
        allow from all
        Require all granted
	</Directory>

    php_value post_max_size 50M
    php_value upload_max_filesize 50M

    SSLEngine on
    SSLCipherSuite ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP:+eNULL
    SSLCertificateFile \"$this->ssl_crtfile\"
    SSLCertificateKeyFile \"$this->ssl_keyfile\"

	ErrorLog $this->apache_log_dir/keexybox_error.log
	CustomLog $this->apache_log_dir/keexybox_access.log combined
</VirtualHost>
"
?>
