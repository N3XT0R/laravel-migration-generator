<?php

namespace N3XT0R\MigrationGenerator\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use N3XT0R\MigrationGenerator\Console\Commands;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition;

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

        $this->publishes(
            [
                __DIR__ . '/../Config/migration-definition.php' => config_path('migration-definition.php'),
            ],
            'migration-definition'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/migration-definition.php', 'migration-definition');
        $this->registerParser();
        $this->registerGenerator();
    }


    protected function registerParser(): void
    {
        $this->app->bind(
            SchemaParserInterface::class,
            SchemaParser::class
        );
    }

    protected function getDefinitions(): array
    {
        return (array)config('migration-definition');
    }

    protected function registerGenerator(): void
    {
        $definitions = $this->getDefinitions();

        foreach ($definitions as $definition) {
            $this->app->bind($definition, $definition);
        }

        $this->app->bind(
            MigrationGeneratorInterface::class,
            static function (Application $app, array $params) use ($definitions) {
                $key = 'connectionName';
                if (!array_key_exists($key, $params)) {
                    throw new \InvalidArgumentException('missing key ' . $key . ' in params.');
                }

                $definitionClasses = [];
                foreach ($definitions as $definitionKey => $definitionData) {
                    $definitionClasses[$definitionKey] = $app->make($definitionData['class']);
                }

                return new MigrationGenerator($params[$key], $definitionClasses);
            }
        );
    }
}
