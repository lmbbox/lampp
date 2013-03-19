#!/bin/bash

cd ../../

git stash
git pull
git submodule update
git stash pop
