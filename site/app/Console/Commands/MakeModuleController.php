<?php

namespace App\Console\Commands;

class MakeModuleController extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:controller {module} {name}';
    protected $description = 'Create a new controller in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $controllerName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createController($modulePath, $moduleName, $controllerName);
        $this->info('Controller created successfully.');
        }

    protected function createController($modulePath, $moduleName, $controllerName)
        {
        $stub = $this->getStub('Controller.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $controllerName . 'Controller'],
            $stub
        );
        $this->files->put($modulePath . '/Http/Controllers/' . $controllerName . 'Controller.php', $stub);
        }
    }
