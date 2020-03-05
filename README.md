# Wordpress plugin to collect donations for a non-profit organization

## Preinstall

### Run shop local

* Install Docker local [Docker get started](https://www.docker.com/get-started)
* Open root directory in cmd and run command `docker-compose up -d --remove-orphans`
* Check if container is running `docker container ls`
* Start shop via web browser `localhost:8000`

### Set up WordPress
* Select `Deutsch` as language
* Set following values and press `WordPress installieren` <br>
  ![alt text](https://github.com/adam-p/markdown-here/raw/master/src/common/images/icon48.png "Admin page")
* Log in with credentials from the previous step

### Set up WooCommerce
* Install WooCommerce plugin as described <br>
  ![alt text](https://github.com/adam-p/markdown-here/raw/master/src/common/images/icon48.png "Language")
* Activate plugin with Button `Aktivieren`
* Complete plugin settings, the values can be entered as desired 

### Set up shop theme
* Go to `Dashboard -> Design -> Themes -> Hinzufügen`
* Search for `Shophistic Lite` and install theme 
* After installation activate theme

## Shutdown and cleanup
* Open root directory in cmd and run command `docker-compose down --volumes`

## Links
* [Quickstart: Compose and WordPress](https://docs.docker.com/compose/wordpress)
* [Docker cheat sheet](https://www.docker.com/sites/default/files/d8/2019-09/docker-cheat-sheet.pdf)
* [Docker cheat sheet remove all](https://linuxize.com/post/how-to-remove-docker-images-containers-volumes-and-networks/)
