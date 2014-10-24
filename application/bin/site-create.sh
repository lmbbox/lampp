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


# Copy base site root and update config files
sudo cp -a "$SITES_ROOT/$TEMPLATE" "$SITES_ROOT/$DOMAIN"
sudo sed -i "s/$TEMPLATE/$DOMAIN/" "$SITES_ROOT/$DOMAIN/vhost.conf" "$SITES_ROOT/$DOMAIN/cron"

# Set PHP Version
sudo sed -i "s/AddHandler php-fastcgi[0-9\.]*/AddHandler php-fastcgi$PHPVERSION/" "$SITES_ROOT/$DOMAIN/vhost.conf"

# Set aliases
sudo sed -Ei "s/ServerAlias (.*)/ServerAlias \1 $ALIASES/" "$SITES_ROOT/$DOMAIN/vhost.conf"


# Link Cron file
sudo ln -s "$SITES_ROOT/$DOMAIN/cron" "/etc/cron.d/${DOMAIN//./-}"


# Setup Apache Vhost file and enable site
sudo ln -s "$SITES_ROOT/$DOMAIN/vhost.conf" "/etc/apache2/sites-available/$DOMAIN.conf"
sudo ln -s "../sites-available/$DOMAIN.conf" "/etc/apache2/sites-enabled/$DOMAIN.conf"


# Restart apache
sudo service apache2 restart


# Generate MySQL username, password, and database name
dbname="$(echo $DOMAIN | tr -d "[:space:][:punct:]" | head -c 32)"
dbuser="$(echo $DOMAIN | tr -d "[:space:][:punct:]" | head -c 16)"
dbpass="$(cat /dev/urandom | tr -cd "[:alnum:]" | head -c 32)"

mysql -h $MYSQL_HOST -u $MYSQL_USER -p${MYSQL_PASS} -e "CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci; GRANT ALL ON $dbname.* TO $dbuser@localhost IDENTIFIED BY '$dbpass'; FLUSH PRIVILEGES;"

echo "host: $mysqlhost" > "$SITES_ROOT/$DOMAIN/database.conf"
echo "user: $dbuser" >> "$SITES_ROOT/$DOMAIN/database.conf"
echo "pass: $dbpass" >> "$SITES_ROOT/$DOMAIN/database.conf"
echo "db: $dbname" >> "$SITES_ROOT/$DOMAIN/database.conf"

echo "MySQL Database and User created. Details are in the file '$SITES_ROOT/$DOMAIN/database.conf'"