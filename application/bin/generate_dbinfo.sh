#!/bin/bash

## Load conf file
source $(dirname "${BASH_SOURCE[0]}")/../config/app.conf

## Normalize pathes
SITENAME=$1
SITEPATH=$(readlink -f "$SITES_ROOT/$1")


## Do some checks
if [[ "" == "$SITENAME" ]]
then
	echo "Missing required parameters."
	exit 5
fi
if [[ "$SITENAME" =~ (\.\.)|([^a-z0-9\-\.]) ]] || [[ "$SITES_ROOT" != "$(dirname $SITEPATH)" ]]
then
	echo "Invalid site name."
	exit 4
fi
if [[ ! -d "$SITEPATH" ]]
then
	echo "Site folder does not exist."
	exit 3
fi
if [[ -f "$SITEPATH/database.conf" ]]
then
	echo "Site already has database.conf file."
	exit 2
fi


## Generate MySQL username, password, and database name
DB_NAME="$(echo $SITENAME | tr -d "[:space:][:punct:]" | head -c 32)"
DB_USER="$(echo $SITENAME | tr -d "[:space:][:punct:]" | head -c 16)"
DB_PASS="$(cat /dev/urandom | tr -cd "[:alnum:]" | head -c 32)"


## Create new database and user
mysql -h $MYSQL_HOST -u $MYSQL_USER -p$MYSQL_PASS -e "CREATE DATABASE $DB_NAME DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci; GRANT ALL ON $DB_NAME.* TO $DB_USER@$MYSQL_HOST IDENTIFIED BY '$DB_PASS'; FLUSH PRIVILEGES;"


## Save database info to file
echo "host: $MYSQL_HOST" > "$SITEPATH/database.conf"
echo "user: $DB_USER" >> "$SITEPATH/database.conf"
echo "pass: $DB_PASS" >> "$SITEPATH/database.conf"
echo "db: $DB_NAME" >> "$SITEPATH/database.conf"
