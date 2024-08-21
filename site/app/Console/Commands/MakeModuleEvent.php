<?php

namespace App\Console\Commands;

class MakeModuleEvent extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:event {module} {name}';
    protected $description = 'Create a new event class in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $eventName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createEvent($modulePath, $moduleName, $eventName);
        $this->info('Event created successfully.');
        }

    protected function createEvent($modulePath, $moduleName, $eventName)
        {
        $stub = $this->getStub('Event.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $eventName . 'Event'],
            $stub
        );
        $this->files->put($modulePath . '/Events/' . $eventName . 'Event.php', $stub);
        }
    }
