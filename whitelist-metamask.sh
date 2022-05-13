#!/bin/bash

function valid_ip()
{
local ip=$1
local stat=1
if [[ $ip =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
OIFS=$IFS
IFS='.'
ip=($ip)
IFS=$OIFS
[[ ${ip[0]} -le 255 && ${ip[1]} -le 255 && ${ip[2]} -le 255 && ${ip[3]} -le 255 ]]
stat=$?
fi
return $stat
}

touch add.txt
echo "Add IP to the Whitelist (Right click or just type it in)"
ip=$1

if valid_ip $ip; then
stat='Good'
else
stat='Bad'
exit
fi

if grep -q $ip add.txt; then
echo "IP address already whitelisted"
else
printf "%-20s %s\n" $ip $stat
echo "Enter number of hours for which ip will be whitelisted"
hours=$2
if [ $hours -eq $hours 2>/dev/null -o $hours -eq 0 2>/dev/null ]; then
echo "Opening ports.."
touch add.txt
sudo ufw allow from $ip to any port 8000
sudo ufw reload
echo $ip $hours >> add.txt
echo "IP $ip succesfully added to the Whitelist"
fi
fi