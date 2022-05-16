#!/usr/bin/env bash
set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

cd "../../../../../../assets"
npm run assemble

cd "$dir"

# create directories
node_modules/.bin/shx mkdir -p "./src/Resources/app/administration/static"
node_modules/.bin/shx mkdir -p "./src/Resources/public/static"

# copy over sample images
node_modules/.bin/shx cp -fr "../../../../../../assets/dist/images/*" "./src/Resources/app/administration/static"
node_modules/.bin/shx cp -fr "../../../../../../assets/dist/images/*" "./src/Resources/public/static"
node_modules/.bin/shx cp -fr "../../../../../../assets/dist/sample-images/*" "./src/Resources/public/static"
node_modules/.bin/shx cp -fr "../../../../../../assets/dist/sample-images/*" "./src/Resources/app/administration/static"

node_modules/.bin/rimraf "./src/Resources/app/administration/static/*-s.png"
