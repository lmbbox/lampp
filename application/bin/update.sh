#!/bin/bash

## Load conf file
source "$(dirname "${BASH_SOURCE[0]}")/../config/app.conf"


cd $LAMPP_ROOT

git stash
git pull
git submodule init
git submodule update
git stash pop
