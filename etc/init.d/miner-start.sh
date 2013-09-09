#!/bin/bash
sudo /usr/bin/ntpdate -u pool.ntp.org
/usr/bin/screen -dmS cgminer /opt/minepeon/bin/cgminer -c /opt/minepeon/etc/miner.conf
