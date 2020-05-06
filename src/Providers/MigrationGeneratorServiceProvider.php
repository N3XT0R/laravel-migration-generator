<?php

namespace N3XT0R\MigrationGenerator\Providers;

use Illuminate\Contracts\Container\Container as Application;
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
        $this->bootCommand();
        $this->loadViewsFrom(__DIR__ . '/../Stubs/', 'migration-generator');
        $this->publishes(
            [
                __DIR__ . '/../Config/migration-generator.php' => $this->config_path('migration-generator.php'),
            ],
            'migration-generator'
        );
    }

    protected function bootCommand(): void
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
     * function to make able use this library on lumen, too.
     * @param string $path
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function config_path(string $path = ''): string
    {
        return app()->basePath() . 'config' . DIRECTORY_SEPARATOR . ($path ? DIRECTORY_SEPARATOR . $path : $path);
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
        $this->registerCompilerEngine();
        $this->registerCompiler();
        $this->registerDefinitionResolver();
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
        return (array)app('config')->get('migration-generator.definitions');
    }

    protected function getMapper(): array
    {
        return (array)app('config')->get('migration-generator.mapper');
    }

    protected function registerDefinitionResolver(): void
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
    }

    protected function registerGenerator(): void
    {
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
                    $app->make(DefinitionResolverInterface::class, ['connection' => $connection]),
                    $app->make(MigrationCompilerInterface::class)
                );
            }
        );
    }

    protected function registerCompilerEngine(): void
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
    }

    protected function registerCompiler(): void
    {
        $mapper = $this->getMapper();
        foreach ($mapper as $map) {
            $this->app->bind($map['class'], $map['class']);
        }

        $this->app->bind(
            MigrationCompilerInterface::class,
            static function (Application $app) use ($mapper) {
                $view = $app->make(ViewFactory::class);
                $view->addExtension(
                    'stub',
                    'replace'
                );

                $compiler = new MigrationCompiler($view, $app->make('files'));
                $compiler->setMapper($mapper);

                return $compiler;
            }
        );
    }
}
