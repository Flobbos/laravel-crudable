<?php

namespace Flobbos\Crudable\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ViewCommand extends GeneratorCommand{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:views {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Crud views';
    
    protected $type = 'Views';
    
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(){
        return [
            __DIR__.'/../../resources/stubs/index.stub',
            __DIR__.'/../../resources/stubs/create.stub',
            __DIR__.'/../../resources/stubs/edit.stub'
            ];
    }
    
    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name){
        return resource_path('views/admin/'.$this->getDirectoryName($name));
    }
    
    protected function getDirectoryName($name){
        return  str_plural(strtolower($name));
    }
    
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace){
        return $rootNamespace.'\\'.config('crudable.default_namespace');
    }
    
    /**
     * Replace the service variable in the stub
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceServiceVar($name){
        //dd($name);
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $class = strtolower(str_replace('Service', '', $class));
        //dd($class);
        return snake_case($class);
    }
    
    protected function replaceViewPath($name){
        return str_plural($this->replaceServiceVar($name));
    }
    
    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name, $stub){
        $controllerNamespace = $this->getNamespace($name);
        $replace = [
            'DummyServiceVar' => snake_case($this->replaceServiceVar($name)),
            'DummyViewPath' => snake_case($this->replaceViewPath($name)),
        ];
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
        //dd($replace);
        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }
    
    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName){
        return $this->files->exists($this->getPath($this->getNameInput()));
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $this->comment('Building new Crudable views.');
        
        $path = $this->getPath($this->getNameInput());
        
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exist!');

            return false;
        }
        
        //dd($path);
        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);
        foreach($this->getStub() as $stub){
            
        }
        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }
}
