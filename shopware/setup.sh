#!/usr/bin/env bash

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

# shopware 5 stuff
cd ./sw5/src/custom/plugins/WWFDonationPlugin
php ./../../../../../../composer.phar install
bash assemble.sh

cd "$dir"
cd ./sw6/src/custom/plugins/WWFDonationPlugin
php ./../../../../../../composer.phar install
npm i
bash assemble.sh

