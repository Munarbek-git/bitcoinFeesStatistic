## Running the API

It's very simple to get the API up and running. First, create the database (and database
user if necessary) and add them to the `.env` file.

```
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_password
```

Then install, migrate, seed, all that jazz:

1. `composer install`
2. `cp .env.example .env`
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan db:seed`
6. `php artisan serve`
7. add cron `* * * * * php /path/to/artisan schedule:run >>/dev/null 2>&1`

The API will be running on `localhost:8000`.
For manual updating `localhost:8000/update`.
