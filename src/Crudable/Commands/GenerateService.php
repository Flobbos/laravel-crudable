<?php

namespace Flobbos\Crudable\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a service for binding with the Crud interface';
    
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Service';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $name = $this->options('name');
        
    }
}
