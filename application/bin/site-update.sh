#!/bin/bash

## Load conf file
source "$(dirname "${BASH_SOURCE[0]}")/../config/app.conf"

## Normalize pathes
CURRENT="$1"
NEW="$2"
PHPVERSION="$3"
ALIASES="$4"


# Check if site root $CURRENT exists
if [[ ! -d "$SITES_ROOT/$CURRENT" ]]
then
	echo "The site does not exist."
	exit 1
fi

# Check if site root $NEW does not exists
if [[ "$CURRENT" != "$NEW" && -d "$SITES_ROOT/$NEW" ]]
then
	echo "The new site already exists."
	exit 1
fi


# Move site root and update config files
if [[ "$CURRENT" != "$NEW" ]]
then
	sudo mv "$SITES_ROOT/$CURRENT" "$SITES_ROOT/$NEW"
	sudo sed -i "s/$CURRENT/$NEW/" "$SITES_ROOT/$NEW/vhost.conf" "$SITES_ROOT/$NEW/cron"
	
	# Setup Apache Vhost file and enable site
	rm "/etc/apache2/sites-enabled/$CURRENT.conf"
	rm "/etc/apache2/sites-available/$CURRENT.conf"
	sudo ln -s "$SITES_ROOT/$NEW/vhost.conf" "/etc/apache2/sites-available/$NEW.conf"
	sudo ln -s "../sites-available/$NEW.conf" "/etc/apache2/sites-enabled/$NEW.conf"
	
	# Link Cron file
	rm "/etc/cron.d/${CURRENT//./-}"
	sudo ln -s "$SITES_ROOT/$NEW/cron" "/etc/cron.d/${NEW//./-}"
fi


# Set PHP Version
sudo sed -i "s/AddHandler php-fastcgi[0-9\.]*/AddHandler php-fastcgi$PHPVERSION/" "$SITES_ROOT/$NEW/vhost.conf"

# Set aliases
sudo sed -Ei "s/ServerAlias (.*)/ServerAlias $ALIASES/" "$SITES_ROOT/$NEW/vhost.conf"


# Restart apache
sudo service apache2 restart