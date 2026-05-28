# B2B Trading API

This is a simple Laravel 11 API for wholesale trading.

It helps you manage:
- buying products from providers
- storing goods in warehouses
- selling products to clients
- handling refunds
- tracking stock and profit by batch

The project is written for a B2B workflow and the sample data is prepared with the Uzbek market in mind.

## What this project does

- `Purchases` create new stock batches
- `Refunds` return goods back to the provider
- `Available products` show what is ready to sell
- `Orders` sell goods using FIFO stock logic
- `Remaining stock` shows how much is still in storage
- `Batch profit` helps you see profit per batch

## Tech Stack

- PHP 8.2+
- Laravel 11
- MySQL or SQLite
- Laravel API resources and use cases

## API Endpoints

All endpoints are under `/api/v1`.

- `POST /purchases`
- `POST /batches/{batch}/refunds`
- `GET /products/available`
- `POST /orders`
- `GET /storage/remaining`
- `GET /batches/profit`

## Local Setup

1. Install PHP dependencies.
```bash
composer install
```

2. Copy or create your `.env` file.
```bash
cp .env.example .env
php artisan key:generate
```

3. Set your database settings in `.env`.

4. Create the database if you use MySQL.
```bash
mysql -uroot -e 'CREATE DATABASE IF NOT EXISTS b2b_trading CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
```

5. Run migrations.
```bash
php artisan migrate
```

6. Seed sample data.
```bash
php artisan db:seed
```

7. Start the server.
```bash
php artisan serve
```

8. Test the API.
```bash
curl http://127.0.0.1:8000/api/v1/products/available
```

9. If you also use frontend assets, install Node packages.
```bash
npm install
npm run dev
```

10. Start working with the API in Postman, Insomnia, or your own frontend.

11. Seeders for the Uzbek market.
Use the seeded demo data to test a real B2B scenario from Uzbekistan:
- providers like local importers or wholesale suppliers in Tashkent
- storages in Tashkent, Samarkand, and Namangan
- categories such as beverages, confectionery, household goods, and textile
- products with realistic Uzbek-style SKU codes
- clients like local shops, mini markets, and distributors

This makes testing easier because the data feels closer to how a real Uzbek wholesale business works.

## Seed Data

The default seeder creates:
- one demo admin user
- local-style providers
- warehouses
- categories and products
- clients with Uzbek business names

If you want to change the demo data, edit `database/seeders/DatabaseSeeder.php`.
## Notes

- FIFO is used when creating orders.
- Refunds are tracked separately from sales.
- Batch profit is calculated after refunds.
- The code is meant to stay simple and easy to extend.
