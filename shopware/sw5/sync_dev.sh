#!/usr/bin/env bash
# the dockware container has to be started for this script to work!
set -e

echo "Sync this repository with dockware container and (re-)build afterwards..."
date +%c

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"
pwd

# download shopware-directories to local directory
docker exec shopware5 bash -c 'sudo chown -R www-data:www-data /var/www/html'
docker cp shopware5:/var/www/html/vendor ./src/ || true
docker cp shopware5:/var/www/html/bin ./src/
docker cp shopware5:/var/www/html/web ./src/
docker cp shopware5:/var/www/html/themes ./src/
docker cp shopware5:/var/www/html/engine ./src/
docker cp shopware5:/var/www/html/files ./src/

docker cp shopware5:/var/www/html/autoload.php ./src/
docker cp shopware5:/var/www/html/composer.json ./src/
docker cp shopware5:/var/www/html/composer.lock ./src/

# copy local plugin code to the container
docker cp ./src/refresh.sh shopware5:/var/www/html/refresh.sh
docker exec shopware5 bash -c "sudo chmod +x /var/www/html/refresh.sh"
docker cp ./src/config.php shopware5:/var/www/html/config.php
docker cp ./src/custom/plugins shopware5:/var/www/html/custom
# Set proper permissions
docker exec shopware5 bash -c 'sudo chown -R www-data:www-data /var/www/html'
docker exec shopware5 bash -c 'cd /var/www/html; bin/console sw:plugin:refresh; bin/console sw:plugin:install -v --activate WWFDonationPlugin'

date +%c
echo "OK."
