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
* [Translations](#translations)
* [Usage](#usage)
* [Functions](#functions)
* [Exceptions](#exceptions)
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

Adding the option --contract will load up the service class with a custom 
contract named after the provided service class.

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

Adding the option --contract will put a custom contract into the controller
named after the controller class name provided. 

### View Generator

You can generate basic views for create/edit/index based on the Bootstrap
version that shipped with Laravel. 

```php
php artisan crud:views YourModelName
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

You can also use the --contract option for a custom contract version. 

### Contract Generator

If you want to use your own custom contract in combination with Crudable you
can simply generate a boiler plate version.

```php
php artisan crud:contract YourContract
```

This command will put a contract into your App\Contracts folder with the 
following content:

```php
namespace App\Contracts;

use Flobbos\Crudable\Contracts\Crud;

interface CountryContract extends Crud{
    //your custom code here
}
```

## Translations

### Translation info

Handling translations is based on my other package [Laravel Translatable-DB](https://github.com/Flobbos/laravel-translatable),
where the translations live in a separate table and are identified by either 
a language ID or a language code.

### Basic options for translations

If you plan on using these functions for handling translations there are some
basics you need to set:

```php

use Flobbos\Crudable\Contracts\Crud;
use Flobbos\Crudable;
use Flobbos\Crudable\Contracts\Translation;

class Category implements Crud,Translation{    
    
    use Crudable\Crudable;
    use \Flobbos\Crudable\Translations\Translatable;

    protected $translation_name;
    protected $required_trans; //optional array of fields

    public function __construct(){
        $this->translation_name = 'translations';//defines the relation name
    }

}

```

If you add the --translated option when generating a resource the package will
automatically generate a model translation class. 

### How to use translation functions

You need to run your language options as a foreach loop and send
the translated data as an array like so (example is based on Bootstrap):

```php

<div class="form-group">
    <ul class="nav nav-tabs" role="tablist">
        @foreach($languages as $k=>$lang)
        <li role="presentation" @if($k == 0)class="active"@endif>
            <a href="#{{$lang->code}}" aria-controls="{{$lang->code}}" role="tab" data-toggle="tab">{{$lang->name}}</a>
        </li>
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($languages as $k=>$lang)
        <div role="tabpanel" id="{{$lang->code}}" @if($k==0)class="tab-pane active" @else class="tab-pane" @endif id="{{$lang->code}}">
            <input type="hidden" name="translations[{{$lang->id}}][language_id]" value="{{$lang->id}}" />
            <div class="row">
                <div class="col-md-12">
                    <label for="name{{$lang->id}}" class="control-label">Category Name ({{$lang->code}})</label>
                    <input id="name{{$lang->id}}" type="text" class="form-control" name="translations[{{$lang->id}}][name]" value="{{ old('translations.'.$lang->id.'.name') }}">
                </div>
            </div>
        </div>
        @endforeach
    </div>    
</div>

```
The hidden input is used to setup the correct relation to the corresponding
language. 

Data is then handled in a two step process. First the translation data is filtered and packaged
into an array with this function:

```php

    public function processTranslations(
            array $translations, 
            $trans_key = null, 
            $language_key = 'language_id'){
        
        $approved = [];
        
        foreach($translations as $trans){
            //Check for translation key
            if(!is_null($trans_key)){
                unset($trans[$trans_key]);
            }
            //Filter out empty fields
            if(!isset($this->required_trans) && !empty($this->filterNull($trans,$language_key))){
                $approved[] = $trans;
            }
            //Check if required translations are present for saving
            elseif(isset($this->required_trans) && $this->checkRequired($trans)){
                $approved[] = $trans;
            }
        }
        return $approved;
    }

```

This function returns an array that is then put into the save function:

```php

    public function saveTranslations(
            \Illuminate\Database\Eloquent\Model $model, 
            array $translations){
        
        if(empty($translations))
            throw new MissingTranslationsException;
        
        if(empty($this->translation_name))
            throw new MissingTranslationNameException;
        
        return $model->{$this->translation_name}()->saveMany($translations);
    }

```

In the edit function you also need to reference the translation ID
used to identify an existing translation:

```php

<ul class="nav nav-tabs" role="tablist">
    @foreach($languages as $k=>$lang)
    <li role="presentation" @if($k == 0)class="active"@endif>
        <a href="#{{$lang->code}}" aria-controls="{{$lang->code}}" role="tab" data-toggle="tab">{{$lang->name}}</a>
    </li>
    @endforeach
</ul>
<div class="tab-content">
    @foreach($languages as $k=>$lang)
    <div role="tabpanel" id="{{$lang->code}}" @if($k==0)class="tab-pane active" @else class="tab-pane" @endif id="{{$lang->code}}">
        <input type="hidden" name="translations[{{$lang->id}}][language_id]" value="{{$lang->id}}" />
        <input type="hidden" name="translations[{{$lang->id}}][category_translation_id]" value="get the ID from the translation here" />
        <div class="row">
            <div class="col-md-12">
                <label for="name{{$lang->id}}" class="control-label">Category Name ({{$lang->code}})</label>
                <input id="name{{$lang->id}}" type="text" class="form-control" name="translations[{{$lang->id}}][name]" value="{{ old('translations.'.$lang->id.'.name',get_translation($category->translations,'name',$lang->id)) }}">
            </div>
        </div>
    </div>
    @endforeach
</div>

```

This way you can update existing translations and create new ones where
necessary. 

### Available functions for handling translations

The following functions are available:

```php

public function processTranslations(
            array $translations, 
            $trans_key = null, 
            $language_key = 'language_id');
```

This function will take the translations from an input array and process them
into a usable array of data that you can attach to a model.

You need to provide an array of translation data ($translations), the translation
key name and the language key name;

```php

public function saveTranslations(
            \Illuminate\Database\Eloquent\Model $model, 
            array $translations, 
            $relation_name = 'translations');

```

This function attaches the previously processed translations to an existing model.

```php

public function updateTranslations(
            array $translations, 
            \Illuminate\Database\Eloquent\Model $model, 
            $translation_key, 
            $translation_class);

```

This function updates translations and creates new ones if a translation isn't
already present with the current model.

```php

public function checkRequired(array $arr);

```

You have the option to set required fields within your repository/service via
using $this->required_trans so you always get the minimum translation data.

```php

public function filterNull(array $arr, $except = null);

```

Here you can simply filter out all fields that were left blank in the form if 
you don't have required minimum fields mentioned above. 

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

### get

This will perform a simple get call on the model resource. You can optionally
pass an ID to the function and it will do the same thing as find.

```php
    return $yourService->get();
```

### find

This will get the resource with the given ID. 

```php
    return $yourService->find($id);
```

### first

First will get you the very first row from from your resource, it works the same
as the original Laravel version pretty much with the added Crudable benefits.

```php
    return $yourService->first();
```

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

### with

This works just like setRelation but you can either pass a string OR an array.

```php
    return $yourService->with('some_stuff')->get();
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

### delete

Delete will remove a model from the database, unless soft deletes are in use.
If you want to permanently delete a model you need to give it true as the 
second parameter.

```php
    return $yourService->delete($id); //Normal delete
    return $yourService->delete($id,true); //if you want to force delete
```

### restore

The restore function simply recovers a previously soft deleted model. 

```php
    return $yourService->restore($id);
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

## Exceptions

### MissingRelationData

This is thrown if your trying to save a hasMany or belongsToMany relationship
with empty data. 

### MissingRequiredFields

This exception occurs when you're trying to call required_trans without setting
the necessary data for it.

### MissingTranslationName

If you're using translations but don't set this option then the trait won't 
know where to save the translation data.

### MissingTranslations

This exception is thrown in the case that no translations are present because
all fields were empty or the required fields weren't filled out. 

## Laravel compatibility

 Laravel  | Crudable
:---------|:----------
 5.7      | >3.*
 5.6      | >3.*
 5.5      | >2.*
 5.4      | >2.*
 5.3      | >2.*

**Notice**: If you're planning on using automated binding in Laravel <5.3 you 
need to update the config file to reflect the correct usage. Please refer to
the Laravel [documentation](https://laravel.com/docs/5.2/container).

Have fun CRUDding! :-)