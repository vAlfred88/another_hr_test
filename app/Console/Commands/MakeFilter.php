<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeFilter extends Command
{
    protected $signature = 'make:filter {name}'; // Command signature (name argument)

    protected $description = 'Create a new filter class';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        // Get the filter name from the input
        $name = $this->argument('name');
        $path = app_path("Filters/{$name}.php");

        // Check if the file already exists
        if ($this->files->exists($path)) {
            $this->error("Filter {$name} already exists!");

            return false;
        }

        // Make the directory if it doesn't exist
        $this->makeDirectory($path);

        // Get the stub content and replace placeholders
        $stub = $this->files->get($this->getStub());
        $stub = $this->replacePlaceholders($stub, $name);

        // Write the generated file
        $this->files->put($path, $stub);

        $this->info("Filter {$name} created successfully.");
    }

    // Define the stub file location
    protected function getStub(): string
    {
        return resource_path('stubs/filter.stub'); // Location of the stub file
    }

    // Replace placeholders in the stub file
    protected function replacePlaceholders($stub, $name): array|string
    {
        return str_replace(
            ['{{ class }}', '{{ filterName }}'],
            [$name, strtolower($name)],
            $stub
        );
    }

    // Ensure the directory exists
    protected function makeDirectory($path): void
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}
