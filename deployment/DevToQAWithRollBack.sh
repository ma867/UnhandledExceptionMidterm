#!/bin/bash

###################################### Get Dev Version #########################################
scp melissa@192.168.2.7:/home/melissa/deployment/currentVersion.txt /home/melissa/deployment
file="$HOME/deployment/currentVersion.txt"
currentVersionNumberDEV=$(cat "$file")
echo "Current Dev version is $currentVersionNumberDEV"
sleep 3
clear

########################## Compare Version Numbers and Pass to QA ##################################
if [ $currentVersionNumberDEV -gt 1 ]
then
	cp -r /home/melissa/deployment/currentVersion/* /home/melissa/deployment/previousVersion 
	echo "current version has been copied into previous version folder"	
	sleep 3
	
	scp -r melissa@192.168.2.7:/home/melissa/deployment/"$currentVersionNumberDEV"/* /home/melissa/deployment/currentVersion/
	echo "File Transfer Successful. QA Starting in 5 seconds"
	sleep 5
	
elif [ $currentVersionNumberDEV -eq 1 ]
then
	
	scp -r melissa@192.168.2.7:/home/melissa/deployment/"$currentVersionNumberDEV"/* /home/melissa/deployment/currentVersion/
	echo "files copied to current version folder for the first time"
	sleep 3


	scp -r melissa@192.168.2.7:/home/melissa/deployment/"$currentVersionNumberDEV"/* /home/melissa/deployment/previousVersion/
	echo "files copied to previous version folder for the first time"	
	echo "File Transfer Successful. QA Starting in 5 seconds"
	sleep 5

fi	







################# QA Test Procedure (with QA Rollback if QA fails) ###############################
qaSuccess=false

## QA Test Setup ##
previousVersion=false

devFileCount=$(cat "/home/melissa/deployment/currentVersion/DevFileCount.txt")
qaFiles=(/home/melissa/deployment/currentVersion/*)
qaFilesCount=${#qaFiles[@]}

echo "qa filecount is $qaFilesCount . and dev file count in $devFileCount"
while [ $qaSuccess ]
do
	clear
	echo "Starting QA testing"
	## QA ##
	if [ ${devFileCount} -eq ${qaFilesCount} ]
	then
		echo "QA Test Successful. Ready for Deployment"
		qaSuccess=true
		break

	elif [ ${devFileCount} -ne ${qaFilesCount} && $currentVersionNumberDEV -gt 1 ]
	then
		devFileCount=$(cat "/home/melissa/deployment/previousVersion/DevFileCount.txt")
		qaFiles=(/home/melissa/deployment/previousVersion/*)
		qaFilesCount=${#qaFiles[@]}

		previousVersion=true
		break
	
	elif [ ${devFileCount} -ne ${qaFilesCount} && $currentVersionNumberDEV -eq 1 ]
	then
		echo "Fix ya' code fams"
		break
	fi
	## End of QA ##
done
##############copy to production ##################


if [ $previousVersion == true ]
then
	#fix	
	sed -i 's/192.168.2.4/192.168.2.10/g' /home/melissa/deployment/previousVersion/ApplicationFunctions.php
	sed -i 's/192.168.2.4/192.168.2.10/g' /home/melissa/deployment/currentVersion/ApplicationFunctions.php
	cp -r /home/melissa/deployment/previousVersion/* /var/www/sample
	scp -r /home/melissa/deployment/previousVersion/* smit@192.168.2.9:/var/www/sample
	
	sed -i 's/192.168.2.10/192.168.2.13/g' /home/melissa/deployment/previousVersion/ApplicationFunctions.php
	sed -i 's/192.168.2.10/192.168.2.13/g' /home/melissa/deployment/currentVersion/ApplicationFunctions.php
	scp -r /home/melissa/deployment/previousVersion/* melissa@192.168.2.14:/home/melissa/deployment
	scp -r /home/melissa/deployment/previousVersion/* melissa@192.168.2.14:/var/www/sample	
	scp -r /home/melissa/deployment/currentVersion/* smit@192.168.2.12:/var/www/sample
	echo "Previous version copied to production."

elif [ $previousVersion == false ]
then
	#fix
	sed -i 's/192.168.2.4/192.168.2.10/g' /home/melissa/deployment/previousVersion/ApplicationFunctions.php
	sed -i 's/192.168.2.4/192.168.2.10/g' /home/melissa/deployment/currentVersion/ApplicationFunctions.php
	cp -r /home/melissa/deployment/currentVersion/* /var/www/sample
	scp -r /home/melissa/deployment/currentVersion/* smit@192.168.2.9:/var/www/sample

	sed -i 's/192.168.2.10/192.168.2.13/g' /home/melissa/deployment/previousVersion/ApplicationFunctions.php
	sed -i 's/192.168.2.10/192.168.2.13/g' /home/melissa/deployment/currentVersion/ApplicationFunctions.php
	scp -r /home/melissa/deployment/currentVersion/* melissa@192.168.2.14:/home/melissa/deployment
	scp -r /home/melissa/deployment/currentVersion/* melissa@192.168.2.14:/var/www/sample
	scp -r /home/melissa/deployment/currentVersion/* smit@192.168.2.12:/var/www/sample
	echo "New version copied to production."
fi