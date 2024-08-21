<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;


class MakeModuleFileCommand extends Command
{
    protected $signature = 'make:module-file {name}';
    protected $description = 'Create a new module file';

    protected $files;

    public function __construct(Filesystem $files)
        {
        parent::__construct();
        $this->files = $files;
        }

    protected function getModulePath($moduleName)
        {
        return app_path('Modules/' . $moduleName);
        }

    protected function getStub($stub)
        {
        return $this->files->get(resource_path("stubs/$stub"));
        }

    protected function normalizeNamespace($moduleName)
        {
        return str_replace('/', '\\', $moduleName);
        }

    protected function getClassName($name)
        {
        $segments = explode('/', $name);
        return end($segments);
        }
}
