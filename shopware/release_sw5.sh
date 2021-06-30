#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

rm -rf ../release/sw5 || true
mkdir -p ../release/sw5

cd ./sw5/src/custom/plugins/WWFDonationPlugin
rm -rf vendor
php ../../../../../../composer.phar install --no-dev
cd "$dir"

cp -fr sw5/src/custom/plugins/WWFDonationPlugin ../release/sw5
cd ../release/sw5/WWFDonationPlugin

# cleanup release directory
rm assemble.sh

# Copy over LICENSE file
cp ../../../LICENSE .

cd ..

zip -r sw5-wwf-donations-plugin.zip WWFDonationPlugin
du -d0 -h sw5-wwf-donations-plugin.zip

cd "$dir/sw5"
docker-compose down || true
