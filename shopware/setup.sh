#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"
cd ./sw5/src/
php ./../../../composer1.phar install --no-scripts

# shopware 5 stuff
cd "$dir"
cd ./sw5/src/custom/plugins/WWFDonationPlugin
php ./../../../../../../composer1.phar install
bash assemble.sh

cd "$dir"
# shopware 6 stuff
cd ./sw6/src/
php ./../../../composer.phar clearcache
php ./../../../composer.phar install --no-scripts

cd "$dir"
cd ./sw6/src/custom/plugins/WWFDonationPlugin
php ./../../../../../../composer.phar clearcache
php ./../../../../../../composer.phar install
npm i
bash assemble.sh

