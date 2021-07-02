#!/usr/bin/env bash

# script to set up a development environment for all shop plugins contained in this project
echo "setting up development environment"

# check for required available commands of this script
which npm &>/dev/null
[ $? -eq 0 ] || echo "npm command not found."
which php &>/dev/null
[ $? -eq 0 ] || echo "php command not found."
which mkdir &>/dev/null
[ $? -eq 0 ] || echo "mkdir command not found."
which curl &>/dev/null
[ $? -eq 0 ] || echo "curl command not found."

set -eu

dir=$(cd -P -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)
cd "$dir"

# download composer
if [ ! -f composer.phar ]; then
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
  # atm only composer 1 is supported
  php composer-setup.php --1
  php -r "unlink('composer-setup.php');"
fi

# build banner core package for php composer
cd core
php ../composer.phar install
cd -

# assemble core assets
cd assets
npm i
npm run assemble
cd -

# shop-specific setup instructions follow here:
cd wp
# download wp-cli
if [ ! -f wp-cli.phar ]; then
  curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
fi
php wp-cli.phar --info

# download current wordpress
mkdir -p wp
cd wp
php ../wp-cli.phar core download || true
cd "$dir/wp"

# setup development environment
cd ./wwf-donations-plugin
php ../../composer.phar install || php ../../composer.phar dump-autoload || true

npm i
npm run build-js
npm run build:clean

cd "$dir"
bash ./shopware/setup.sh

echo "Setup OK."