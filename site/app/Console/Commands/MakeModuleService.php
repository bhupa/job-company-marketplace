<?php

namespace App\Console\Commands;

class MakeModuleService extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:service {module} {name}';
    protected $description = 'Create a new service in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $serviceName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createService($modulePath, $moduleName, $serviceName);
        $this->info('Service created successfully.');
        }

    protected function createService($modulePath, $moduleName, $serviceName)
        {
        $stub = $this->getStub('Service.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $serviceName . 'Service'],
            $stub
        );
        $this->files->put($modulePath . '/Services/' . $serviceName . 'Service.php', $stub);
        }
    }
