<?php

namespace App\Console\Commands;

class MakeModuleResource extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:resource {module} {name}';
    protected $description = 'Create a new resource in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $resourceName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createResource($modulePath, $moduleName, $resourceName);
        $this->info('Resource created successfully.');
        }

    protected function createResource($modulePath, $moduleName, $resourceName)
        {
        $stub = $this->getStub('Resource.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $resourceName . 'Resource'],
            $stub
        );
        $this->files->put($modulePath . '/Http/Resources/' . $resourceName . 'Resource.php', $stub);
        }
    }
