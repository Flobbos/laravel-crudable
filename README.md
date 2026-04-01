# Laravel-Crudable

![Laravel Crudable](img/laravel-crudable.png)
![Tests](https://github.com/Flobbos/laravel-crudable/actions/workflows/tests.yml/badge.svg)

**If you want to save time on your crud operations**

This Laravel package is for saving time on CRUD operations when used in
combination with Repositories or Services. The trait covers the basics needed
for running simple CRUD operations. It also comes with a Contract that you
can bind to your services via automated contextual binding.

Supports **Laravel 11, 12, and 13** on **PHP 8.1–8.4**.

### Docs

-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Generators](#generators)
-   [Translations](#translations)
-   [Slugs](#slugs)
-   [Usage](#usage)
-   [Functions](#functions)
-   [Exceptions](#exceptions)
-   [Security](#security)
-   [Performance](#performance)
-   [Troubleshooting](#troubleshooting)
-   [Laravel compatibility](#laravel-compatibility)

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

```bash
php artisan vendor:publish --provider="Flobbos\Crudable\CrudableServiceProvider"
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

### Select CSS Framework

Since Laravel 8 switched to Tailwind CSS there are two options of views that can
be generated. Just set the config to bootstrap or tailwind, depending on what you
need.

```php
'css_framework' => 'tailwind',
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
basics you need to set in your service class:

```php

use Flobbos\Crudable\Contracts\Crud;
use Flobbos\Crudable;
use Flobbos\Crudable\Contracts\Translation;

class CategoryService implements Crud,Translation{

    use Crudable\Crudable;
    use \Flobbos\Crudable\Translations\Translatable;

    //only necessary if your translation relation is named something else
    //than 'translations' in your model
    protected $translation_name = 'my_translations';
    //optional array of fields that HAVE to be present to save a translation
    protected $required_trans;

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

You need to provide an array of translation data (\$translations), the translation
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
using \$this->required_trans so you always get the minimum translation data.

```php

public function filterNull(array $arr, $except = null);

```

Here you can simply filter out all fields that were left blank in the form if
you don't have required minimum fields mentioned above.

## Slugs

### Handle slugs with ease

If you want a service class to handle slugs for you automatically, all you need to
do is set the following variable to the field you would like to be transformed
into a slug:

```php
protected $slug_field = 'title';
```

Also implement the Sluggable interface like so:

```php
use Flobbos\Crudable\Contracts\Sluggable;

class YourServiceClass implements Crud,Sluggable{}
```

### Creating slugs

In the example scenario the field 'title' will be transformed into a URL slug and saved
to the default 'slug' field in the database.

If you wish to name the slug field differently you need to set the following
variable to the field name you prefer:

```php
protected $slug_name = 'url_slug';
```

### Retrieving resources with slugs

Of course you need to be able to retrieve database resources from their respective tranlated
slugs. No worries, we already thought of that. Use the interface and trait:

```php
use Flobbos\Crudable\Contracts\Sluggable;
use Flobbos\Crudable\Contracts\Slugger;

class YourServiceClass implements Crud,Sluggable,Slugger{}

use Crudable\Slugs\Slugger;
```

This will give you access to the following functions:

```php
public function getResourceIdFromTranslatedSlug(string $slug): int;
```

With this function you can retrieve the requested resource ID from the given translated URL slug.

```php
public function getTranslatedSlugFromResourceId(int $id, int $language_id): string;
```

If you need to get the URL slug of a specific resource in a specific language just use this function.

The same functions also exist for non translated slugs. Just in case.

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
the protected \$this->model, the trait now has access to your model and can work
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
where method. It returns \$this so you can chain it onto other methods.

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

### MissingTranslations

This exception is thrown in the case that no translations are present because
all fields were empty or the required fields weren't filled out.

### MissingSlugField

This exception is thrown when you forgot to define your slug field in the service
class.

### SlugNotFound

The SlugNotFoundException is thrown when you're trying to get a resource ID from
a normal or a translated slug.

## Security

### File uploads

`handleUpload()` and `handleUploadedFile()` store files using Laravel's filesystem abstraction.
To keep your application secure:

-   **Validate MIME types** before calling these methods. Never trust the client-provided extension alone.
    ```php
    $request->validate([
        'photo' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
    ]);
    ```
-   **Use a non-public disk** for sensitive uploads (e.g. `s3`, `local`) and generate signed URLs instead of exposing raw paths.
-   **Set a maximum file size** at the framework and web-server level (e.g. `upload_max_filesize` in `php.ini`, `client_max_body_size` in nginx).
-   Files are randomized by default (`$randomize = true`) to prevent enumeration; keep this enabled in production.

### Mass assignment

The `create()` and `update()` methods pass data directly to Eloquent. Always define `$fillable` (or `$guarded`) on your models and validate input at the controller/form-request layer before it reaches the service.

### Slug generation

Slugs are generated from user-supplied strings. Ensure the field used as the slug source (`$slug_field`) is validated and length-limited before processing.

---

## Performance

-   **Eager-load relations** when displaying lists to avoid N+1 queries:
    ```php
    $service->with(['category', 'tags'])->get();
    ```
-   **Use `paginate()`** instead of `get()` for large datasets:
    ```php
    $service->orderBy('created_at', 'desc')->paginate(25);
    ```
-   **Batch operations**: for bulk inserts consider using Eloquent's `insert()` directly rather than calling `create()` in a loop.
-   Keep `use_auto_binding` set to `false` if you are not using the contextual binding feature — it avoids the binding loop on every request.

---

## Troubleshooting

**`MissingSlugFieldException`** — You implemented `Sluggable` but forgot to define `$slug_field` in your service class.
```php
protected string $slug_field = 'name'; // the field whose value becomes the slug
protected string $slug_name  = 'slug'; // the column that stores the slug (default: 'slug')
```

**`MissingTranslationsException`** — `saveTranslations()` was called with an empty array. Make sure `processTranslations()` runs first and returns non-empty results.

**`MissingRelationDataException`** — You called `withHasMany()` or `withBelongsToMany()` but did not provide both `data` and `relation`. Check that your relation array keys are set correctly.

**`Class [config] does not exist` in tests** — Your test class does not boot a Laravel application container. Extend `Orchestra\Testbench\TestCase` (or this package's `Flobbos\Crudable\Tests\TestCase`) instead of `PHPUnit\Framework\TestCase`.

**Service file not generated** — Pass the full intended class name including the `Service` suffix to `crud:service`, e.g. `php artisan crud:service PostService`.

---

## Laravel compatibility

| Laravel | Crudable | PHP         |
| :------ | :------- | :---------- |
| 13.x    | >6.1     | ^8.2        |
| 12.x    | >6.1     | ^8.2        |
| 11.x    | >6.\*    | ^8.1        |
| 10.x    | >5.\*    | ^8.1        |
| 9.x     | >4.\*    | ^8.0        |
| 8.x     | >4.\*    | ^7.3\|^8.0  |

Have fun CRUDding! :-)
