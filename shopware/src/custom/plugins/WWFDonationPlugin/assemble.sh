#!/usr/bin/env bash
set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

cd "../../../../../assets"
npm run assemble
cd "../shopware/src/custom/plugins/WWFDonationPlugin"

# copy files
# TODO handle SCSS stuff
# shx cp -fr "../../../../../assets/dist/banner.css" .

# copy default banner images + icons
node_modules/.bin/shx cp -fr "../../../../../assets/dist/images/*" "./src/Resources/public/images/"

# copy over sample images
node_modules/.bin/shx cp -fr "../../../../../assets/dist/images/*" "./src/Resources/app/administration/static"
node_modules/.bin/shx cp -fr "../../../../../assets/dist/sample-images/*" "./src/Resources/app/administration/static"

# remove -s.png images, because we don't use them here
node_modules/.bin/rimraf "./src/Resources/app/administration/static/*-s.png"
