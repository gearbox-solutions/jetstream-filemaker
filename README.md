# Jetstream-FileMaker
[![Total Downloads](https://img.shields.io/packagist/dt/gearbox-solutions/jetstream-filemaker)](https://packagist.org/packages/gearbox-solutions/jetstream-filemaker)
[![Latest Stable Version](https://img.shields.io/packagist/v/gearbox-solutions/jetstream-filemaker)](https://packagist.org/packages/gearbox-solutions/jetstream-filemaker)
[![License](https://img.shields.io/packagist/l/gearbox-solutions/jetstream-filemaker)](https://github.com/gearbox-solutions/jetstream-filemaker/blob/2.x/LICENSE)

## Introduction
[Laravel Jetstream](https://jetstream.laravel.com/2.x/introduction.html) is a great starting point for building web applications. Laravel and Eloquent-FileMaker make it easy to store and retrieve data from a FileMaker database through the FileMaker Data API and then integrate that data into a Laravel/Jetstream app. The default behavior for Jetstream, however, is to use a SQL database such as MySQL or SQLite. It's not normally possible to exclusively use a FileMaker database for everything with Jetstream.

This package is designed to allow you to exclusively use a FileMaker database and the FileMaker Data API as your data source for a Jetstream application. With this package, you no longer need to have a primary SQL database and then use FileMaker for additional data. FileMaker can be the sole database used by your Jetstream app.

## Support

This package is built and maintained by [Gearbox Solutions](https://gearboxgo.com/). We build fantastic web apps with technologies like Laravel, Vue, React, and Node. If you would like assistance building your own web app, either using this package or other technologies, please [contact us](https://gearboxgo.com/) for a free introductory consultation to discuss your project. 

## Prepare your FileMaker database

### Quickstart with an example FileMaker database
If you just want to see an example of Jetstream working with FileMaker you can unzip and use the `Jetstream-FileMaker.fmp12` file included in the `dist` folder as an example data source ([or download here](https://github.com/gearbox-solutions/Jetstream-FileMaker/blob/main/dist/Jetstream-FileMaker.fmp12.zip?raw=true)). This file has been configured with the minimum necessary fields to work with Jetstream and already has layouts ready for accessing through the Data API. You can host this file on your FileMaker Server and use it as a testing ground to see Laravel Jetstream running using a FileMaker database as a data source.

Otherwise, read the directions below for how to configure your existing FileMaker database to work with Jetstream. The example file is a great reference for configuring your own database.

A Data API user is preconfigured with the username `jetstream` and the password also set to `jetstream`. You can set these credentials in your `.env` to configure your app for access to this sample file.

### Prepare tables and fields
Laravel Jetstream requires certain tables and fields to function. Normally this would be a SQL database and the tables and fields would be created through migrations, but we're going to be using FileMaker as a data source instead. Your FileMaker database will need to be configured to have the minimum required tables to be able to work with Jetstream. 

You will need the following tables:
* User
* PasswordReset
* PersonalAccessToken

These tables must also have the minimum required fields to support the features of the Jetstream starter kit. The required tables and fields for your FileMaker database can be found in the `Jetstream-FileMaker.fmp12` file included in the `dist` folder of this package ([or download here](https://github.com/gearbox-solutions/Jetstream-FileMaker/blob/main/dist/Jetstream-FileMaker.fmp12.zip?raw=true)). You can either copy these tables/fields to your database, rename your existing fields, or use the [Eloquent-FileMaker field mapping feature](https://github.com/gearbox-solutions/eloquent-filemaker) to map your existing fields to these expected field names.

### Set up layouts for Data API access
The The FileMaker Data API allows access to your tables through layouts in your FileMaker database. Only fields which are on the layouts accessed through the Data API are visible. This means that you MUST include all the fields you want to access through the data API on your layouts.

As a starting point, we recommend creating one layout per table with the minimum number of fields you need one each of the layouts for each of the tables. Again, the example `Jetstream-FileMaker.fmp12` is a great reference for setting up some basic access.

We recommend prefixing layout names you plan on using with the data API so that you can make sure that they're both simple and unique. The example file uses `web_` as a layout name prefix to make sure layout names don't conflict with other common layout names.

By default, Laravel looks for pluralized versions of each of the tables. If you are also using a layout name prefix your layout names, your layout names would need to be:
* `web_users`
* `web_password_resets` 
* `web_personal_access_tokens`

The example `Jetstream-FileMaker.fmp12` file has these prepared as a demonstration, so you can always look at that for reference.

If you don't want to use the pluralized table names Laravel will be searching for by default, the layout names used for the models can also be configured in each model file by setting the table name directly using the `$table` property. Do not include any configured table prefix, such as `web_` when setting table/layout names. You can refer to the [Laravel](https://laravel.com/docs/9.x/eloquent#table-names) and [Eloquent-FileMaker](https://github.com/gearbox-solutions/eloquent-filemaker) documentation for more information about layout and table naming.

## Install and configure Laravel
Laravel should be installed and configured as normal. You can follow the instructions on the [official Laravel website](https://laravel.com/docs/9.x/installation) to get started.
If you already know what you're doing, installation is easy using composer.

```
composer create-project laravel/laravel example-app
```
## Install Laravel Jetstream
Similar to the base Laravel install, you should follow the instructions for installing Jetstream as normal from the [offical Jetstream documentation](https://jetstream.laravel.com/2.x/introduction.html).

Our recommendation is to use the Inertia stack with Jetstream. The steps for a quick installation would go as follows: 
```
composer require laravel/jetstream
php artisan jetstream:install inertia
npm install
npm run dev
```

## Install Jetstream-FileMaker
With the basic Jetstream installed it is now time to install the Jetstream-FileMaker package to make Jetstream work with FileMaker as a data source.

Install Jetstream-FileMaker using Composer
```
composer require gearbox-solutions/jetstream-filemaker
```

Jetstream-FileMaker needs to update the default Jetstream `User` model and add new, custom models for `PasswordReset` and `PersonalAccessToken`. Install these models using artisan.
```
php artisan jetstream-filemaker:install
```

## Update the Laravel configuration
All the basic dependencies are now installed and it's time to configure Laravel to point to your FileMaker database. Update `config/auth.php` and change the existing providers->users->driver value from `eloquent` to `filemaker`. This will tell Laravel to use the custom FileMakerUserProvider included with this package to connect to FileMaker to read and write user credentials.
  ```
  'providers' => [
    'users' => [
      'driver' => 'filemaker',
      'model' => App\Models\User::class,
    ],
  ],
  ```

You also need to add a new FileMaker database connection to `config/database.php` in the `connections` array. This sets some defaults which will be overwritten by settings in the `.env` file.
```
  'connections' => [
        'filemaker' => [
            'driver' => 'filemaker',
            'host' => env('DB_HOST', 'fms.mycompany.com'),
            'database' => env('DB_DATABASE', 'MyDatabaseName'), 
            'username' => env('DB_USERNAME', 'MyUsername'),
            'password' => env('DB_PASSWORD', ''),
            'prefix' => env('DB_PREFIX', ''),
            'version' => env('DB_VERSION', 'vLatest'),
            'protocol' => env('DB_PROTOCOL', 'https'),
            'cache_session_token' => env('DB_CACHE_SESSION_TOKEN', true), // set to true to cache the session token between requests and prevent the need to re-login each time. This can be a significant performance improvement!
            'empty_strings_to_null' => env('DB_EMPTY_STRINGS_TO_NULL', true), // set to false to return empty strings instead of null values when fields are empty in FileMaker
        ],
```

Finally, set update the session_driver, cache_store and database connection information in the `.env` file. If you're using the example file included with this package you would configure your `.env` as follows (with your real server address in `DB_HOST`):
```

# Use a session driver other than 'database'
SESSION_DRIVER=file

# Use a cache store other than `database`
CACHE_STORE=file

DB_CONNECTION=filemaker
DB_HOST=fms.mycompany.com
DB_DATABASE=Jetstream-FileMaker
DB_USERNAME=jetstream
DB_PASSWORD=jetstream
DB_PREFIX=web_
```

## License
Jetstream-FileMaker is open-sourced software licensed under the MIT license.
