<?php

namespace Flobbos\Crudable\Commands;

use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ControllerCommand extends GeneratorCommand{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:controller {name} {--blank}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a resource controller with Crud implementation';
    
    protected $type = 'Controller';
    
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace){
        return $rootNamespace.'\Http\Controllers';
    }
    
    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceServiceVar($name){
        //dd($name);
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $service_var = strtolower(str_replace('Controller', '', $class));
        return snake_case($service_var);
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
    protected function buildClass($name){
        $controllerNamespace = $this->getNamespace($name);

        $replace["use {$controllerNamespace}\Controller;\n"] = '';
        $replace = array_merge($replace, [
            'DummyViewPath' => snake_case($this->replaceViewPath($name)),
            'DummyServiceVar' => snake_case($this->replaceServiceVar($name)),
        ]);
        //dd($replace);
        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }
    
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(){
        if($this->option('blank')){
            return __DIR__.'/../../resources/stubs/controller.blank.stub';
        }
        return __DIR__.'/../../resources/stubs/controller.stub';
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        if($this->option('blank')){
            $this->comment('Building new Crudable based controller');
        }
        else{
            $this->comment('Building new Crudable based resource controller');
        }
        
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }
        
        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
        
    }
}
