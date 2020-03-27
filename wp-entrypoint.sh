#!/bin/bash
set -euo pipefail

# run default wordpress entrypoint script

bash /usr/local/bin/docker-entrypoint.sh apache2-foreground &
# wait for the other script's finish
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

wp core install --path="$WP_PATH" --url="http://127.0.0.1:8000" --title="WWF Plugin" --admin_user=admin --admin_password=password --admin_email="test@test.local"
# remove all plugins
wp plugin list
wp plugin deactivate --quiet akismet hello
wp plugin delete --quiet akismet hello
# install and activate woocommerce plugin
wp plugin install --activate woocommerce
wp plugin install --activate woocommerce-services
# link donations plugin to wp-content/plugins
ln -s /var/www/donations-plugin/ /var/www/html/wp-content/plugins/donations-plugin
# .. and activate it
wp plugin activate donations-plugin
# set proper theme
wp theme install --activate shophistic-lite

# Trap Ctrl-c
trap terminate INT

function terminate() {
  echo "Exiting now."
  exit 0
}
echo "Running forever (hit Ctrl-C to exit) ..."
while(true); do
  sleep 5
done