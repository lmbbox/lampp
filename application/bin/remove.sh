#!/bin/bash

## Load conf file
source $(dirname "${BASH_SOURCE[0]}")/../config/app.conf

## Normalize pathes
TO=$(readlink -f "$SITES_ROOT/$1")

[[ "" != "$1" ]] \
&& [[ ! "$1" =~ (\.\.)|([^a-z0-9\-\.]) ]] \
&& [[ "$SITES_ROOT" == "$(dirname $TO)" ]] \
&& [[ -d "$TO" ]] \
&& rm -rf "$TO"
