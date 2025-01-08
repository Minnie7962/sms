<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeService extends Command
{
    // Define the command signature
    protected $signature = 'make:service {name}';

    // Command description
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name'); // Get the service name from the input
        $directory = app_path('Services'); // Define the directory for services

        // Ensure the Services directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Full path of the new service file
        $path = "{$directory}/{$name}.php";

        // Prevent overwriting existing files
        if (file_exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        // Generate the class stub
        $stub = <<<EOT
<?php

namespace App\Services;

class {$name}
{
    // Implement your service logic here
}
EOT;

        // Write the stub to the file
        file_put_contents($path, $stub);

        $this->info("Service {$name} created successfully at {$path}");
    }
}
