#!/bin/bash
#
# Copyright 2020-2021 EXXETA AG, Marius Schuppert
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program. If not, see <https://www.gnu.org/licenses/>.
set -euo pipefail

# NOTE: If you change this file, the wordpress container needs to be rebuilt

# run default wordpress entrypoint script

bash /usr/local/bin/docker-entrypoint.sh apache2-foreground &
# "wait" for the other script's finish. Note: the previous script does not terminate.
sleep 10

echo "Basic setup started. Starting WP Setup"

# variables
WP_PATH="/var/www/html"

# setup routine
wp --info
wp core config --path="$WP_PATH" --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=db \
  --dbprefix=dev_db_ --locale="de_DE" \
  --skip-check --force --extra-php <<PHP
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
PHP

wp core install --path="$WP_PATH" --url="http://127.0.0.1:8080" --title="WWF Plugin" --admin_user=admin --admin_password=password --admin_email="test@test.local"
wp option update timezone_string "Europe/Berlin"

wp config set DISABLE_WP_CRON true || true

# remove all plugins
wp plugin list

wp plugin deactivate --quiet akismet hello || true
wp plugin delete --quiet akismet hello || true
wp maintenance-mode deactivate || true

# install and activate woocommerce plugin
wp plugin install woocommerce --version=5.9.1
wp plugin install --activate debug-bar
wp plugin install --activate debug-bar-cron
wp plugin install --activate wp-mail-logging
wp plugin install --activate wp-crontrol

echo "php_value upload_max_filesize 20M">>/var/www/html/.htaccess

# link donations plugin to wp-content/plugins
if [ ! -L /var/www/html/wp-content/plugins/wwf-donations-plugin ]; then
  echo "creating symlink dir for plugin development"
  ln -s /var/www/wwf-donations-plugin/ /var/www/html/wp-content/plugins/wwf-donations-plugin
fi
# .. and activate it
wp plugin activate wwf-donations-plugin || true
# set proper theme
wp theme install --activate shophistic-lite

# Trap Ctrl-c
trap terminate INT

function terminate() {
  echo "Exiting now."
  exit 0
}
echo "Running forever (press Ctrl-C to stop the container) ..."
while(true); do
  sleep 5
done