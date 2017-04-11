# Laravel-Crudable


![Laravel Crudable](img/laravel-crudable.png)

** If you want to save time on your crud operations **

This Laravel package is for saving time on CRUD operations when used in 
combination with Repositories or Services.


###Docs

* [Installation](#installation-in-4-steps)

## Installation 

### Step 1: Install package

Add the package in your composer.json by executing the command.

```bash
composer require flobbos/laravel-crudable
```
### Step 2: Use the package

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