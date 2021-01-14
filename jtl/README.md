# JTL WWF Plugin Development

NOTE: The following container image configuration is suited for development purposes only!

### Build

- Download JTL Shop and Systemcheck into this directory. Ensure their names are:
    - shop-v5-0-0.zip
    - shop5-systemcheck-v5-0-0.zip

- Build container
    - `docker-compose build`

### Run

- Run: `docker-compose up`
- Stop: `docker-compose down`

A web server listens on your host at port 8080 TCP. To adjust ports you have to modify the docker-compose.yml file.

### Get a shell in the container

`docker exec -ti jtl-shop-dev bash`

## Container components

- Webserver: Apache 2.4 `8080`
- MariaDB `root:root` DB name: `jtl` port `3306`
- JTL 5.X + Systemcheck (`/systemcheck`)
- Adminer for direct DB access `/adminer`
- PimpMyLogs to see server and php logs `/pml`
- Mailhog at `:8025` with local SMTP at default port 25