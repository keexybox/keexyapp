-- MySQL dump 10.17  Distrib 10.3.23-MariaDB, for debian-linux-gnueabihf (armv7l)
--
-- Host: localhost    Database: keexybox.config
-- ------------------------------------------------------
-- Server version	10.3.23-MariaDB-0+deb10u1

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
-- MySQL dump 10.17  Distrib 10.3.23-MariaDB, for debian-linux-gnueabihf (armv7l)
--
-- Host: localhost    Database: keexybox
-- ------------------------------------------------------
-- Server version	10.3.23-MariaDB-0+deb10u1

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
  `value` varchar(20000) DEFAULT NULL,
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
INSERT INTO `config` VALUES ('apache_admin_https_port','8003','tcpip_port','Keexybox admin HTTPS port'),('apache_admin_port','8001','tcpip_port','HTTP port to keexybox admin'),('apache_denied_access_https_nolog_port','4431','tcpip_port','HTTPS port pointing to apache config with logs disabled'),('apache_denied_access_https_port','4430','tcpip_port','HTTPS port pointing to apache config with logs enabled'),('apache_denied_access_http_nolog_port','8081','tcpip_port','HTTP port pointing to apache config with logs disabled'),('apache_denied_access_http_port','8080','tcpip_port','HTTP port pointing to apache config with logs enabled'),('apache_envvars_conf_file','/etc/apache2/envvars','config_file','Apache2 envvars configuration file'),('apache_init','/etc/init.d/apache2','exec_file','Script to start/stop Apache'),('apache_log_dir','/var/log/apache2/','dir_path',''),('apache_ports_conf_file','/etc/apache2/ports.conf','config_file','apache2 ports.conf'),('apache_redirect_port','8002','tcpip_port','HTTP port for captive portal redirection'),('apache_vhosts_conf_file','/etc/apache2/sites-available/000-default.conf','config_file','Défault apache2 vhosts configuration file'),('arp_scan_oui_file','/opt/keexybox/keexyapp/src/Shell/scripts/ieee-oui.txt','config_file','Ethernet vendor OUI file for arp-scan'),('arp_scan_refresh_delay','600','setting','not used'),('bind9_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_bind','exec_file','Script to start/stop Bind'),('bind_root_dir','/opt/keexybox/bind/','dir_path',''),('bind_use_redirectors','0','setting','Tell if bind have to use host_dns1 and  host_dns2 as redirectors '),('bin_arp','/usr/sbin/arp','exec_file','Tool used to scan devices on the LAN'),('bin_arpscan','/usr/sbin/arp-scan','exec_file','Tool used to scan devices on the LAN'),('bin_cut','/usr/bin/cut','exec_file','bash util'),('bin_date','/bin/date','exec_file','You should know what it is ;)'),('bin_echo','/bin/echo','exec_file','bash util'),('bin_get-oui','/usr/bin/get-oui','exec_file_unused','command to update oui-file for arpscan'),('bin_grep','/bin/grep','exec_file','bash util'),('bin_halt','/sbin/halt','exec_file','Used to poweroff keexybox'),('bin_hwclock','/sbin/hwclock','exec_file','Tool to set clock on hardware device'),('bin_iptables','/sbin/iptables','exec_file','Iptables command to load website routing and ipfilters'),('bin_iptables_save','/sbin/iptables-save','exec_file','Command to save Iptables config'),('bin_mysql','/usr/bin/mysql','exec_file',''),('bin_nmap','/usr/bin/nmap','exec_file_unused','Tool to scan ports'),('bin_openssl','/usr/bin/openssl','exec_file','Command to generate certificate'),('bin_ping','/bin/ping','exec_file',''),('bin_python','/usr/bin/python','exec_file','Used to run python script like Blacklist or log import'),('bin_reboot','/sbin/reboot','exec_file','Used to reboot keexybox'),('bin_sudo','/usr/bin/sudo','exec_file','Used by keexybox user to run command as root'),('bin_sysctl','/sbin/sysctl','exec_file','Used to enable ipv4 forwarding'),('connection_default_time','1209600','setting','Default connection duration (minutes)'),('connection_max_time','10080','setting','Max connection duration can be set by user (minutes)'),('cportal_default_profile_id','1','setting','Profile to use for user registration'),('cportal_default_user_id','1','setting','User to use for fast login'),('cportal_record_mac','1','setting','Record Mac address of the device when users connects to the Internet.'),('cportal_record_useragent','1','setting','Record UserAgent information when users connects to the Internet.'),('cportal_register_allowed','0','setting','Allows the user to register himself on Captive Portal - 0=disable, 1=enable, 2=allow internet without registration'),('cportal_register_code','REGCODE','setting','Code to allow user to register himself on Captive Portal'),('cportal_register_expiration','31','setting','Number of days until account expires when register'),('cportal_terms','<p>\r\n</p><h3>\r\nInternet Access Terms and Conditions<br></h3>\r\n<div>\r\n<div>\r\n<div>\r\n<p><small><b>Terms and Conditions</b></small></p><p><small>By\r\n using our internet service, you hereby expressly acknowledge and agree \r\nthat there are significant security, privacy and confidentiality risks \r\ninherent in accessing or transmitting information through the internet, \r\nwhether the connection is facilitated through wired or wireless \r\ntechnology. Security issues include, without limitation, interception of\r\n transmissions, loss of data, and the introduction or viruses and other \r\nprograms that can corrupt or damage your computer.</small></p><p><small>Accordingly,\r\n you agree that the owner and/or provider of this network is NOT liable \r\nfor any interception or transmissions, computer worms or viruses, loss \r\nof data, file corruption, hacking or damage to your computer or other \r\ndevices that result from the transmission or download of information or \r\nmaterials through the internet service provided.</small></p><p><small>Use\r\n of the wireless network is subject to the general restrictions outlined\r\n below. If abnormal, illegal, or unauthorized behavior is detected, \r\nincluding heavy consumption of bandwidth, the network provider reserves \r\nthe right to permanently disconnect the offending device from the \r\nwireless network.</small></p><p><small><b>Examples of Illegal Uses</b></small></p><small>The following are representative examples only and do not comprise a comprehensive list of illegal uses:</small><ol><li><small>Spamming\r\n and invasion of privacy - Sending of unsolicited bulk and/or commercial\r\n messages over the Internet using the Service or using the Service for \r\nactivities that invade another\'s privacy.</small></li><li><small>Intellectual\r\n property right violations - Engaging in any activity that infringes or \r\nmisappropriates the intellectual property rights of others, including \r\npatents, copyrights, trademarks, service marks, trade secrets, or any \r\nother proprietary right of any third party.</small></li><li><small>Accessing\r\n illegally or without authorization computers, accounts, equipment or \r\nnetworks belonging to another party, or attempting to \r\npenetrate/circumvent security measures of another system. This includes \r\nany activity that may be used as a precursor to an attempted system \r\npenetration, including, but not limited to, port scans, stealth scans, \r\nor other information gathering activity.</small></li><li><small>The transfer of technology, software, or other materials in violation of applicable export laws and regulations.</small></li><li><small>Export Control Violations</small></li><li><small>Using\r\n the Service in violation of applicable law and regulation, including, \r\nbut not limited to, advertising, transmitting, or otherwise making \r\navailable ponzi schemes, pyramid schemes, fraudulently charging credit \r\ncards, pirating software, or making fraudulent offers to sell or buy \r\nproducts, items, or services.</small></li><li><small>Uttering threats;</small></li><li><small>Distribution of pornographic materials to minors;</small></li><li><small>and Child pornography.</small></li></ol><p><small><b>Examples of Unacceptable Uses</b></small></p><p><small>The following are representative examples only and do not comprise a comprehensive list of unacceptable uses:</small></p><ol><li><small>High bandwidth operations, such as large file transfers and media sharing with peer-to-peer programs (i.e.torrents)</small></li><li><small>Obscene or indecent speech or materials</small></li><li><small>Defamatory or abusive language</small></li><li><small>Using\r\n the Service to transmit, post, upload, or otherwise making available \r\ndefamatory, harassing, abusive, or threatening material or language that\r\n encourages bodily harm, destruction of property or harasses another.</small></li><li><small>Forging or misrepresenting message headers, whether in whole or in part, to mask the originator of the message.</small></li><li><small>Facilitating a Violation of these Terms of Use</small></li><li><small>Hacking</small></li><li><small>Distribution of Internet viruses, Trojan horses, or other destructive activities</small></li><li><small>Distributing\r\n information regarding the creation of and sending Internet viruses, \r\nworms, Trojan horses, pinging, flooding, mail-bombing, or denial of \r\nservice attacks. Also, activities that disrupt the use of or interfere \r\nwith the ability of others to effectively use the node or any connected \r\nnetwork, system, service, or equipment.</small></li><li><small>Advertising,\r\n transmitting, or otherwise making available any software product, \r\nproduct, or service that is designed to violate these Terms of Use, \r\nwhich includes the facilitation of the means to spam, initiation of \r\npinging, flooding, mail-bombing, denial of service attacks, and piracy \r\nof software.</small></li><li><small>The sale, transfer, or \r\nrental of the Service to customers, clients or other third parties, \r\neither directly or as part of a service or product created for resale.</small></li><li><small>Seeking information on passwords or data belonging to another user.</small></li><li><small>Making unauthorized copies of proprietary software, or offering unauthorized copies of proprietary software to others.</small></li><li><small>Intercepting or examining the content of messages, files or communications in transit on a data network.</small></li></ol></div><div><small>(source: <a target=\"_blank\" rel=\"nofollow\" href=\"https://sites.google.com/site/wifitermsgeneric/\">https://sites.google.com/site/wifitermsgeneric/</a>)<br></small><p></p>\r\n\r\n</div></div></div><p></p>','setting','Text of Terms and conditions of Internet Access to display to users.'),('dhcp_conffile','/opt/keexybox/dhcpd/etc/dhcpd.conf','config_file',''),('dhcp_enabled','0','setting','Enable/Disable DHCP. set 1 no enable.'),('dhcp_end_ip','192.168.1.200','ipv4','First IP delivered by DHCP'),('dhcp_end_ip_input','192.168.2.201','ipv4','Last IP delivered by DHCP for input network'),('dhcp_end_ip_output','192.168.1.201','ipv4','Last IP delivered by DHCP for output network'),('dhcp_external','0','setting','Use DHCP for ouput network only.'),('dhcp_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_dhcpd','exec_file','Script to start/stop DHCPd'),('dhcp_reservations_conffile','/opt/keexybox/dhcpd/etc/dhcpd-reservations.conf','config_file','Reserved IP for DHCP configuration file'),('dhcp_start_ip','192.168.1.150','ipv4','Last IP delivered by DHCP'),('dhcp_start_ip_input','192.168.2.151','ipv4','First IP delivered by DHCP for input network'),('dhcp_start_ip_output','192.168.1.151','ipv4','First IP delivered by DHCP for output network'),('dns_expiration_delay','604800','setting','Delay that keexybox DNS should be renew. This cache is used for website routing.'),('hostapd_ap_isolate','1','setting','Isolation between connected clients (1=yes 0=no)'),('hostapd_auth_algs','1','setting','Enable Authentication for Access Point'),('hostapd_bridge','br0','setting','Used Bridge interface name for Access Point'),('hostapd_bridge_ports','eth0','setting','Wired bridging interface'),('hostapd_channel','8','setting','Frequency channel for Access Point'),('hostapd_conf_file','/opt/keexybox/hostapd/etc/hostapd.conf','config_file','Hostapd configuration file'),('hostapd_country_code','FR','setting','Country Code of Access Point'),('hostapd_enabled','0','setting','Enable Wifi Access Point (1=yes 0=no)'),('hostapd_host_interface_input_bak','eth0:0','setting','Used input interface before enabling hostapd'),('hostapd_host_interface_output_bak','eth0','setting','Used output interface before enabling hostapd'),('hostapd_hw_mode','g','setting','Wifi mode for Access Point (a = IEEE 802.11a, b = IEEE 802.11b, g = IEEE 802.11g)'),('hostapd_ignore_broadcast_ssid','0','setting','Broadcast SSID Access Point (0=yes 1=No)'),('hostapd_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_hostapd','exec_file','Script to start/stop hostapd'),('hostapd_interface','wlan0','setting','Used Wifi interface for Access Point'),('hostapd_macaddr_acl','0','setting','macaddr_acl'),('hostapd_rsn_pairwise','CCMP','setting','Accepted cipher suites for Access Point'),('hostapd_ssid','KeexyBox','setting','SSID of Access Point'),('hostapd_wmm_enabled','1','setting','wmm_enabled'),('hostapd_wpa','2','setting','WPA setting for Access Point'),('hostapd_wpa_key_mgmt','WPA-PSK','setting','Accepted key management algorithms for Access Point'),('hostapd_wpa_pairwise','TKIP','setting','Accepted cipher suites for Access Point'),('hostapd_wpa_passphrase','KeexyBox974','setting','WPA passphrase of Access Point'),('hostname_conffile','/etc/hostname','config_file','File that contains hostname'),('host_dns1','8.8.8.8','ipv4','Keexybox primary DNS'),('host_dns2','','ipv4','Keexybox secondary DNS'),('host_gateway','192.168.1.1','ipv4','IP address of internet router'),('host_interface','eth0','setting','Keexybox network interface name'),('host_interface_input','eth0:0','setting','Host internal interface name'),('host_interface_output','eth0','setting','Host external interface name'),('host_ip','192.168.1.251','ipv4','Keexybox address'),('host_ip_input','192.168.2.254','ipv4','Host ip address for internal network'),('host_ip_output','192.168.1.254','ipv4','Host ip address for external network'),('host_name','kxbv2','setting','Keexybox device hostname'),('host_netmask','255.255.255.0','ipv4','Keexybox netmask'),('host_netmask_input','255.255.255.0','ipv4','Host netmask for internal network'),('host_netmask_output','255.255.255.0','ipv4','Host netmask for external network'),('host_ntp1','0.debian.pool.ntp.org','setting','Keexybox NTP server'),('host_ntp2','1.debian.pool.ntp.org','setting','Keexybox NTP server'),('host_timezone','Indian/Reunion','setting','Keexybox Timezone'),('keexyboxlogs','/opt/keexybox/logs/','dir_path',''),('keexybox_root_dir','/opt/keexybox','dir_path','Root directory of Keexybox'),('locale','en_US','setting','Default locale'),('logrotate_conf_file','/etc/logrotate.d/keexybox','config_file','Keexybox logrotate configuration file'),('log_db_retention','31','setting','Log retention in database (days)'),('log_import_schedule_time','02:00','setting','Time to import logs to database (HH:MM)'),('log_retention','150','setting','log files retention (days)'),('named_port','5300','tcpip_port','DNS port pointing to bind9 configuration with logs enabled'),('named_port_nolog','5302','tcpip_port','DNS port pointing to bind9 configuration with logs disabled'),('named_port_portal','5305','tcpip_port','bind port for fake DNS root to redirect all domains to Keexybox IP for captive portal.'),('named_port_tor','5301','tcpip_port','DNS port pointing to bind9 configuration with logs enabled'),('named_port_tor_nolog','5303','tcpip_port','DNS port pointing to bind9/ttdnsd configuration with logs disabled'),('network_conffile','/etc/network/interfaces','config_file',''),('network_init','/etc/init.d/networking','exec_file','Script to start/stop network'),('nic_path','/sys/class/net/','dir_path','Path of network interfaces'),('ntp_conffile','/etc/ntp.conf','config_file',''),('ntp_init','/etc/init.d/ntp','exec_file','Script to start/stop NTP'),('privoxy_confdir','/opt/keexybox/privoxy/etc','dir_path_unused','Path to privoxy config directory'),('privoxy_conffile','/opt/keexybox/privoxy/etc/config','config_file_unused',''),('privoxy_port','8118','tcpip_port_unused','Privoxy port'),('redsocks_conffile','/opt/keexybox/redsocks/etc/redsocks.conf','config_file_unused',''),('redsocks_local_port','12345','tcpip_port_unused','Port that Redsocks listen on.'),('redsocks_proxy_ip','127.0.0.1','ipv4_unused','IP used by redsocks to redirect traffic to privoxy'),('redsocks_proxy_port','8118','tcpip_port_unused','Port redsocks redirect traffic to privoxy. Should be the same as privoxy port.'),('redsocks_type','http-connect','setting_unused','redsocks type of redirect'),('rndc_port','9530','tcpip_port','Management DNS port pointing to bind9 configuration with logs enabled'),('rndc_port_nolog','9532','tcpip_port','Management DNS port pointing to bind9 configuration with logs disabled'),('rndc_port_portal','9535','tcpip_port','rndc port to manage fake DNS root.'),('rndc_port_tor','9531','tcpip_port','Management DNS port pointing to bind9/ttdnsd configuration with logs enabled'),('rndc_port_tor_nolog','9533','tcpip_port','Management DNS port pointing to bind9/ttdnsd configuration with logs disabled'),('run_wizard','0','setting','Enable wizard to setup Keexybox'),('scripts_dir','/opt/keexybox/keexyapp/src/Shell/scripts/','dir_path','Keexybox script directory'),('ssl_crtfile','/opt/keexybox/ssl/keexybox.crt','config_file','Certificate file'),('ssl_csr_c','FR','setting','Certificate Country Code'),('ssl_csr_cn','keexybox.keexybox','setting','Certificate Common Name'),('ssl_csr_file','/opt/keexybox/ssl/keexybox.csr','config_file','Certificate Signing Request file'),('ssl_csr_l','Somewhere','setting','Certificate City'),('ssl_csr_o','Keexybox','setting','Certificate Organization'),('ssl_csr_ou','Home','setting','Certificate Organization Unit'),('ssl_csr_st','Some-State','setting','Certificate State'),('ssl_keyfile','/opt/keexybox/ssl/keexybox.key','config_file','Certificate Key file'),('ssl_keysize','2048','setting','Certificate Keysize'),('ssl_validity','3650','setting','Certificate Validity (days)'),('sudoers_conf_file','/etc/sudoers','config_file',''),('sudoers_keexybox_conf_file','/etc/sudoers.d/keexybox','config_file',''),('sudo_init','/etc/init.d/sudo','exec_file','Script to start/stop Sudo'),('tor_conffile','/opt/keexybox/tor/etc/tor/torrc','config_file',''),('tor_control_password','16:2E36A3EF9D6FC0B960943CB6EEF66FF62C2BD458B0EE0EF2FDDADCAEA6','setting_unused','unused'),('tor_control_port','9051','tcpip_port_unused','unused'),('tor_dns_port','9053','tcpip_port','TOR DNSport'),('tor_exitnodes_countries','{de}','setting','Force Tor Exit Nodes Countries.'),('tor_host','127.0.0.1','ipv4','IP address Tor is listen on'),('tor_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_tor','exec_file','Script to start/stop Tor'),('tor_port','9050','tcpip_port','Port Tor is listen on'),('tor_trans_port','9040','tcpip_port','Tor transparent port'),('ttdnsd_defaults_conffile','/etc/default/ttdnsd','config_file_unused',''),('ttdnsd_dns1','8.8.8.8','ipv4_unused','DNS used by Tor exit node to resolv fqdn'),('ttdnsd_dns2',' 8.8.4.4','ipv4_unused','DNS used by Tor exit node to resolv fqdn'),('ttdnsd_dns_conffile','/etc/ttdnsd.conf','config_file_unused',''),('ttdnsd_host','127.0.0.1','ipv4_unused','IP address Ttdnsd is listen on'),('ttdnsd_init','/etc/init.d/ttdnsd','exec_file_unused','Script to start/stop Ttdnsd'),('ttdnsd_port','5304','tcpip_port_unused','Port Ttdnsd is listen on'),('ttdnsd_tsocks_conffile','/var/lib/ttdnsd/tsocks.conf','config_file_unused',''),('version','20.10.1','setting','Keexybox version'),('wpa_config_file','/etc/wpa_supplicant/wpa_supplicant.conf','config_file','Wi-Fi Protected Access config file');
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

-- Dump completed on 2020-10-01 16:26:45
