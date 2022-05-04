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
FROM wordpress:5-php8.0-apache

# installing wp-cli and mysql client
RUN apt-get update; apt-get install --no-install-recommends -y libcap2-bin mariadb-client-10.5 less bash dos2unix vim; rm -rf /var/lib/apt/lists/*; \
 curl -o /bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
 chmod +x /bin/wp-cli.phar; \
 mv /bin/wp-cli.phar /usr/local/bin/wp; \
 # create a user of group www-data (apache web server user)
 useradd -m -g www-data wordpresser

# allow apache2 web server to bind to privileged port as non-root user
# and set proper permissions for wordpress directory for file system changes
RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2; chown -R wordpresser:www-data /usr/src/wordpress;
COPY wp-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/wp-entrypoint.sh; chown wordpresser:www-data /usr/local/bin/wp-entrypoint.sh; \
    dos2unix /usr/local/bin/wp-entrypoint.sh; mkdir -p /var/www/wwf-donations-plugin;

VOLUME /var/www/wwf-donations-plugin

USER wordpresser
WORKDIR /var/www/html

ENTRYPOINT ["wp-entrypoint.sh"]

