<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Enable auto binding
    |--------------------------------------------------------------------------
    |
    | With this setting you can enable the use of auto binding or disable it.
    */
    'use_auto_binding' => false,
    
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
        //    'give' => \App\Services\UserService::class
        //]
    ]
    
];