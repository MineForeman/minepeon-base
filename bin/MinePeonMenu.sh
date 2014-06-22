#!/bin/bash
#
# Script to perform some common system operations
#
while :
do
clear
echo "########################################"
echo "# MinePeon Console Menu                #"
echo "########################################"
echo "# [a] Miner Screen (CTRL-A-D to exit)  #"
echo "# [s] Change console password          #"
echo "# [d] Stop Miner                       #"
echo "# [f] Start Miner                      #"
echo "# [g] Restart Miner                    #"
echo "# [z] Update MinePeon                  #"
echo "# [x] Update MinePeon Configuration    #"
echo "# [c] Update ArchLinux (MinePeon Base) #"
echo "# [v] Reboot MinePeon                  #"
echo "# [q] Exit to shell                    #"
echo "########################################"
echo "# [Some options require your password] #"
echo "# [ Exit to shell and type logout to ] #"
echo "# [           Exit System            ] #"
echo "########################################"
echo ""
echo -n "Enter your menu choice [a-0]: "
read yourch
case $yourch in
a) /usr/bin/screen -r;;
s) /usr/bin/passwd ;;
d) /usr/bin/sudo /usr/bin/systemctl stop miner ;;
f) /usr/bin/sudo /usr/bin/systemctl start miner ;;
g) /usr/bin/sudo /usr/bin/systemctl restart miner ;;
z) /opt/minepeon/bin/scripts/MinePeonUIUpdate.sh ;;
x) /opt/minepeon/bin/scripts/MinePeonConfigUpdate.sh ;;
c) /opt/minepeon/bin/scripts/ArchUpdate.sh ;;
z) /usr/bin/sudo /usr/bin/reboot ;;
q) exit 0;;
*) echo "Please select one of the menu items";
echo "Press Enter to continue. . ." ; read ;;
esac
done

