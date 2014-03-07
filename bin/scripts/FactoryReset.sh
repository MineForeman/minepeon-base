#!/bin/bash
# Factory Reset.  You peobably don't want to do this!

if [ "$UID" -ne 0 ]
  then echo "Please run as root, even then, You peobably don't want to do this!"
  exit
fi

read -p "Press enter key to continue or CTRL-C to abort"

find / -type f -iname '*.pacnew' -exec rm -f {} \+
find /var/ -type f -iname '*.1' -exec rm -f {} \+
find /var/ -type f -iname '*.log' -exec cp /dev/null {} \+

echo "" > /var/log/pacman.log
echo "" > /var/log/btmp
echo "" > /var/log/faillog
echo "" > /var/log/wtmp

rm -rf /root/.config
rm -rf /root/.bash_history
rm -rf /root/.wicd
rm -rf /root/.ssh
rm -rf /root/.g*

rm -rf /home/minepeon/*
rm -rf /home/minepeon/.bash_history
rm -rf /home/minepeon/.ssh
rm -rf /home/minepeon/.viminfo
rm -rf /home/minepeon/.wicd
rm -rf /home/minepeon/.g*

rm -rf /etc/ssh/ssh_host_rsa_key
rm -rf /etc/ssh/ssh_host_dsa_key
rm -rf /etc/ssh/ssh_host_ecdsa_key
rm -rf /etc/ssh/ssh_host_ecdsa_key.pub
rm -rf /etc/ssh/ssh_host_key.pub
rm -rf /etc/ssh/ssh_host_key
rm -rf /etc/ssh/ssh_host_rsa_key.pub
rm -rf /etc/ssh/sshd_config.pacnew
rm -rf /etc/ssh/ssh_host_dsa_key.pub

rm -rf /opt/minepeon/var/rrd/*.rrd
rm -rf /opt/minepeon/http/rrd/*.png
cp /opt/minepeon/var/sql/summary.db.clean /opt/minepeon/var/sql/summary.db
rm /opt/minepeon/DEBUG

dd if=/dev/zero of=/junk
sync
rm /junk

read -p "Press enter key to continue"
