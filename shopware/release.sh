#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

cd sw6

docker-compose down || true
docker-compose pull
docker-compose up -d

bash ./sync_dev.sh
mkdir -p sw6/src/custom/plugins2/
docker cp shopware:/var/www/html/custom/plugins sw6/src/custom/plugins2/

cd ..

rm -rf ../release/sw6
mkdir -p ../release/sw6

cd sw6/src/custom/plugins/WWFDonationPlugin/
rm -rf node_modules || true
rm -rf vendor || true

npm install
npm run clean
npm run build
php ../../../../../../composer.phar install --no-dev

cd "$dir"
cp -fr sw6/src/custom/plugins/WWFDonationPlugin ../release/sw6
cd ../release/sw6/WWFDonationPlugin

# cleanup release directory
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

zip -r sw6-wwf-donations-plugin.zip WWFDonationPlugin
du -d0 -h sw6-wwf-donations-plugin.zip
