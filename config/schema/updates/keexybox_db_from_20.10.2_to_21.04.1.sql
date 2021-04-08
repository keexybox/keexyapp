UPDATE config SET value='21.04.1' WHERE param='version';
ALTER TABLE `config` MODIFY description VARCHAR(300);
UPDATE config SET description='Bahavior of the Captive Portal - 0=Private (admin need to create accounts for users), 1=Registration (The user can create his account himself), 2=Free (Free access by accepting terms and conditions), 3=None (Captive portal is not needed to access the Internet)' WHERE param='cportal_register_allowed';
INSERT INTO `config` VALUES ('dhcp_enabled_input', '0', 'setting', 'Enable/Disable DHCP for input network. set 1 to enable.');
INSERT INTO `config` VALUES ('dhcp_enabled_output', '0', 'setting', 'Enable/Disable DHCP for output network. set 1 to enable.');
INSERT INTO `config` VALUES ('dhcp_interfaces_conffile', '/opt/keexybox/dhcpd/etc/interfaces.conf', 'config_file', 'Listening interfaces configuration file for DHCPd.');
UPDATE config SET value = (SELECT value FROM config WHERE param='dhcp_enabled' LIMIT 1) WHERE config.param = 'dhcp_enabled_input';
UPDATE config SET value = (SELECT value FROM config WHERE param='dhcp_enabled' LIMIT 1) WHERE config.param = 'dhcp_enabled_output';
UPDATE config SET value =
       IF(
         ((SELECT value FROM config WHERE param='dhcp_external' LIMIT 1) = 1 AND (SELECT value FROM config WHERE param='dhcp_enabled' LIMIT 1) = 1)
         OR
         ((SELECT value FROM config WHERE param='dhcp_external' LIMIT 1) = 1 AND (SELECT value FROM config WHERE param='dhcp_enabled' LIMIT 1) = 0)
         ,0, 1)
WHERE config.param = 'dhcp_enabled_input';
