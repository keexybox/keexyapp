UPDATE config SET value='20.10.1' WHERE param='version';
ALTER TABLE `config` MODIFY value VARCHAR(20000);
INSERT INTO `config` VALUES ('hostapd_ap_isolate','1','setting','Isolation between connected clients (1=yes 0=no)');
INSERT INTO `config` VALUES ('hostapd_auth_algs','1','setting','Enable Authentication for Access Point');
INSERT INTO `config` VALUES ('hostapd_bridge','br0','setting','Used Bridge interface name for Access Point');
INSERT INTO `config` VALUES ('hostapd_bridge_ports','eth0','setting','Wired bridging interface');
INSERT INTO `config` VALUES ('hostapd_channel','8','setting','Frequency channel for Access Point');
INSERT INTO `config` VALUES ('hostapd_conf_file','/opt/keexybox/hostapd/etc/hostapd.conf','config_file','Hostapd configuration file');
INSERT INTO `config` VALUES ('hostapd_country_code','FR','setting','Country Code of Access Point');
INSERT INTO `config` VALUES ('hostapd_enabled','1','setting','Enable Wifi Access Point (1=yes 0=no)');
INSERT INTO `config` VALUES ('hostapd_host_interface_input_bak','eth0:0','setting','Used input interface before enabling hostapd');
INSERT INTO `config` VALUES ('hostapd_host_interface_output_bak','eth0','setting','Used output interface before enabling hostapd');
INSERT INTO `config` VALUES ('hostapd_hw_mode','g','setting','Wifi mode for Access Point (a = IEEE 802.11a, b = IEEE 802.11b, g = IEEE 802.11g)');
INSERT INTO `config` VALUES ('hostapd_ignore_broadcast_ssid','0','setting','Broadcast SSID Access Point (0=yes 1=No)');
INSERT INTO `config` VALUES ('hostapd_init','/opt/keexybox/keexyapp/src/Shell/scripts/init_hostapd','exec_file','Script to start/stop hostapd');
INSERT INTO `config` VALUES ('hostapd_interface','wlan0','setting','Used Wifi interface for Access Point');
INSERT INTO `config` VALUES ('hostapd_macaddr_acl','0','setting','macaddr_acl');
INSERT INTO `config` VALUES ('hostapd_rsn_pairwise','CCMP','setting','Accepted cipher suites for Access Point');
INSERT INTO `config` VALUES ('hostapd_ssid','KeexyBox-3','setting','SSID of Access Point');
INSERT INTO `config` VALUES ('hostapd_wmm_enabled','1','setting','wmm_enabled');
INSERT INTO `config` VALUES ('hostapd_wpa','2','setting','WPA setting for Access Point');
INSERT INTO `config` VALUES ('hostapd_wpa_key_mgmt','WPA-PSK','setting','Accepted key management algorithms for Access Point');
INSERT INTO `config` VALUES ('hostapd_wpa_pairwise','TKIP','setting','Accepted cipher suites for Access Point');
INSERT INTO `config` VALUES ('hostapd_wpa_passphrase','KeexyBox974','setting','WPA passphrase of Access Point');
INSERT INTO `config` VALUES ('cportal_default_profile_id', 1, 'setting', 'Profile to use for user registration');
INSERT INTO `config` VALUES ('cportal_default_user_id', 1, 'setting', 'User to use for fast login');
INSERT INTO `config` VALUES ('cportal_register_code', 'REGCODE', 'setting', 'Code to allow user to register himself on Captive Portal');
INSERT INTO `config` VALUES ('cportal_register_allowed', 0, 'setting', 'Allows the user to register himself on Captive Portal - 0=disable, 1=enable, 2=allow internet without registration');
INSERT INTO `config` VALUES ('cportal_register_expiration', 7, 'setting', 'Number of days until account expires when register');
INSERT INTO `config` VALUES ('cportal_record_useragent', 0, 'setting', 'Record UserAgent information when users connects to the Internet.');
INSERT INTO `config` VALUES ('cportal_record_mac', 0, 'setting', 'Record Mac address of the device when users connects to the Internet.');
INSERT INTO `config` VALUES ('cportal_terms', null, 'setting', 'Text of Terms and conditions of Internet Access to display to users.');
INSERT INTO `config` VALUES ('tor_exitnodes_countries', null, 'setting', 'Force Tor Exit Nodes Countries.');
ALTER TABLE `users` ADD COLUMN email VARCHAR(255) AFTER displayname;
ALTER TABLE `users` ADD COLUMN expiration datetime AFTER admin;
ALTER TABLE `users` ADD COLUMN lastlogin datetime AFTER expiration;
ALTER TABLE `actives_connections` ADD COLUMN client_details varchar(1000) AFTER mac;
ALTER TABLE `connections_history` ADD COLUMN client_details varchar(1000) AFTER mac;
