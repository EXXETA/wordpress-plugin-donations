#!/usr/bin/env bash

# check for required available commands of this script
# which zip &>/dev/null
# [ $? -eq 0 ] || echo "zip command not found."
which npm &>/dev/null
[ $? -eq 0 ] || echo "npm command not found."
which php &>/dev/null
[ $? -eq 0 ] || echo "php command not found."
which rm &>/dev/null
[ $? -eq 0 ] || echo "rm command not found."
which mkdir &>/dev/null
[ $? -eq 0 ] || echo "mkdir command not found."
which find &>/dev/null
[ $? -eq 0 ] || echo "find command not found."
which cp &>/dev/null
[ $? -eq 0 ] || echo "cp command not found."
which tar &>/dev/null
[ $? -eq 0 ] || echo "tar command not found."
which gzip &>/dev/null
[ $? -eq 0 ] || echo "gzip command not found."

set -eu

# execute unit tests of core lib
cd core
./vendor/phpunit/phpunit/phpunit test
cd -

cd wp/wwf-donations-plugin
# build js artifacts
npm i
npm run build-js
# this will copy over the core assets, too
npm run build:clean
cd -

# create empty release dir
if [ ! -d release ]; then
  mkdir release
else
  echo "removing release directory due to rebuild"
  rm -rf release
  mkdir release
fi

# copy project files
find wp/wwf-donations-plugin -type f -not -path '*/node_modules/*' -not -path '*/vendor/*' -not -path '*/wp-content/*' -exec cp -v --parents '{}' 'release/' \;

cd release/wwf-donations-plugin

# adjust composer path to the core lib as it is one additional level distant
#sed -i 's/..\/core/..\/..\/core/g' composer.json

php ../../composer.phar update --no-dev
rm package.json
rm package-lock.json
rm composer.lock

# remove js sources from release output
rm -rf src

# copy license and readme
cp ../../LICENSE .
cp ../../wp/README.md README_dev.md

# build archives
cd ..

# Uncomment the following lines to generate archives of the release

#zip -r wp-wwf-donations-plugin.zip wwf-donations-plugin
#tar -cvf wp-wwf-donations-plugin.tar wwf-donations-plugin
#gzip wp-wwf-donations-plugin.tar

echo "OK."