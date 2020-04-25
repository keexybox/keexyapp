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
"# If you just change the port or add more ports here, you will likely also
# have to change the VirtualHost statement in
# /etc/apache2/sites-enabled/000-default.conf

# KEEXYBOX GUI ADMIN PORT
Listen $this->apache_admin_port
Listen $this->apache_admin_https_port
# KEEXYBOX REDIRECT PORT
Listen $this->apache_redirect_port
# KEEXYBOX DENIED ACCESS HTTP PORTS
Listen $this->apache_denied_access_http_port
Listen $this->apache_denied_access_http_nolog_port

# KEEXYBOX DENIED ACCESS HTTPS PORTS
<IfModule ssl_module>
	Listen $this->apache_denied_access_https_port
	Listen $this->apache_denied_access_https_nolog_port
</IfModule>

# vim: syntax=apache ts=4 sw=4 sts=4 sr noet
"
?>
