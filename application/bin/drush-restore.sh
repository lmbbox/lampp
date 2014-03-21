#!/bin/bash

## Load conf file
source $(dirname "${BASH_SOURCE[0]}")/../config/app.conf

## Normalize pathes
SITENAME=$1
SITEPATH=$(readlink -f "$SITES_ROOT/$1")
TEMPLATE_URL=$2


## Do some checks
if [[ "" == "$TEMPLATE_URL" ]] || [[ "" == "$SITENAME" ]]
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
if [[ "$(ls -A "$SITEPATH/htdocs")" ]]
then
	echo "Site htdocs is not empty."
	exit 2
fi


## Get template
wget "$TEMPLATE_URL" -O /tmp/drush-restore-template.tar.gz

if [[ $? -ne 0 ]]
then
	echo "Failed to download template source file. Please make sure this server can reach the source."
	exit 3
fi


## Generate MySQL username, password, and database name
DB_NAME="$(echo $SITENAME | tr -d "[:space:][:punct:]" | head -c 32)"
DB_USER="$(echo $SITENAME | tr -d "[:space:][:punct:]" | head -c 16)"
DB_PASS="$(cat /dev/urandom | tr -cd "[:alnum:]" | head -c 32)"


## Restore drush archive
rm -r "$SITEPATH/htdocs"
drush archive-restore /tmp/drush-restore-template.tar.gz --db-su="$MYSQL_USER" --db-su-pw="$MYSQL_PASS" --destination="$SITEPATH/htdocs" --db-url="mysql://$DB_USER:$DB_PASS@$MYSQL_HOST/$DB_NAME"


## Fix ownership permission on restored files
chown $HTDOCS_CHOWN "$SITEPATH/htdocs"
chmod $HTDOCS_CHMOD "$SITEPATH/htdocs"
chown $HTDOCS_CHOWN "$SITEPATH/htdocs/sites"
chmod $HTDOCS_CHMOD "$SITEPATH/htdocs/sites"


## Delete template file
rm /tmp/drush-restore-template.tar.gz
