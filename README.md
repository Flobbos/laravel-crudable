# Laravel-Crudable


![Laravel Crudable](img/laravel-crudable.png)

**If you want to save time on your crud operations**

This Laravel package is for saving time on CRUD operations when used in 
combination with Repositories or Services. The trait covers the basics needed
for running simple CRUD operations. It also comes with a Contract that you
can bind to your services via automated contextual binding. 


### Docs

* [Installation](#installation)
* [Configuration](#configuration)
* [Generators](#generators)
* [Usage](#usage)
* [Functions](#functions)
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

## Configuration

### Publish configuration file

Laravel 5.*
```bash
php artisan vendor:publish 
```

### Auto binding

Auto binding is used to run through the implementation list explained below.
If this is set to true the auto binding feature will be used.

```php
'use_auto_binding' => false
```

### Namespace for services

Here you can set your default namespace for your service or repository classes.

```php
'default_namespace' => 'Services'
```

### Namespace for resource controllers

If you wish to set a default namespace for resource controllers use this option.
Which will be used when in silent mode in the resource generator.

```php
'default_resource' => 'Admin',
```

### Implementations

Update the configuration according to your needs. A sample configuration is
provided in the config file.

```php
return [
    'implementations' => [
        [
            //This is where you set the requesting class
            'when' => \App\Http\Controllers\Admin\UserController::class,
            //This is where you can define your own contracts
            'needs' => \Your\Own\Contract::class,
            //This is where you send out the implementation
            'give' => \App\Services\UserService::class
        ]
    ]
];
```
### Fixed bindings

If you are using your own contracts you may want to use fixed bindings instead
of the contextual binding mentioned above. This automatically binds your
implementation to your specifically designed contract.

```php

'bindings' => [
        [
            //'contract' => \App\Contracts\YourContract,
            //'target' => \App\Services\YourService
        ]
    ]

```

## Generators

### Service Generator

You can generate your own service/repository classes that implement the model
and already use the Crudable trait. So easy. 

```php
php artisan crud:service CountryService
```

The above command will generate a service class in App\Services (depending on
your configuration setting mentioned above) that looks like this:

```php

namespace App\Services;

use App\Country;
use Flobbos\Crudable\Contracts\Crud;
use Flobbos\Crudable;

class CountryService implements Crud {
    
    use Crudable\Crudable;
    
    public function __construct(Country $country) {
        $this->model = $country;
    }
    
}

```

### Controller Generator

You can generate either a blank controller or a complete resource controller. 

```php
php artisan crud:controller 
```
This will generate the resource controllers with all necessary basic functions
already filled in for you based on the Crudable functionality. 

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Flobbos\Crudable\Contracts\Crud;
use Exception;

class CountryController extends Controller{
    
    protected $country;

    public function __construct(Crud $country) {
        $this->country = $country;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('admin.countries.index')->with(['country'=>$this->country->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('admin.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validate($request, []);
        
        try{
            $this->country->create($request->all());
            return redirect()->route('')->withMessage(trans('crud.record_created'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
        return view('admin.countries.show')->with(['country'=>$this->country->find($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        return view('admin.countries.edit')->with(['country'=>$this->country->find($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $this->validate($request, []);
        
        try{
            $this->country->update($id, $request->all());
            return redirect()->route('admin.countries.index')->withMessage(trans('crud.record_updated'));
        } catch (Exception $ex) {
            return redirect()->back()->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        try{
            $this->country->delete($id);
            return redirect()->route('admin.countries.index')->withMessage(trans('crud.record_deleted'));
        } catch (Exception $ex) {
            return redirect()->route('admin.countries.index')->withErrors($ex->getMessage());
        }
    }
}
```

This of course only covers the very basic functions but saves you from 
writing the same boiler plate code over and over again. 

If you just need a blank controller with just the services implemented use the
blank option like so:

```php
php artisan crud:controller --blank
```

### View Generator

You can generate basic views for create/edit/index based on the Bootstrap
version that shipped with Laravel. 

```php
php artisan crud:views yourviewpath
```

The above command will generate a basic listing template, the form template for
creating a new resource and of course the form template for editing. It is 
assumed that your views live inside an "admin" folder in resources/views.


### Resource Generator

If you're starting out fresh you may wish to generate the entire resource 
including the model, service, resource controller and views.

```php
php artisan crud:resource Country
```

All necessary suffixes (Controller, Service) will be added automatically. The
generator will ask you which parts to create. If you just want to generate 
everything without interruptions use silent mode:

```php
php artisan crud:resource Country --silent
```

## Usage

### Repository/Service implementation

Add the package to the repository or service where you want the trait to be used.

```php
use App\Country;

class CountryService {
    
    use \Flobbos\Crudable\Crudable;
    
    //protected $model; in version 2.0 and higher this no longer needs to be set

    public function __construct(Country $country){
        $this->model = $country;
    }

}
```

By injecting the model into the service or repository and assigning it to 
the protected $this->model, the trait now has access to your model and can work
its magic.

### Auto binding explained

You have the option to use auto binding in the config which automatically binds
the Crud contract to your implementation.

```php
namespace App\Http\Controllers;

use Flobbos\Crudable\Contracts\Crud;

class SomeController extends Controller {
    protected $crud;

    public function __construct(Crud $crud){
        $this->crud = $crud;
    }
}
```

The ServiceProvider automatically binds the Crud interface to the 
implementation you specified in the config as explained below.

### Use your own contracts

```php
namespace App\Contracts;

use Flobbos\Crudable\Contracts\Crud;

interface MyOwnContract extends Crud{
    //place your custom code here
}
```

By simply extending the Crud contract you can define your own logic without
needing to redeclare everything that Crudable already provides. 

## Functions

### Where

The where function accepts parameters and passes them along to the Eloquent
where method. It returns $this so you can chain it onto other methods.

```php
    return $yourService->where('some_field','some_value');
```

### setRelation

The setRelation method acts like the with() statement you are used to from
Eloquent. Just pass in an array of eager loading statements complete with
callbacks an everything else. Can also be chained.

```php
    return $yourService->setRelation(['some_stuff'])->get();
```

### orderBy

This method just passes along your orderBy statement to Eloquent.

### withHasMany

The method adds hasMany data to your create statement. 

```php
    return $yourService->withHasMany($data,'App\YourRelatedModel','related_model')->create($model);
```

### withBelongsToMany

With this method you can automatically save related data in a many-to-many
relationship. Just pass an array of the data to be synced and the relation
name from your model as the second parameter.

```php
    return $yourService->withBelongsToMany($data,'some_relation')->create($model);
```

### handleUpload

Processing uploads is somewhat cumbersome so I thought I'd include an easy to
use function for handling single file image uploads. You just pass in the 
request object, the fieldname for the photo upload, the folder where the 
photo is supposed to go, the storage disk and you can optionally have the photo
name randomized by that function so files don't get overwritten. Defaults are 
below. 

```php
    $yourService->handleUpload($request, $fieldname = 'photo', $folder = 'images', $storage_disk = 'public', $randomize = true);
```

## Laravel compatibility

 Laravel  | Crudable
:---------|:----------
 5.4      | >0.4/2.0
 5.3      | >0.4/2.0

**Notice**: If you're planning on using automated binding in Laravel <5.3 you 
need to update the config file to reflect the correct usage. Please refer to
the Laravel [documentation](https://laravel.com/docs/5.2/container).

Have fun CRUDding! :-)