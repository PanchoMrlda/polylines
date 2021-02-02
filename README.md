# Polylines
A simply E-Commerce Software. Features Provided:

- Products
- Cart
- Checkout
- Categories
- Customers
- Orders
- Payment
- Couriers
- Employees

## Usage

To get started, make sure you have [Docker installed](https://docs.docker.com/docker-for-mac/install/) on your system, and then clone this repository. Finally, open a terminal and from this cloned respository's root run `docker-compose up -d --build`.

To set _composer_ and _npm_ libraries, enter the Docker container (`docker-compose exec app bash`) and run these commands:
- For _composer_: `composer update`
- For _npm_: `npm install`

Well done! Now we have the app running, but we need to set up the database and the styles:
- To set the database: `php artisan migrate`
- To compile the assets: `npm run dev`

Ready! Open up your browser of choice to [http://localhost:8081](http://localhost:8081) and you should see the app running as intended.

#### Containers created and their ports (if used) are as follows:

- **webserver** - `:8081`
- **mysql** - `:3306`
- **app** - `:9000`
