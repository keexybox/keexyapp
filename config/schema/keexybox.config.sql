-- MySQL dump 10.17  Distrib 10.3.22-MariaDB, for debian-linux-gnueabihf (armv8l)
--
-- Host: localhost    Database: keexybox
-- ------------------------------------------------------
-- Server version	10.3.22-MariaDB-0+deb10u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `param` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `type` varchar(25) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`param`),
  UNIQUE KEY `variable` (`param`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES ('apache_admin_https_port','8003','tcpip_port','Keexybox admin HTTPS port'),('apache_admin_port','8001','tcpip_port','HTTP port to keexybox admin'),('apache_denied_access_https_nolog_port','4431','tcpip_port','HTTPS port pointing to apache config with logs disabled'),('apache_denied_access_https_port','4430','tcpip_port','HTTPS port pointing to apache config with logs enabled'),('apache_denied_access_http_nolog_port','8081','tcpip_port','HTTP port pointing to apache config with logs disabled'),('apache_denied_access_http_port','8080','tcpip_port','HTTP port pointing to apache config with logs enabled'),('apache_envvars_conf_file','/etc/apache2/envvars','config_file','Apache2 envvars configuration file'),('apache_init','/etc/init.d/apache2','exec_file','Script to start/stop Apache'),('apache_log_dir','/var/log/apache2/','dir_path',''),('apache_ports_conf_file','/etc/apache2/ports.conf','config_file','apache2 ports.conf'),('apache_redirect_port','8002','tcpip_port','HTTP port for captive portal redirection'),('apache_vhosts_conf_file','/etc/apache2/sites-available/000-default.conf','config_file','Défault apache2 vhosts configuration file'),('arp_scan_oui_file','/opt/keexybox/keexyapp/src/Shell/scripts/ieee-oui.txt','config_file','Ethernet vendor OUI file for arp-scan'),('arp_scan_refresh_delay','600','setting','not used'),('bind9_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_bind','exec_file','Script to start/stop Bind'),('bind_root_dir','/opt/keexybox/bind/','dir_path',''),('bind_use_redirectors','0','setting','Tell if bind have to use host_dns1 and  host_dns2 as redirectors '),('bin_arp','/usr/sbin/arp','exec_file','Tool used to scan devices on the LAN'),('bin_arpscan','/usr/sbin/arp-scan','exec_file','Tool used to scan devices on the LAN'),('bin_cut','/usr/bin/cut','exec_file','bash util'),('bin_date','/bin/date','exec_file','You should know what it is ;)'),('bin_echo','/bin/echo','exec_file','bash util'),('bin_get-oui','/usr/bin/get-oui','exec_file_unused','command to update oui-file for arpscan'),('bin_grep','/bin/grep','exec_file','bash util'),('bin_halt','/sbin/halt','exec_file','Used to poweroff keexybox'),('bin_hwclock','/sbin/hwclock','exec_file','Tool to set clock on hardware device'),('bin_iptables','/sbin/iptables','exec_file','Iptables command to load website routing and ipfilters'),('bin_iptables_save','/sbin/iptables-save','exec_file','Command to save Iptables config'),('bin_mysql','/usr/bin/mysql','exec_file',''),('bin_nmap','/usr/bin/nmap','exec_file_unused','Tool to scan ports'),('bin_openssl','/usr/bin/openssl','exec_file','Command to generate certificate'),('bin_ping','/bin/ping','exec_file',''),('bin_python','/usr/bin/python','exec_file','Used to run python script like Blacklist or log import'),('bin_reboot','/sbin/reboot','exec_file','Used to reboot keexybox'),('bin_sudo','/usr/bin/sudo','exec_file','Used by keexybox user to run command as root'),('bin_sysctl','/sbin/sysctl','exec_file','Used to enable ipv4 forwarding'),('connection_default_time','1209600','setting','Default connection duration (minutes)'),('connection_max_time','10080','setting','Max connection duration can be set by user (minutes)'),('dhcp_conffile','/opt/keexybox/dhcpd/etc/dhcpd.conf','config_file',''),('dhcp_enabled','0','setting','Enable/Disable DHCP. set 1 no enable.'),('dhcp_end_ip','192.168.1.200','ipv4','First IP delivered by DHCP'),('dhcp_end_ip_input','192.168.2.200','ipv4','Last IP delivered by DHCP for input network'),('dhcp_end_ip_output','192.168.1.200','ipv4','Last IP delivered by DHCP for output network'),('dhcp_external','0','setting','Use DHCP for ouput network only.'),('dhcp_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_dhcpd','exec_file','Script to start/stop DHCPd'),('dhcp_reservations_conffile','/opt/keexybox/dhcpd/etc/dhcpd-reservations.conf','config_file','Reserved IP for DHCP configuration file'),('dhcp_start_ip','192.168.1.150','ipv4','Last IP delivered by DHCP'),('dhcp_start_ip_input','192.168.2.150','ipv4','First IP delivered by DHCP for input network'),('dhcp_start_ip_output','192.168.1.150','ipv4','First IP delivered by DHCP for output network'),('dns_expiration_delay','604800','setting','Delay that keexybox DNS should be renew. This cache is used for website routing.'),('hostapd_auth_algs','1','setting','Enable Authentication for Access Point'),('hostapd_bridge','br0','setting','Used Bridge interface name for Access Point'),('hostapd_channel','11','setting','Frequency channel for Access Point'),('hostapd_country_code','US','setting','Country Code of Access Point'),('hostapd_hw_mode','g','setting','Wifi mode for Access Point (a = IEEE 802.11a, b = IEEE 802.11b, g = IEEE 802.11g)'),('hostapd_ignore_broadcast_ssid','0','setting','Broadcast SSID Access Point (0=yes 1=No)'),('hostapd_interface','wlan0','setting','Used Wifi interface for Access Point'),('hostapd_macaddr_acl','0','setting','macaddr_acl'),('hostapd_rsn_pairwise','CCMP','setting','Accepted cipher suites for Access Point'),('hostapd_ssid','KeexyBox_2','setting','SSID of Access Point'),('hostapd_wmm_enabled','1','setting','wmm_enabled'),('hostapd_wpa','2','setting','WPA setting for Access Point'),('hostapd_wpa_key_mgmt','WPA-PSK','setting','Accepted key management algorithms for Access Point'),('hostapd_wpa_pairwise','TKIP','setting','Accepted cipher suites for Access Point'),('hostapd_wpa_passphrase','KeexyBox974','setting','WPA passphrase of Access Point'),('hostname_conffile','/etc/hostname','config_file','File that contains hostname'),('host_dns1','192.168.1.1','ipv4','Keexybox primary DNS'),('host_dns2','','ipv4','Keexybox secondary DNS'),('host_gateway','192.168.1.1','ipv4','IP address of internet router'),('host_interface','eth0','setting','Keexybox network interface name'),('host_interface_input','eth0:0','setting','Host internal interface name'),('host_interface_output','eth0','setting','Host external interface name'),('host_ip','192.168.1.251','ipv4','Keexybox address'),('host_ip_input','192.168.2.253','ipv4','Host ip address for internal network'),('host_ip_output','192.168.1.253','ipv4','Host ip address for external network'),('host_name','kxbv2','setting','Keexybox device hostname'),('host_netmask','255.255.255.0','ipv4','Keexybox netmask'),('host_netmask_input','255.255.255.0','ipv4','Host netmask for internal network'),('host_netmask_output','255.255.255.0','ipv4','Host netmask for external network'),('host_ntp1','0.debian.pool.ntp.org','setting','Keexybox NTP server'),('host_ntp2','1.debian.pool.ntp.org','setting','Keexybox NTP server'),('host_timezone','Etc/GMT','setting','Keexybox Timezone'),('keexyboxlogs','/opt/keexybox/logs/','dir_path',''),('keexybox_root_dir','/opt/keexybox','dir_path','Root directory of Keexybox'),('locale','en_US','setting','Default locale'),('logrotate_conf_file','/etc/logrotate.d/keexybox','config_file','Keexybox logrotate configuration file'),('log_db_retention','31','setting','Log retention in database (days)'),('log_import_schedule_time','02:00','setting','Time to import logs to database (HH:MM)'),('log_retention','150','setting','log files retention (days)'),('named_port','5300','tcpip_port','DNS port pointing to bind9 configuration with logs enabled'),('named_port_nolog','5302','tcpip_port','DNS port pointing to bind9 configuration with logs disabled'),('named_port_portal','5305','tcpip_port','bind port for fake DNS root to redirect all domains to Keexybox IP for captive portal.'),('named_port_tor','5301','tcpip_port','DNS port pointing to bind9 configuration with logs enabled'),('named_port_tor_nolog','5303','tcpip_port','DNS port pointing to bind9/ttdnsd configuration with logs disabled'),('network_conffile','/etc/network/interfaces','config_file',''),('network_init','/etc/init.d/networking','exec_file','Script to start/stop network'),('nic_path','/sys/class/net/','dir_path','Path of network interfaces'),('ntp_conffile','/etc/ntp.conf','config_file',''),('ntp_init','/etc/init.d/ntp','exec_file','Script to start/stop NTP'),('privoxy_confdir','/opt/keexybox/privoxy/etc','dir_path_unused','Path to privoxy config directory'),('privoxy_conffile','/opt/keexybox/privoxy/etc/config','config_file_unused',''),('privoxy_port','8118','tcpip_port_unused','Privoxy port'),('redsocks_conffile','/opt/keexybox/redsocks/etc/redsocks.conf','config_file_unused',''),('redsocks_local_port','12345','tcpip_port_unused','Port that Redsocks listen on.'),('redsocks_proxy_ip','127.0.0.1','ipv4_unused','IP used by redsocks to redirect traffic to privoxy'),('redsocks_proxy_port','8118','tcpip_port_unused','Port redsocks redirect traffic to privoxy. Should be the same as privoxy port.'),('redsocks_type','http-connect','setting_unused','redsocks type of redirect'),('rndc_port','9530','tcpip_port','Management DNS port pointing to bind9 configuration with logs enabled'),('rndc_port_nolog','9532','tcpip_port','Management DNS port pointing to bind9 configuration with logs disabled'),('rndc_port_portal','9535','tcpip_port','rndc port to manage fake DNS root.'),('rndc_port_tor','9531','tcpip_port','Management DNS port pointing to bind9/ttdnsd configuration with logs enabled'),('rndc_port_tor_nolog','9533','tcpip_port','Management DNS port pointing to bind9/ttdnsd configuration with logs disabled'),('run_wizard','0','setting','Enable wizard to setup Keexybox'),('scripts_dir','/opt/keexybox/keexyapp/src/Shell/scripts/','dir_path','Keexybox script directory'),('ssl_crtfile','/opt/keexybox/ssl/keexybox.crt','config_file','Certificate file'),('ssl_csr_c','FR','setting','Certificate Country Code'),('ssl_csr_cn','keexybox.keexybox','setting','Certificate Common Name'),('ssl_csr_file','/opt/keexybox/ssl/keexybox.csr','config_file','Certificate Signing Request file'),('ssl_csr_l','Somewhere','setting','Certificate City'),('ssl_csr_o','Keexybox','setting','Certificate Organization'),('ssl_csr_ou','Home','setting','Certificate Organization Unit'),('ssl_csr_st','Some-State','setting','Certificate State'),('ssl_keyfile','/opt/keexybox/ssl/keexybox.key','config_file','Certificate Key file'),('ssl_keysize','2048','setting','Certificate Keysize'),('ssl_validity','3650','setting','Certificate Validity (days)'),('sudoers_conf_file','/etc/sudoers','config_file',''),('sudoers_keexybox_conf_file','/etc/sudoers.d/keexybox','config_file',''),('sudo_init','/etc/init.d/sudo','exec_file','Script to start/stop Sudo'),('tor_conffile','/opt/keexybox/tor/etc/tor/torrc','config_file',''),('tor_control_password','16:2E36A3EF9D6FC0B960943CB6EEF66FF62C2BD458B0EE0EF2FDDADCAEA6','setting_unused','unused'),('tor_control_port','9051','tcpip_port_unused','unused'),('tor_dns_port','9053','tcpip_port','TOR DNSport'),('tor_host','127.0.0.1','ipv4','IP address Tor is listen on'),('tor_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_tor','exec_file','Script to start/stop Tor'),('tor_port','9050','tcpip_port','Port Tor is listen on'),('tor_trans_port','9040','tcpip_port','Tor transparent port'),('ttdnsd_defaults_conffile','/etc/default/ttdnsd','config_file_unused',''),('ttdnsd_dns1','8.8.8.8','ipv4_unused','DNS used by Tor exit node to resolv fqdn'),('ttdnsd_dns2',' 8.8.4.4','ipv4_unused','DNS used by Tor exit node to resolv fqdn'),('ttdnsd_dns_conffile','/etc/ttdnsd.conf','config_file_unused',''),('ttdnsd_host','127.0.0.1','ipv4_unused','IP address Ttdnsd is listen on'),('ttdnsd_init','/etc/init.d/ttdnsd','exec_file_unused','Script to start/stop Ttdnsd'),('ttdnsd_port','5304','tcpip_port_unused','Port Ttdnsd is listen on'),('ttdnsd_tsocks_conffile','/var/lib/ttdnsd/tsocks.conf','config_file_unused',''),('version','20.04.2','setting','Keexybox version'),('wpa_config_file','/etc/wpa_supplicant/wpa_supplicant.conf','config_file','Wi-Fi Protected Access config file');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-08 13:39:39
