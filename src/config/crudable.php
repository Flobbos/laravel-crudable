<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable auto binding
    |--------------------------------------------------------------------------
    |
    | With this setting you can enable the use of auto binding or disable it.
    | The auto bindings need to be set below
    */
    'use_auto_binding' => true,

    /*
    |--------------------------------------------------------------------------
    | Namespace for Services
    |--------------------------------------------------------------------------
    |
    | Here you can set the default namespace for generating Service classes.
    | Simply change this if you wanna put your classes in a different location.
    | The App\ part is assumed automatically. 
    */
    'default_namespace' => 'Services',

    /*
    |--------------------------------------------------------------------------
    | Namespace for resource controllers
    |--------------------------------------------------------------------------
    |
    | You may with to define a default namespace for your resource controllers
    | like App\Http\Controllers\Admin or something similar. Leave blank for 
    | keeping all the controllers in the Contollers namespace. 
    */
    'default_resource' => 'Admin',

    /*
    |--------------------------------------------------------------------------
    | Array for automatic contextual binding
    |--------------------------------------------------------------------------
    |
    | You can set the contextual binding here so it will automatically be
    | loaded once you added the service provider to your app.php config file.
    | Below is an example configuration. You simply specify the "when" part 
    | of the contextual bind and the "give" part here and CrudableServiceProvider
    | will automatically bind them for you. 
    */

    'implementations' => [
        //User service example
        //[
        //    'when' => \App\Http\Controllers\Admin\UserController::class,
        //    'needs' => \Your\Custom\Contract::class
        //    'give' => \App\Services\UserService::class
        //]
    ],

    /*
    |--------------------------------------------------------------------------
    | Fixed bindings 
    |--------------------------------------------------------------------------
    |
    | Here you can set your fixed bindings for using specific contracts instead
    | of conextual binding seen above 
    |
    */
    'bindings' => [
        //[
        //    'contract' => \App\Contracts\YourContract::class,
        //    'target' => \App\Services\YourService::class
        //],

    ],

    /*
    |--------------------------------------------------------------------------
    | Localized slugs
    |--------------------------------------------------------------------------
    |
    | If you want your slugs to be localized for German umlaut for example
    | you can set this to true and also decide which localization profile you 
    | want to use. 
    |
    */
    'localized_slugs' => true,

    /*
    |--------------------------------------------------------------------------
    | Localization rules
    |--------------------------------------------------------------------------
    |
    | Choose a localization you want to use. The complete list of supported
    | locales can be found on the dependencies Github page.
    | https://github.com/cocur/slugify/tree/master/Resources/rules
    |
    */
    'localization_rule' => 'german',

    /*
    |--------------------------------------------------------------------------
    | CSS Framework
    |--------------------------------------------------------------------------
    |
    | You can choose if you want to continue using Bootstrap4 in Laravel 8
    | or go with the flow and use tailwind for the views. We have both
    |
    */
    'css_framework' => 'tailwind',


];
