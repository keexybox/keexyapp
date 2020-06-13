#!/bin/bash
USER=keexybox
KEEXYBOX_HOME="/opt/keexybox"
chown -R $USER:$USER ${KEEXYBOX_HOME}
chmod go+w ${KEEXYBOX_HOME}/bind/etc/zones
# Set UID on iptables (instead of sudo that terminated with buffer over flow)
[[ -f "/sbin/xtables-multi" ]] && chmod u+s /sbin/xtables-multi
[[ -f "/usr/sbin/xtables-nft-multi" ]] && chmod u+s /usr/sbin/xtables-nft-multi

items=" 
/etc/ntp.conf
/etc/hostname
/etc/network/interfaces
/etc/apache2/sites-available/000-default.conf
/etc/apache2/envvars
/etc/apache2/ports.conf
/etc/logrotate.d/keexybox
/etc/wpa_supplicant/wpa_supplicant.conf
"

pidfiles="
${KEEXYBOX_HOME}/dhcpd/dhcpcd.pid
${KEEXYBOX_HOME}/bind/var/run/named/*.pid
${KEEXYBOX_HOME}/tor/var/run/tor.pid
${KEEXYBOX_HOME}/hostapd/var/run/hostapd.pid
/var/run/keexybox/keexybox.pid
/var/run/apache2/apache2.pid
"

# Add ACL for files that must be changed as keexybox user
for item in $items
do
    setfacl -b $item
    setfacl -R -m u:$USER:rw $item
    setfacl -R -d -m u:$USER:rw $item
done

chmod +x ${KEEXYBOX_HOME}/keexyapp/src/Shell/scripts/init_*
chmod +x ${KEEXYBOX_HOME}/keexyapp/src/Shell/scripts/*.sh

# Remove PID files that were not deleted before reboot
for pidfile in ${pidfiles}
do
    rm -f ${pidfile}
done

