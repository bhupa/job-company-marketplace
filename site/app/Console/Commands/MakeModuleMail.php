<?php

namespace App\Console\Commands;

class MakeModuleMail extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:mail {module} {name}';
    protected $description = 'Create a new mail class in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $mailName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createMail($modulePath, $moduleName, $mailName);
        $this->info('Mail created successfully.');
        }

    protected function createMail($modulePath, $moduleName, $mailName)
        {
        $stub = $this->getStub('Mail.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $mailName . 'Mail'],
            $stub
        );
        $this->files->put($modulePath . '/Mail/' . $mailName . 'Mail.php', $stub);
        }
    }
