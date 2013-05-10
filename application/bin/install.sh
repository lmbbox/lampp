#!/bin/bash

## Clone repo to install location.
git clone --recursive git://github.com/lmbbox/lampp.git /usr/local/share/lampp

## Set correct folder/file permissions
chmod 777 /usr/local/share/lampp/application/cache
chmod 777 /usr/local/share/lampp/application/logs

## Copy sudoers file.
cp /usr/local/share/lampp/application/config/sudoers /etc/sudoers.d/lampp

## Added Apache site config, enable, and restart.
ln -s /usr/local/share/lampp/application/config/apache.conf /etc/apache2/sites-available/lampp
a2ensite lampp
service apache2 restart
