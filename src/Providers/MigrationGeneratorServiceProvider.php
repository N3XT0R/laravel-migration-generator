<?php

namespace N3XT0R\MigrationGenerator\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use N3XT0R\MigrationGenerator\Console\Commands;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Engine\ReplaceEngine;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolver;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use Illuminate\Contracts\View\Factory as ViewFactory;

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

        $this->loadViewsFrom(__DIR__ . '/../Stubs/', 'migration-generator');

        $this->publishes(
            [
                __DIR__ . '/../Config/migration-generator.php' => config_path('migration-generator.php'),
            ],
            'migration-generator'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../Config/migration-generator.php', 'migration-generator');
        $this->registerParser();
        $this->registerGenerator();
        $this->registerCompiler();
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
        return (array)config('migration-generator.definitions');
    }

    protected function registerGenerator(): void
    {
        $definitions = $this->getDefinitions();

        foreach ($definitions as $definition) {
            $this->app->bind($definition['class'], $definition['class']);
        }

        $this->app->bind(
            DefinitionResolverInterface::class,
            static function (Application $app, array $params) use ($definitions) {
                $key = 'connection';
                if (!array_key_exists($key, $params)) {
                    throw new \InvalidArgumentException('missing key ' . $key . ' in params.');
                }

                return new DefinitionResolver($params[$key], $definitions);
            }
        );

        $this->app->bind(
            MigrationGeneratorInterface::class,
            static function (Application $app, array $params) {
                $key = 'connectionName';
                if (!array_key_exists($key, $params)) {
                    throw new \InvalidArgumentException('missing key ' . $key . ' in params.');
                }

                /**
                 * @var DatabaseManager $dbManager
                 */
                $dbManager = $app->get('db');
                $connection = $dbManager->connection($params[$key])->getDoctrineConnection();

                return new MigrationGenerator(
                    $app->make(DefinitionResolverInterface::class, ['connection' => $connection])
                );
            }
        );
    }

    protected function registerCompiler(): void
    {
        $this->app->bind(ReplaceEngine::class, ReplaceEngine::class);

        $this->app->extend(
            'view.engine.resolver',
            static function (EngineResolver $resolver, Application $app) {
                $resolver->register(
                    'replace',
                    static function () use ($app) {
                        return $app->make(ReplaceEngine::class);
                    }
                );

                return $resolver;
            }
        );


        $this->app->bind(
            MigrationCompilerInterface::class,
            static function (Application $app) {
                $view = $app->make(ViewFactory::class);
                $view->addExtension(
                    'stub',
                    'replace'
                );
                return new MigrationCompiler($view, $app->make('files'));
            }
        );
    }
}
