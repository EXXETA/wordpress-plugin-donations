#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

docker-compose down || true
docker-compose pull
docker-compose up -d

echo "Copy local plugin release files"
docker cp ../../../release/sw5/WWFDonationPlugin shopware5prod:/var/www/html/custom/plugins/

echo "Set correct permissions"
docker exec shopware5prod bash -c 'sudo chown -R www-data:www-data /var/www/html/custom'

echo "Install plugin"
sleep 10
docker exec shopware5prod bash -c 'cd /var/www/html; bin/console sw:plugin:refresh; bin/console sw:plugin:install -v --activate --clear-cache WWFDonationPlugin'
docker exec shopware5prod bash -c 'cd /var/www/html; bin/console sw:thumbnail:generate; bin/console sw:cache:clear'

echo -e "\n Now have a look at 'http://localhost' (not: 127.0.0.1!)"
