#!/bin/bash
echo -n "Confirm the uninstall of Keexybox? [y/n] "
read confirm_uninstall
if [ "$confirm_uninstall" = "y" -o "$confirm_uninstall" = "Y" ]
then
	#. install.conf
    KEEXYBOX_HOME="/opt/keexybox"
	
	db_rc=0
	DATABASE_KEEXYBOX_USER=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config keexybox_db_config username)
	db_rc=$(expr $db_rc + $?)
	DATABASE_KEEXYBOX_PASSWORD=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config keexybox_db_config password)
	db_rc=$(expr $db_rc + $?)
	DATABASE_KEEXYBOX_DATABASE=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config keexybox_db_config database)
	db_rc=$(expr $db_rc + $?)

	DATABASE_KEEXYBOX_BLACKLIST_USER=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config blacklist_db_config username)
	db_rc=$(expr $db_rc + $?)
	DATABASE_KEEXYBOX_BLACKLIST_PASSWORD=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config blacklist_db_config password)
	db_rc=$(expr $db_rc + $?)
	DATABASE_KEEXYBOX_BLACKLIST_DATABASE=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config blacklist_db_config database)
	db_rc=$(expr $db_rc + $?)

	DATABASE_KEEXYBOX_LOGS_USER=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config logs_db_config username)
	db_rc=$(expr $db_rc + $?)
	DATABASE_KEEXYBOX_LOGS_PASSWORD=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config logs_db_config password)
	db_rc=$(expr $db_rc + $?)
	DATABASE_KEEXYBOX_LOGS_DATABASE=$(${KEEXYBOX_HOME}/keexyapp/bin/cake config get_db_config logs_db_config database)
	db_rc=$(expr $db_rc + $?)

	if [ "$db_rc" -ne 0 ]; then
		echo
		echo "We can not get all the information for the uninstallation."
		exit 1
	fi

	/etc/init.d/apache2 stop
	/etc/init.d/keexybox stop
	update-rc.d keexybox remove
	unlink /etc/init.d/keexybox
	
	echo "DROP DATABASE ${DATABASE_KEEXYBOX_DATABASE}" | mysql -u ${DATABASE_KEEXYBOX_USER} -p${DATABASE_KEEXYBOX_PASSWORD}
	echo "DROP DATABASE ${DATABASE_KEEXYBOX_BLACKLIST_DATABASE}" | mysql -u ${DATABASE_KEEXYBOX_BLACKLIST_USER} -p${DATABASE_KEEXYBOX_BLACKLIST_PASSWORD}
	echo "DROP DATABASE ${DATABASE_KEEXYBOX_LOGS_DATABASE}" | mysql -u ${DATABASE_KEEXYBOX_LOGS_USER} -p${DATABASE_KEEXYBOX_LOGS_PASSWORD}
	
	if [ "$KEEXYBOX_HOME" != "/" -o "$KEEXYBOX_HOME" != "" ]; then 
		echo "Do you confirm the deletion of the directory ${KEEXYBOX_HOME}? [y/n] "
		read confirm_delete_keexybox_dir
		if [ "$confirm_uninstall" = "y" -o "$confirm_uninstall" = "Y" ]; then
			rm -rf $KEEXYBOX_HOME
		else
			exit 1
		fi
	fi
else
	
    exit 0
fi


