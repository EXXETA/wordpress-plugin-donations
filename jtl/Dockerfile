FROM debian:buster

# install system dependencies
RUN apt-get update \
    && apt-get upgrade -y --quiet --no-install-recommends \
    && apt-get install -y --no-install-recommends \
        supervisor git curl vim nano less unzip apache2 php php-zip php-curl php-pdo-mysql php-gd php-intl php-bcmath php-imagick php-dom composer \
        mariadb-client mariadb-server \
        golang sudo \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/www/html /var/log/supervisor /var/lock/apache2 /var/run/apache2 /gobin \
    && export GOPATH=/gobin && go get github.com/mailhog/MailHog

# setup mariadb
RUN mysql_install_db --user=mysql && service mysql start; \
    sleep 5; \
    mysqladmin -u root password 'root' \
    && mysql -u root -proot -e "create database jtl;" \
    && printf "root\n n\n y\n n\n y\n y\n" sudo mysql_secure_installation; \
    service mysql restart;

# FIXME delete apt cache
COPY shop5-systemcheck-v5-0-0.zip /var/www/html
COPY shop-v5-0-0.zip /var/www/html
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# change default web ports to non-root ports 8080 and 8443
RUN sed -i "/Listen 80/c\Listen 8080" /etc/apache2/ports.conf \
    && sed -i "/Listen 443/c\Listen 8443" /etc/apache2/ports.conf \
    && sed -i "172s/AllowOverride None/AllowOverride All/" /etc/apache2/apache2.conf \
    && sed -i "/upload_max_filesize/c\upload_max_filesize = 12M" /etc/php/7.3/apache2/php.ini \
    && sed -i "/max_execution_time/c\max_execution_time = 150" /etc/php/7.3/apache2/php.ini \
    && echo "error_log = /var/log/apache2/php_errors.log" >> /etc/php/7.3/apache2/php.ini \
    && a2enmod rewrite

# install pml
RUN cd /var/www/html; git clone https://github.com/potsky/PimpMyLog.git pml
COPY config.user.php /var/www/html/pml/config.user.php

# unzip jtl and delete archives
RUN rm /var/www/html/index.html; cd /var/www/html; unzip -q shop5-systemcheck-v5-0-0.zip; \
    unzip -q shop-v5-0-0.zip; rm shop5-systemcheck-v5-0-0.zip shop-v5-0-0.zip; \
    # install adminer + set file and dir permissions
    mkdir -p /var/www/html/adminer \
    && curl -L -o /var/www/html/adminer/index.php https://github.com/vrana/adminer/releases/download/v4.7.8/adminer-4.7.8.php \
    && chown -R www-data:www-data /var/www/html && chown -R www-data:www-data /var/log;

# webserver http port
EXPOSE 8080
#mariadb
EXPOSE 3306
#mailhog
EXPOSE 8025

VOLUME /var/www/html/plugins
VOLUME /var/lib/mysql

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

WORKDIR /var/www/html