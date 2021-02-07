UPDATE config SET value='20.10.2' WHERE param='version';
INSERT INTO `config` VALUES ('cportal_homepage_button_url', 'https://duckduckgo.com/', 'setting', 'Home page button URL to the Internet');
INSERT INTO `config` VALUES ('cportal_homepage_button_name', 'Browse the Internet', 'setting', 'Home page button name to the Internet');
INSERT INTO `config` VALUES ('cportal_check_tor_url', 'https://check.torproject.org', 'setting', 'URL to check if a user use Tor');
INSERT INTO `config` VALUES ('cportal_ip_info_url', 'http://ifconfig.co/', 'setting', 'URL to check Internet IP address and Finger print');
INSERT INTO `config` VALUES ('update_check_url', 'https://update.keexybox.org/check-update/arm/', 'setting', 'URL used to check KeexyBox Updates');
INSERT INTO `config` VALUES ('tmp_dir', '/opt/keexybox/tmp', 'dir_path', 'Temp directory used for KeexyBox updates');
INSERT INTO `config` VALUES ('bin_tee', '/usr/bin/tee', 'exec_file', 'Allow to send output of a job to file');
