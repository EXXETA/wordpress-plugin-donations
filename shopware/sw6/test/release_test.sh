#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

docker-compose down || true
docker-compose pull
docker-compose up -d

echo "Copy local plugin release files"
docker cp ../../../release/sw6/WWFDonationPlugin shopware6prod:/var/www/html/custom/plugins/

docker exec shopware6prod bash -c 'rm -rf /var/www/html/custom/plugins/DockwareSamplePlugin'

echo "Set correct permissions"
docker exec shopware6prod bash -c 'sudo chown -R www-data:www-data /var/www/html/custom'

echo "Install plugin"
sleep 10
docker exec shopware6prod bash -c 'bin/console plugin:refresh; bin/console plugin:install --activate WWFDonationPlugin; bin/console assets:install; bin/console cache:clear'
echo -e "\n Now have a look at 'http://localhost' (not: 127.0.0.1!)"
