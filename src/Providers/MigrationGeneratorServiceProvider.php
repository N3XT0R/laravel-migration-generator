<?php

namespace N3XT0R\MigrationGenerator\Providers;

use Illuminate\Database\MigrationServiceProvider;
use Illuminate\Support\ServiceProvider;
use N3XT0R\MigrationGenerator\Console\Commands;

class MigrationGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    Commands\MigrationGeneratorCommand::class,
                ]
            );
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(MigrationServiceProvider::class);
    }
}
