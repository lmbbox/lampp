#!/bin/bash

## Load conf file
source "$(dirname "${BASH_SOURCE[0]}")/../config/app.conf"

## Normalize pathes
DOMAIN="$1"


# Check if site root $DOMAIN exists
if [[ ! -d "$SITES_ROOT/$DOMAIN" ]]
then
	echo "The site does not exist."
	exit 1
fi


# Delete site files and configs
rm "/etc/cron.d/${DOMAIN//./-}"
rm "/etc/apache2/sites-enabled/$DOMAIN.conf"
rm "/etc/apache2/sites-available/$DOMAIN.conf"
rm -rf "$SITES_ROOT/$DOMAIN"


# Restart apache
service apache2 reload
