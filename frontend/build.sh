#!/usr/bin/env bash

#Fix permissions
chown -R 1007:www-data /app

#Rm npm bower files
rm -rf bower_components/ node_modules/ package-lock.json

#Install dependencies
npm install --include=dev

# npm install -g bower@^1.8.14
# npm install -g gulp@^3.9.1

bower install --allow-root

#Build
gulp