#!/bin/bash

## ToDo:
## Add support for other install locations

$LAMPP_ROOT="/usr/local/share/lampp"


## Clone repo to install location.
git clone --recursive "git://github.com/lmbbox/lampp.git" "$LAMPP_ROOT"


## Set correct folder/file permissions
chmod 777 "$LAMPP_ROOT/application/cache"
chmod 777 "$LAMPP_ROOT/application/logs"


## Copy sudoers file.
cp "$LAMPP_ROOT/application/config/sudoers" "/etc/sudoers.d/lampp"


# Setup new system user/group and set ownership
sudo useradd --system --home-dir "$LAMPP_ROOT" --shell /bin/false lampp
chown -R lampp:lampp "$LAMPP_ROOT"


## Added Apache site config, enable, and restart.
ln -s "$LAMPP_ROOT/application/config/apache.conf" "/etc/apache2/sites-available/lampp.conf"
ln -s "../sites-available/lampp.conf" "/etc/apache2/sites-enabled/000-lampp.conf"
service apache2 restart
