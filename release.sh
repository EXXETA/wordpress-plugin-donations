#!/usr/bin/env bash

# check for required available commands of this script
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

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

echo "Run unit tests of the core library"

cd core
./vendor/phpunit/phpunit/phpunit test
cd -

echo -e "\nbuild js and assemble assets into the plugin directory"

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

echo "Copy over plugin files..."
# copy project files
find wp/wwf-donations-plugin -type f -not -path '*/node_modules/*' -not -path '*/vendor/*' -not -path '*/wp-content/*' -exec cp -v --parents '{}' 'release/' \;

cd release/wp/wwf-donations-plugin

echo "Preparing release directory..."

# adjust composer path to the core lib as it is one additional level distant
sed -i 's/..\/..\/core/..\/..\/..\/core/g' composer.json

php ../../../composer.phar update --no-dev
rm package.json
rm package-lock.json
rm composer.lock

# remove js sources from release output
rm -rf src

# copy license and readme
cp ../../../LICENSE .
cp ../../../wp/README.md README_dev.md

# build archives
cd ..

# generate release archives
if [ -x "$(command -v zip)" ]; then
  zip -r wp-wwf-donations-plugin.zip wwf-donations-plugin
  if [ -x "$(command -v du)" ]; then
    du -d0 -h wp-wwf-donations-plugin.zip
  fi
fi
if [ -x "$(command -v tar)" ]; then
  tar -cvf wp-wwf-donations-plugin.tar wwf-donations-plugin
  if [ -x "$(command -v gzip)" ]; then
    gzip wp-wwf-donations-plugin.tar
  fi
fi

echo "Wordpress release finished"
cd "$dir"

echo "Starting Shopware release process."
bash ./shopware/release.sh

echo "Release OK. Generating release archive hashes..."

if [ -f "$dir/release/wp/wp-wwf-donations-plugin.zip" ]; then
  if [ -x "$(command -v sha256sum)" ]; then
    sha256sum --tag "$dir/release/wp/wp-wwf-donations-plugin.zip"
  fi
fi
if [ -f "$dir/release/sw5/sw5-wwf-donations-plugin.zip" ]; then
  if [ -x "$(command -v sha256sum)" ]; then
    sha256sum --tag "$dir/release/sw5/sw5-wwf-donations-plugin.zip"
  fi
fi
if [ -f "$dir/release/sw6/sw6-wwf-donations-plugin.zip" ]; then
  if [ -x "$(command -v sha256sum)" ]; then
    sha256sum --tag "$dir/release/sw6/sw6-wwf-donations-plugin.zip"
  fi
fi