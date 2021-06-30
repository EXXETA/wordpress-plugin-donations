#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

cd sw5/test

# just to be sure its down
docker-compose down || true
cd ..

docker-compose down || true
docker-compose pull
docker-compose up -d

sleep 10 # give the container some time to startup (the db etc.)

rm -rf ../../release/sw5 || true
mkdir -p ../../release/sw5

cd src/custom/plugins/WWFDonationPlugin/
rm -rf node_modules || true
rm -rf vendor || true

npm install
npm run clean
npm run build
php ../../../../../../composer.phar install --no-dev

cd "$dir/sw5"

bash ./sync_release.sh

cd "$dir"

echo -e "\n\nBuild process finished. Retrieving release files..."
docker cp shopware:/var/www/html/custom/plugins/WWFDonationPlugin sw5/src/custom/plugins

cd "$dir"
cp -fr sw5/src/custom/plugins/WWFDonationPlugin ../release/sw5
cd ../release/sw5/WWFDonationPlugin

# cleanup release directory
rm -rf src/Resources/app/administration/static
rm .gitignore
rm assemble.sh
rm makefile
rm phpstan.neon
rm phpunit.xml
rm -rf node_modules
rm -rf tests
rm package.json
rm package-lock.json
rm README.md

# Copy over LICENSE file
cp ../../../LICENSE .

cd ..

zip -r sw5-wwf-donations-plugin.zip WWFDonationPlugin
du -d0 -h sw5-wwf-donations-plugin.zip

cd "$dir/sw5"
docker-compose down || true
