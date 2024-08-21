<?php

namespace App\Console\Commands;

class MakeModuleModel extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:model {module} {name}';
    protected $description = 'Create a new model in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $modelName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createModel($modulePath, $moduleName, $modelName);
        $this->info('Model created successfully.');
        }

    protected function createModel($modulePath, $moduleName, $modelName)
        {
        $stub = $this->getStub('Model.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $modelName],
            $stub
        );
        $this->files->put($modulePath . '/Models/' . $modelName . '.php', $stub);
        }
    }