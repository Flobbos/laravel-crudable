<?php

namespace Flobbos\Crudable;

use Illuminate\Support\ServiceProvider;

class CrudableServiceProvider extends ServiceProvider{
    
    public function boot(){
        //Publish config and translations
        $this->publishes([
            __DIR__.'/../config/crudable.php' => config_path('crudable.php'),
            __DIR__.'/../resources/lang' => resource_path('lang')
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
        //Check for auto binding
        if($config->get('crudable.use_auto_binding')){
            foreach($config->get('crudable.implementations') as $imp){
                $this->app->when($imp['when'])
                    ->needs(isset($imp['needs'])?$imp['needs']:\Flobbos\Crudable\Contracts\Crud::class)
                    ->give($imp['give']);
            }
        }
    }
}
