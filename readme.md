# News Articles Test

## Installation

Install PHP dependencies:

```sh
composer install
```

Install NPM dependencies:

```sh
npm install
```

Build assets:

```sh
npm run dev
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Create an SQLite database. You can also use another database (MySQL, Postgres), simply update your configuration accordingly.

```sh
touch database/database.sqlite
```

Run database migrations:

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Run artisan server:

```sh
php artisan serve
```

- **Username:** johndoe@example.com
- **Password:** secret

Configure Crontab for Scheduler

```sh
crontab -e
```

Then add the following line to call the Laravel scheduler:

```sh
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

You can run the command manually

```sh
php artisan app:fetch-article-from-api
php artisan app:fetch-article-from-nyt
php artisan app:fetch-article-from-world-news
```
