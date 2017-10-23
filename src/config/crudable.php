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
    'use_auto_binding' => false,
    
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
    
];