UPDATE config SET value='20.10.2' WHERE param='version';
ALTER TABLE `config` MODIFY description VARCHAR(300);
UPDATE config SET description='Bahavior of the Captive Portal - 0=Private (admin need to create accounts for users), 1=Registration (The user can create his account himself), 2=Free (Free access by accepting terms and conditions), 3=None (Captive portal is not needed to access the Internet)' WHERE param='cportal_register_allowed';
