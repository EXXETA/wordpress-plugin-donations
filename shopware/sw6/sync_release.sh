#!/usr/bin/env bash
# the dockware container has to be started for this script to work!
set -e

echo "Sync this repository with dockware container and (re-)build afterwards..."
date +%c

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"
pwd

# download shopware-directories to local directory
# copy local plugin code to the container
docker exec shopware bash -c 'rm -rf /var/www/html/custom/plugins/DockwareSamplePlugin'
docker cp ./src/custom/plugins shopware:/var/www/html/custom
# Set proper permissions
docker exec shopware bash -c 'sudo chown -R www-data:www-data /var/www/html/custom'
docker exec shopware bash -c 'cd /var/www/html; bin/console plugin:refresh; bin/console plugin:install --activate WWFDonationPlugin'

# build everything
echo "Starting shopware 6 build process..."
docker exec shopware bash -c "bash /var/www/html/bin/build.sh"

date +%c
echo "OK."
