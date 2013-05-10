# Linux Apache MySQL PHP Panel

LAMPP is a simple control panel for managing a LAMP configured server.

## Requirements

System running Ubuntu with Apache, MySQL, PHP, and phpMyAdmin (optional) installed.

See https://github.com/lmbbox/Ubuntu-Server-Deploy for an example installation.

## Install

As root run the command:

	wget -qO- https://raw.github.com/lmbbox/lampp/master/application/bin/install.sh | sh

## Update

Either run the update.sh script in the application/bin folder or click Update from the panel dashboard.

## Configuration

Adjust the application/config/app.php and application/config/app.conf files according to your environment.

## Usage



## ToDo

Make note on Site creation about RewriteBase required because of using VirtualDocumentRoot.

Build update script to pull from repo.

Import into Git and GitHub.

Create script to upgrade to PHP 5.4