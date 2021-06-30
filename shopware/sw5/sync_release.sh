#!/usr/bin/env bash
# the dockware container has to be started for this script to work!
set -e

echo "Sync this repository with dockware sw 5 container and (re-)build afterwards..."
date +%c

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"
pwd

# download shopware-directories to local directory
# copy local plugin code to the container
docker cp ./src/custom/plugins shopware5:/var/www/html/custom

# Set proper permissions
docker exec shopware5 bash -c 'sudo chown -R www-data:www-data /var/www/html/custom'
docker exec shopware5 bash -c 'cd /var/www/html; bin/console sw:plugin:refresh; bin/console sw:plugin:install --activate --clear-cache WWFDonationPlugin; bin/console sw:thumbnail:generate; bin/console sw:cache:clear'

date +%c
echo "OK."
