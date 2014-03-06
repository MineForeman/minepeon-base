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
echo "# [b] Change console password          #"
echo "# [c] Stop Miner                       #"
echo "# [d] Start Miner                      #"
echo "# [e] Retart Miner                     #"
echo "# [f] Update MinePeon Web UI           #"
echo "# [g] Update MinePeon Configuration    #"
echo "# [h] Update ArchLinux                 #"
echo "# [z] LogOut                           #"
echo "# [0] Exit to shell                    #"
echo "########################################"
echo "#(Some options require your password)  #"
echo -n "Enter your menu choice [a-0]: "
read yourch
case $yourch in
a) /usr/bin/screen -r;;
b) /usr/bin/passwd ;;
c) /usr/bin/sudo /usr/bin/systemctl stop miner ;;
d) /usr/bin/sudo /usr/bin/systemctl start miner ;;
e) /usr/bin/sudo /usr/bin/systemctl restart miner ;;
f) /usr/bin/passwd ;;
g) /usr/bin/passwd ;;
h) /usr/bin/passwd ;;
z) logout ;;
0) exit 0;;
*) echo "Oopps!!! Please select choice 1,2,3 or 4";
echo "Press Enter to continue. . ." ; read ;;
esac
done

whoami
read -n 1
