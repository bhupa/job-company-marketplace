<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeModule extends Command
    {
    protected $signature = 'make:module {name}';
    protected $description = 'Create a new module';

    protected $files;

    public function __construct(Filesystem $files)
        {
        parent::__construct();
        $this->files = $files;
        }

    public function handle()
        {
        $moduleName = $this->argument('name');
        $modulePath = app_path('Modules/' . $moduleName);

        if ($this->files->exists($modulePath)) {
            $this->error('Module already exists!');
            return;
            }

        // Create resource directories
      

        // Create the module directory structure
        $this->files->makeDirectory($modulePath . '/Http/Controllers', 0755, true);
        $this->files->makeDirectory($modulePath . '/Http/Requests', 0755, true);
        $this->files->makeDirectory($modulePath . '/Http/Resources', 0755, true);
        $this->files->makeDirectory($modulePath . '/Models', 0755, true);
        $this->files->makeDirectory($modulePath . '/Services', 0755, true);
        $this->files->makeDirectory($modulePath . '/database/migrations', 0755, true);
        $this->files->makeDirectory($modulePath . '/Providers', 0755, true);
        $this->files->makeDirectory($modulePath . '/Events', 0755, true);
        $this->files->makeDirectory($modulePath . '/Listeners', 0755, true);
        $this->files->makeDirectory($modulePath . '/Mail', 0755, true);
        $this->files->makeDirectory($modulePath . '/Notifications', 0755, true);
        $this->files->makeDirectory($modulePath . '/resources', 0755, true);
        $this->files->makeDirectory($modulePath . '/resources/views', 0755, true);
        $this->files->makeDirectory($modulePath . '/resources/lang', 0755, true);
        $this->files->makeDirectory($modulePath . '/Traits', 0755, true);


        // Create example files
        $this->createServiceProvider($modulePath, $moduleName);
        $this->createRoutesFile($modulePath, $moduleName);
        $this->createController($modulePath, $moduleName);
        $this->createModel($modulePath, $moduleName);
        $this->createService($modulePath, $moduleName);
        $this->createFormRequest($modulePath, $moduleName);
        $this->createResourceFile($modulePath, $moduleName);
        $this->createEvent($modulePath, $moduleName);
        $this->createListener($modulePath, $moduleName);
        $this->createNotification($modulePath, $moduleName);
        $this->createMail($modulePath, $moduleName);

        $this->info('Module created successfully.');
        }


   
    protected function createServiceProvider($modulePath, $moduleName)
        {
        $stub = $this->getStub('ServiceProvider.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'ServiceProvider'], $stub);
        $this->files->put($modulePath . '/Providers/' . $this->getClassName($moduleName) . 'ServiceProvider.php', $stub);
        }

    protected function createRoutesFile($modulePath, $moduleName)
        {
        $routesDir = $modulePath . '/routes';
        if (!file_exists($routesDir)) {
            mkdir($routesDir, 0755, true);
            }

        // Create web routes file
        $webStub = $this->getStub('webRoutes.stub');
        $webStub = str_replace('{{moduleNamespace}}', $this->normalizeNamespace($moduleName), $webStub);
        file_put_contents($routesDir . '/web.php', $webStub);

        // Create API routes file
        $apiStub = $this->getStub('apiRoutes.stub');
        $apiStub = str_replace('{{moduleNamespace}}', $this->normalizeNamespace($moduleName), $apiStub);
        file_put_contents($routesDir . '/api.php', $apiStub);
        }

    protected function createController($modulePath, $moduleName)
        {
        $stub = $this->getStub('Controller.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'Controller'], $stub);
        $this->files->put($modulePath . '/Http/Controllers/' . $this->getClassName($moduleName) . 'Controller.php', $stub);
        }

    protected function createModel($modulePath, $moduleName)
        {
        $stub = $this->getStub('Model.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName)], $stub);
        $this->files->put($modulePath . '/Models/' . $this->getClassName($moduleName) . '.php', $stub);
        }
    protected function createNotification($modulePath, $moduleName)
        {
        $stub = $this->getStub('Notification.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName)], $stub);
        $this->files->put($modulePath . '/Notifications/' . $this->getClassName($moduleName) . 'Notifications.php', $stub);
        }

    
     protected function createMail($modulePath, $moduleName)
        {
        $stub = $this->getStub('Mail.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName)], $stub);
        $this->files->put($modulePath . '/Mail/' . $this->getClassName($moduleName) . 'Mail.php', $stub);
        }

    protected function createService($modulePath, $moduleName)
        {
        $stub = $this->getStub('Service.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'Service'], $stub);
        $this->files->put($modulePath . '/Services/' . $this->getClassName($moduleName) . 'Service.php', $stub);
        }

    protected function createFormRequest($modulePath, $moduleName)
        {
        $stub = $this->getStub('FormRequest.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'Request'], $stub);
        $this->files->put($modulePath . '/Http/Requests/' . $this->getClassName($moduleName) . 'Request.php', $stub);
        }

    protected function createResourceFile($modulePath, $moduleName)
        {
        $stub = $this->getStub('Resource.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'Resource'], $stub);
        $this->files->put($modulePath . '/Http/Resources/' . $this->getClassName($moduleName) . 'Resource.php', $stub);
        }
    protected function createEvent($modulePath, $moduleName)
        {
        $stub = $this->getStub('Event.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'Event'], $stub);
        $this->files->put($modulePath . '/Events/' . $this->getClassName($moduleName) . 'Event.php', $stub);
        }

    protected function createListener($modulePath, $moduleName)
        {
        $stub = $this->getStub('Listener.stub');
        $stub = str_replace(['{{moduleNamespace}}', '{{className}}'], [$this->normalizeNamespace($moduleName), $this->getClassName($moduleName) . 'Listener'], $stub);
        $this->files->put($modulePath . '/Listeners/' . $this->getClassName($moduleName) . 'Listener.php', $stub);
        }

    protected function getStub($stub)
        {
        return $this->files->get(resource_path("stubs/$stub"));
        }

    protected function normalizeNamespace($moduleName)
        {
        return str_replace('/', '\\', $moduleName);
        }

    protected function getClassName($moduleName)
        {
        $segments = explode('/', $moduleName);
        return end($segments);
        }
    }
