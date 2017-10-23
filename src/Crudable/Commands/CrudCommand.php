<?php

namespace Flobbos\Crudable\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CrudCommand extends GeneratorCommand{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:resource {name} {--silent}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new Crud resource';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Resource';
    
    
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(){
        return;
    }
    
    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseModel($model){
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }
    
    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle(){
        if($this->option('silent')){
            $this->handleSilent();
        }
        else{
            $this->handleVerbose();
        }
        $this->info($this->type.' created successfully.');
        $this->info('Do not forget to register any bindings.');
    }
    
    protected function handleVerbose(){
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        
        //Check if the model exists
        $modelClass = $this->parseModel($this->getNameInput());
        if (! class_exists($modelClass)) {
            if ($this->confirm("The {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }
        
        //Generate service
        if ($this->confirm("Would you like to generate the service class?", true)) {
            $this->call('crud:service', ['name' => $this->getNameInput().'Service']);
        }
        
        //Generate controller
        if ($this->confirm("Would you like to generate a resource controller?", true)) {
            $namespace = $this->ask('Namespace for the controller?','default');
            if($namespace == 'default'){
                $controller_name = $this->getNameInput().'Controller';
            }
            else{
                $controller_name = $namespace.'\\'.$this->getNameInput().'Controller';
            }
            $this->call('crud:controller', ['name' => $controller_name]);
        }

    }
    
    protected function handleSilent(){
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        
        //Check if the model exists
        $modelClass = $this->parseModel($this->getNameInput());
        if (! class_exists($modelClass)) {
            $this->call('make:model', ['name' => $modelClass]);
        }
        //Generate service class
        $this->call('crud:service', ['name' => $this->getNameInput().'Service']);
        //Generate controller
        if(empty(config('crudable.default_resource'))){
            $controller_name = $this->getNameInput().'Controller';
        }
        else{
            $controller_name = config('crudable.default_resource').'\\'.$this->getNameInput().'Controller';
        }
        $this->call('crud:controller', ['name' => $controller_name]);
        
    }
    
    
}
