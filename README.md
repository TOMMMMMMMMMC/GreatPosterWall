## Gazelle Development

### Docker (Recommended)

Install Docker for your preferred system and run the following
command:

```shell
docker-compose up -d
```

This will build and pull the needed images to run Gazelle on Debian
Buster. A volume is mounted from the base of the git repository at
`/var/www` in the container. Changes to the source code are
immediately served without rebuilding or restarting.


At this point, you should be able to browse to the tracker at `http://localhost:9000` and access phpMyAdmin at `http://localhost:9001`

You may want to install additional packages:

* `apt update`
* `apt install less procps vim`

If you want to poke around inside the web container, open a shell:

`export WEBCONT=$(docker ps|awk '$2 ~ /web$/ {print $1}')`

`docker exec -it $WEBCONT bash`

To keep an eye on PHP errors during development:

`docker exec -it $WEBCONT tail -n 20 -f /var/log/nginx/error.log`

To create a Phinx migration:

`docker exec -it $WEBCONT vendor/bin/phinx create MyNewMigration`

Edit the resulting file and then apply it:

`docker exec -it $WEBCONT vendor/bin/phinx migrate`

To access the database, save the following in `~root/.my.cnf` of
the database container:

```
    [mysql]
    user = root
    password = <sekret>
    database = gazelle
```

And then:

`docker exec -it $(docker ps|awk '$2 ~ /^mariadb/ {print $1}') mysql`

In the same vein, you can use `mysqldump` to perform a backup.

#### Boris

You can run Boris directly:

`docker exec -it $WEBCONT /var/www/boris`

#### Production Mode (not fully baked yet)

In order to have Docker run the container using the production mode commands
for both Composer and NPM, run this when powering it up:

`ENV=prod docker-compose up`
