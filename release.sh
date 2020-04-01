#!/usr/bin/env bash

# check for required available commands of this script
which zip &>/dev/null
[ $? -eq 0 ] || echo "zip command not found."
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

# build js artifacts
cd donations-plugin
npm i
npm run build
cd ..

# create empty release dir
if [ ! -d release ]; then
  mkdir release
else
  rm -rf release
  mkdir release
fi

# copy project files
find donations-plugin -type f -not -path '*/node_modules/*' -not -path '*/vendor/*' -not -path '*/wp-content/*' -exec cp -v --parents '{}' 'release/' \;

cd release/donations-plugin
php ../../composer.phar install --no-dev
rm package.json
rm package-lock.json
rm -rf src

# build archives
cd ..
zip -r wp-donations-plugin.zip donations-plugin
tar -cvf wp-donations-plugin.tar donations-plugin
gzip wp-donations-plugin.tar

echo "OK"