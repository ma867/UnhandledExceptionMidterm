#!/bin/bash

versionNumber=1
if [ ! -d "$HOME/deployment/1" ];
then
	#Make directory for version 1
	mkdir "$HOME"/deployment/"$versionNumber"

	#Make currentVersion.txt, write current version number in the deployment folder
	touch "$HOME"/deployment/currentVersion.txt
	chmod 777 ~/deployment/currentVersion.txt
	echo "$versionNumber" > "$HOME"/deployment/currentVersion.txt

	#Copy from sample to version 1 folder
	cp -r /var/www/sample/* "$HOME"/deployment/"$versionNumber"
	rm "$HOME"/deployment/"$versionNumber"/testRabbitMQ.ini

	#Make currentVersion.txt and also write current version inside the folder: 1
	touch "$HOME"/deployment/"$versionNumber"/currentVersion.txt
	chmod 777 ~/deployment/1/currentVersion.txt
	echo "$versionNumber" > "$HOME"/deployment/1/currentVersion.txt

	#Make DevFileCount.txt, calculate amount of files in folder: 1 and copy it to DevFileCount.txt
	touch "$HOME"/deployment/"$versionNumber"/DevFileCount.txt
	devFile=("$HOME"/deployment/"$versionNumber"/*)
	chmod 777 "$HOME"/deployment/"$versionNumber"/DevFileCount.txt
	echo "${#devFile[@]}" > "$HOME"/deployment/"$currentVersionNumber"/DevFileCount.txt

	echo "Successfully created version 1"

else
	#Get current version form currentVersion.txt save it in a variable and add one
	file="$HOME/deployment/currentVersion.txt"
	currentVersionNumber=$(cat "$file")
	let "currentVersionNumber++"

	#Make directory for version number
	mkdir "$HOME"/deployment/"$currentVersionNumber"

	#Make currentVersion.txt, write current version number in the deployment folder
	echo "$currentVersionNumber" > "$HOME"/deployment/currentVersion.txt

	#Copy from sample to version number folder
	cp -r /var/www/sample/* "$HOME"/deployment/"$currentVersionNumber"
	rm "$HOME"/deployment/"$currentVersionNumber"/testRabbitMQ.ini

	#Make currentVersion.txt and also write current version inside the folder of the current version
	touch "$HOME"/deployment/"$currentVersionNumber"/currentVersion.txt
	chmod 777 ~/deployment/"$currentVersionNumber"/currentVersion.txt
	echo "$currentVersionNumber" > "$HOME"/deployment/"$currentVersionNumber"/currentVersion.txt

	#Make DevFileCount.txt, calculate amount of files in folder of number version and copy it to DevFileCount.txt
	touch "$HOME"/deployment/"$currentVersionNumber"/DevFileCount.txt
	devFile=("$HOME"/deployment/"$currentVersionNumber"/*)
	chmod 777 "$HOME"/deployment/"$currentVersionNumber"/DevFileCount.txt
	echo "${#devFile[@]}" > "$HOME"/deployment/"$currentVersionNumber"/DevFileCount.txt

	echo "Successfully created version $currentVersionNumber"
fi