#!/bin/bash

## Load conf file
source $(dirname "${BASH_SOURCE[0]}")/../config/app.conf

## Normalize pathes
FROM=$(readlink -f "$SITES_ROOT/$1")
TO=$(readlink -f "$SITES_ROOT/$2")

[[ "" != "$1" ]] \
&& [[ "" != "$2" ]] \
&& [[ ! "$1" =~ (\.\.)|([^a-z0-9\-\.]) ]] \
&& [[ ! "$2" =~ (\.\.)|([^a-z0-9\-\.]) ]] \
&& [[ "$SITES_ROOT" == "$(dirname $FROM)" ]] \
&& [[ "$SITES_ROOT" == "$(dirname $TO)" ]] \
&& [[ -d "$FROM" ]] \
&& [[ ! -d "$TO" ]] \
&& mv "$FROM" "$TO"
