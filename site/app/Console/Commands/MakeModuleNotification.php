<?php

namespace App\Console\Commands;

class MakeModuleNotification extends MakeModuleFileCommand
    {
    protected $signature = 'make:module:notification {module} {name}';
    protected $description = 'Create a new notification in a module';

    public function handle()
        {
        $moduleName = $this->argument('module');
        $notificationName = $this->argument('name');

        $modulePath = $this->getModulePath($moduleName);
        if (!$this->files->exists($modulePath)) {
            $this->error('Module does not exist!');
            return;
            }

        $this->createNotification($modulePath, $moduleName, $notificationName);
        $this->info('Notification created successfully.');
        }

    protected function createNotification($modulePath, $moduleName, $notificationName)
        {
        $stub = $this->getStub('Notification.stub');
        $stub = str_replace(
            ['{{moduleNamespace}}', '{{className}}'],
            [$this->normalizeNamespace($moduleName), $notificationName . 'Notification'],
            $stub
        );
        
        $this->files->put($modulePath . '/Notifications/' . $notificationName . 'Notification.php', $stub);
        }
    }
