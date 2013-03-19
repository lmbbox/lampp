#!/bin/bash

## Clone repo to install location.
git clone --recursive git://github.com/lmbbox/lampp.git /usr/share/lampp

## Copy sudoers file.
cp /usr/share/lampp/application/config/sudoers /etc/sudoers.d/lampp

## Added Apache site config, enable, and restart.
ln -s /usr/share/lampp/application/config/apache.conf /etc/apache2/sites-available/lampp
a2ensite lampp
service apache2 restart
