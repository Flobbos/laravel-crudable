# Laravel-Crudable


![Laravel Crudable](img/laravel-crudable.png)

** If you want to save time on your crud operations **

This Laravel package is for saving time on CRUD operations when used in 
combination with Repositories or Services.


###Docs

* [Installation](#installation)
* [Configuration](#configuration)
* [Laravel compatibility](#laravel-compatibility)

## Installation 

### Install package

Add the package in your composer.json by executing the command.

```bash
composer require flobbos/laravel-crudable
```

Next, if you plan on using the Contract with automated binding,
add the service provider to `app/config/app.php`

```
Flobbos\Crudable\CrudableServiceProvider::class,
```

### Use the package

Add the package to the repository or service where you want the trait to be used.

```php
use App\Country;

class CountryService {
    
    use \Flobbos\Crudable\Crudable;
    
    protected $model;

    public function __construct(Country $country){
        $this->model = $country;
    }

}
```

By injecting the model into the service or repository and assigning it to 
the protected $this->model, the trait now has access to your model and can work
its magic.

## Configuration

Laravel 5.*
```bash
php artisan vendor:publish 
```

Update the configuration according to your needs. A sample configuration is
provided in the config file.

```php
return [
    'implementations' => [
        [
            //This is where you set the requesting class
            'when' => \App\Http\Controllers\Admin\UserController::class,
            //This is where you send out the implementation
            'give' => \App\Services\UserService::class
        ]
    ]
];
```

## Laravel compatibility

 Laravel  | Crudable
:---------|:----------
 5.4      | 0.4
 5.3      | 0.4
 5.2      | 0.4
 5.1      | 0.4
 5.0      | 0.4

**Notice**: If you're planning on using automated binding in Laravel <5.3 you 
need to update the config file to reflect the correct usage. Please refer to
the Laravel [documentation](https://laravel.com/docs/5.2/container).

Have fun CRUDding! :-)