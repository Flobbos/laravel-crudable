<?php

namespace Flobbos\Crudable\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ViewCommand extends GeneratorCommand
{
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
    private $current_stub;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../resources/stubs/' . config('crudable.css_framework') . '/index.stub';
    }

    /**
     * Get all view stub files mapped by blade filename.
     *
     * @return array<string, string>
     */
    protected function getStubs()
    {
        return [
            'index.blade.php' => __DIR__ . '/../../resources/stubs/' . config('crudable.css_framework') . '/index.stub',
            'create.blade.php' => __DIR__ . '/../../resources/stubs/' . config('crudable.css_framework') . '/create.stub',
            'edit.blade.php' => __DIR__ . '/../../resources/stubs/' . config('crudable.css_framework') . '/edit.stub',
        ];
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return resource_path('views/admin/' . $this->getDirectoryName($name));
    }

    protected function getDirectoryName($name)
    {
        return Str::plural(strtolower(Str::kebab($name)));
    }

    /**
     * Replace the service variable in the stub using pluralization
     *
     * @param  string  $name
     * @return string
     */
    protected function replaceServiceVar($name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        $class = strtolower(Str::snake(str_replace('Service', '', $class)));
        return Str::plural($class);
    }

    /**
     * Replace the service variable in stubs in singular
     * @param string $name
     * @return string
     */
    protected function replaceSingularServiceVar($name)
    {
        $class = str_replace($this->getNamespace($name) . '\\', '', $name);
        $class = strtolower(Str::snake(str_replace('Service', '', $class)));
        return $class;
    }

    protected function replaceViewPath($name)
    {
        return Str::plural(Str::kebab(str_replace($this->getNamespace($name) . '\\', '', $name)));
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $replace = [
            'DummyServiceVar' => $this->replaceServiceVar($name),
            'DummyViewPath' => $this->replaceViewPath($name),
            'DummySingularServiceVar' => $this->replaceSingularServiceVar($name),
        ];
        return str_replace(
            array_keys($replace),
            array_values($replace),
            $this->generateClass($name)
        );
    }

    protected function generateClass($name)
    {
        $stub = $this->files->get($this->current_stub);
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($this->getNameInput()));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Building new Crudable views.');

        $path = $this->getPath(strtolower(Str::kebab($this->getNameInput())));
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exist!');
            return false;
        }

        foreach ($this->getStubs() as $name => $stub) {
            $this->current_stub = $stub;
            $this->makeDirectory($path . '/' . $name);
            $this->files->put($path . '/' . $name, $this->buildClass($this->getNameInput()));
        }
        $this->info($this->type . ' created successfully.');
    }
}
