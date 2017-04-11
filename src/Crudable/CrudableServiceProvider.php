<?php

namespace Flobbos\Crudable;

use Illuminate\Support\ServiceProvider;

class CrudableServiceProvider extends ServiceProvider{
    
    public function boot(){
        $this->publishes([
            __DIR__.'/../config/crudable.php' => config_path('crudable.php'),
        ]);
    }

    /**
     * Register the service provider.
     */
    public function register(){
        $this->mergeConfigFrom(
            __DIR__.'/../config/crudable.php', 'crudable'
        );
        //Load config
        $config = $this->app->make('config');
        if($config->get('crudable.use_auto_binding')){
            foreach($config->get('crudable.implementations') as $imp){
                $this->app->when($imp['when'])
                    ->needs(\App\Contracts\Crud::class)
                    ->give($imp['give']);
            }
        }
    }
}