#!/bin/bash

## Load conf file
source "$(dirname "${BASH_SOURCE[0]}")/../config/app.conf"

## Normalize pathes
TEMPLATE="$1"
DOMAIN="$2"
PHPVERSION="$3"
ALIASES="$4"


# Check if site root $TEMPLATE exists
if [[ ! -d "$SITES_ROOT/$TEMPLATE" || ! -d "$SITES_ROOT/$TEMPLATE/htdocs" || ! -f "$SITES_ROOT/$TEMPLATE/vhost.conf" || ! -f "$SITES_ROOT/$TEMPLATE/cron" ]]
then
	echo "The base site template does not exist or is missing files."
	exit 1
fi

# Check if site root $DOMAIN does not exists
if [[ -d "$SITES_ROOT/$DOMAIN" ]]
then
	echo "The new site already exists."
	exit 1
fi


# Copy base site root and update config files
cp -a "$SITES_ROOT/$TEMPLATE" "$SITES_ROOT/$DOMAIN"
sed -i "s/$TEMPLATE/$DOMAIN/" "$SITES_ROOT/$DOMAIN/vhost.conf" "$SITES_ROOT/$DOMAIN/cron"

# Set PHP Version
sed -i "s/AddHandler php-fastcgi[0-9\.]*/AddHandler php-fastcgi$PHPVERSION/" "$SITES_ROOT/$DOMAIN/vhost.conf"

# Set aliases
sed -Ei "s/ServerAlias (.*)/ServerAlias \1 $ALIASES/" "$SITES_ROOT/$DOMAIN/vhost.conf"


# Link Cron file
ln -s "$SITES_ROOT/$DOMAIN/cron" "/etc/cron.d/${DOMAIN//./-}"


# Setup Apache Vhost file and enable site
ln -s "$SITES_ROOT/$DOMAIN/vhost.conf" "/etc/apache2/sites-available/$DOMAIN.conf"
ln -s "../sites-available/$DOMAIN.conf" "/etc/apache2/sites-enabled/$DOMAIN.conf"


# Restart apache
service apache2 reload
