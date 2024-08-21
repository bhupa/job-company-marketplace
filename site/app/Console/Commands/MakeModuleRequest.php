<?php

namespace App\Console\Commands;

class MakeModuleRequest extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:request {module} {name}';
    protected $description = 'Create a new form request in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $requestName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createRequest($modulePath, $moduleName, $requestName);
        $this->info('Form request created successfully.');
        }

    protected function createRequest($modulePath, $moduleName, $requestName)
        {
        $stub = $this->getStub('FormRequest.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $requestName . 'Request'],
            $stub
        );
        $this->files->put($modulePath . '/Http/Requests/' . $requestName . 'Request.php', $stub);
        }
    }
