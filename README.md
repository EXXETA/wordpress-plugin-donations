# Wordpress plugin to collect donations for a non-profit organization

### Run shop on your local machine
* Install *docker* and *docker-compose* local [Docker get started](https://www.docker.com/get-started)
* This project uses a custom wordpress image with predefined plugins, themes etc.
* Build the development wordpress container with `docker-compose build`
* Open root directory in cmd and run command `docker-compose up -d --remove-orphans`. Consider using `--force-recreate` in some cases.
* Note, that after 10 seconds the wordpress setup routine starts inside the wordpress container.
* Check if container is running `docker container ls` or `docker ps`
* Start shop via web browser [http://127.0.0.1:8000](http://127.0.0.1:8000). Append `/wp-admin` to URL for backend access.
* Be sure to run the WooCommerce plugin setup wizard once logged in 

### WordPress development setup information
* Locale: `de_DE`
* Backend user: `admin:password`
* Site Url `http://127.0.0.1:8000`
* Required plugins
    * Woocommerce
    * Woocommerce-Services
* Used Theme
    * Shophistic Lite

## Shutdown and cleanup
* Open root directory in cmd and run command `docker-compose down --volumes`.
 
**Warning:** This will remove the complete database and existing data!

## Links
* [Quickstart: Compose and WordPress](https://docs.docker.com/compose/wordpress)
* [Docker cheat sheet](https://www.docker.com/sites/default/files/d8/2019-09/docker-cheat-sheet.pdf)
* [Docker cheat sheet remove all](https://linuxize.com/post/how-to-remove-docker-images-containers-volumes-and-networks/)
