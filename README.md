# Polylines

## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository. Finally, open a terminal and from this cloned respository's root run `docker-compose up -d --build`.

To set _composer_ and _npm_ libraries, enter the Docker container (`docker-compose exec app bash`) and run these commands:
- For _composer_: `composer update`. If there is a _composer.lock_ file present, then run `composer update --lock`
- For _npm_: `npm install`

Well done! Now we have the app running, but we need to set up the database and the styles:
- To set the database: `php artisan migrate`
- To compile the assets: `npm run dev`

Ready! Open up your browser of choice to [http://localhost:8000](http://localhost:8000) and you should see the app running as intended.

#### Containers and their ports (if used) are as follows:

- **web** - `:8000`
- **mysql** - `:3307`

### Installation issues

When installing new dependency via _composer_ and the app returns an error because of memory exhaustion, try adding the prefix `COMPOSER_MEMORY_LIMIT=-1` to the _composer_ command.

### Production deployment

When deploying in a production environment, _vendor_ directory must not contain development packages, so to follow this requirement the following commands must be executed:
- `composer install --no-dev`
- `php artisan vendor:cleanup`
- `php artisan vendor:minify`
