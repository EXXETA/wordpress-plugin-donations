FROM wordpress:5.7.2-apache

# installing wp-cli and mysql client
RUN apt-get update; apt-get install --no-install-recommends -y libcap2-bin mariadb-client-10.3 less bash dos2unix vim; rm -rf /var/lib/apt/lists/*; \
 curl -o /bin/wp-cli.phar https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar; \
 chmod +x /bin/wp-cli.phar; \
 mv /bin/wp-cli.phar /usr/local/bin/wp; \
 # create a user of group www-data (apache web server user)
 useradd -m -g www-data wordpresser

# allow apache2 web server to bind to privileged port as non-root user
# and set proper permissions for wordpress directory for file system changes
RUN setcap 'cap_net_bind_service=+ep' /usr/sbin/apache2; chown -R wordpresser:www-data /usr/src/wordpress; \
    echo "php_value upload_max_filesize 20M">>/var/www/html/.htaccess
COPY wp-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/wp-entrypoint.sh; chown wordpresser:www-data /usr/local/bin/wp-entrypoint.sh; \
    dos2unix /usr/local/bin/wp-entrypoint.sh; mkdir -p /var/www/wwf-donations-plugin;

VOLUME /var/www/wwf-donations-plugin

USER wordpresser
WORKDIR /var/www/html

ENTRYPOINT ["wp-entrypoint.sh"]

