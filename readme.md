<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
    v- 5.8.22

</p>

## How to make it work

1) Setup a database variables in the .env  file (and all other ones depending on your need).
2) Run the migrations to create tables in your database:
   ```sh
    php artisan migrate
   ```
3) You can add a source to update your database (data must be in JSON format).
This should be set in the .env file at the DB_UPDATE_SOURCE variable.
To update your database you need to execute a request similar to this  -> http://YourSite/public/dbUpdate?code=bd_Update_Code.



***Learning Laravel***

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1400 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.



