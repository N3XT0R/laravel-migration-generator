<?php

namespace N3XT0R\MigrationGenerator\Providers;

use Doctrine\DBAL\DriverManager;
use Illuminate\Contracts\Container\Container as Application;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\EngineResolver;
use N3XT0R\MigrationGenerator\Console\Commands;
use N3XT0R\MigrationGenerator\Service\Executor\SchemaMigrationExecutor;
use N3XT0R\MigrationGenerator\Service\Executor\SchemaMigrationExecutorInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Engine\ReplaceEngine;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManager;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManagerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolver;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Sort\TopSort;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserFactory;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserFactoryInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;

class MigrationGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Stubs/', 'migration-generator');
        $this->publishes(
            [
                __DIR__.'/../Config/migration-generator.php' => config_path('migration-generator.php'),
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
        $this->mergeConfigFrom(__DIR__.'/../Config/migration-generator.php', 'migration-generator');
        $this->registerParserFactory();
        $this->registerParser();
        $this->registerCompilerEngine();
        $this->registerCompiler();
        $this->registerDefinitionResolver();
        $this->registerNormalizer();
        $this->registerGenerator();
        $this->registerExecutor();
        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        $this->commands([
            Commands\MigrationGeneratorCommand::class,
        ]);
    }

    protected function registerParserFactory(): void
    {
        $this->app->singleton(
            SchemaParserFactoryInterface::class,
            SchemaParserFactory::class
        );
    }

    protected function registerParser(): void
    {
        $this->app->bind(
            SchemaParserInterface::class,
            function (Application $app, array $params = []) {
                /**
                 * @var SchemaParserFactoryInterface $factory
                 */
                $factory = $app->make(SchemaParserFactoryInterface::class);

                /** @var \Illuminate\Database\DatabaseManager $dbManager */
                $dbManager = $app['db'];

                $connectionName = $params['connectionName'] ?? null;
                $connection = $connectionName ? $dbManager->connection($connectionName) : $dbManager->connection();

                return $factory->create($connection);
            }
        );
    }

    protected function getConfigSection(string $key): array
    {
        return (array)$this->app['config']->get('migration-generator.'.$key);
    }

    protected function registerDefinitionResolver(): void
    {
        $definitions = $this->getConfigSection('definitions');
        $this->bindClasses($definitions);

        $this->app->bind(
            DefinitionResolverInterface::class,
            static function (Application $app, array $params) use ($definitions) {
                $key = 'connection';
                if (!array_key_exists($key, $params)) {
                    throw new \InvalidArgumentException('missing key '.$key.' in params.');
                }

                return new DefinitionResolver($params[$key], $definitions);
            }
        );
    }

    protected function registerGenerator(): void
    {
        $config = $this->getConfigSection('config');
        $this->app->bind(
            MigrationGeneratorInterface::class,
            static function (Application $app, array $params) use ($config) {
                $dbMap = [
                    'mysql' => 'pdo_mysql',
                    'sqlite' => 'pdo_sqlite',
                    'pgsql' => 'pdo_pgsql',
                ];

                $key = 'connectionName';
                if (!array_key_exists($key, $params)) {
                    throw new \InvalidArgumentException('missing key '.$key.' in params.');
                }

                /**
                 * @var DatabaseManager $dbManager
                 */
                $dbManager = $app->get('db');
                $dbConfig = $dbManager->connection($params[$key])->getConfig();
                if (array_key_exists($dbConfig['name'], $dbMap)) {
                    $dbConfig['driver'] = $dbMap[$dbConfig['name']];
                }

                $connectionParams = [
                    'dbname' => $dbConfig['database'],
                    'user' => $dbConfig['username'],
                    'password' => $dbConfig['password'],
                    'host' => $dbConfig['host'],
                    'driver' => $dbConfig['driver'],
                ];
                $connection = DriverManager::getConnection($connectionParams);
                return new MigrationGenerator(
                    $app->make(DefinitionResolverInterface::class, ['connection' => $connection]),
                    $app->make(MigrationCompilerInterface::class),
                    (string)$config['migration_dir']
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
        $mapper = $this->getConfigSection('mapper');
        $this->bindClasses($mapper);

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

    protected function bindClasses(array $classes): void
    {
        foreach ($classes as $map) {
            $this->app->bind($map['class'], $map['class']);
        }
    }


    protected function registerNormalizer(): void
    {
        $normalizer = $this->getConfigSection('normalizer');
        $this->bindClasses($normalizer);

        $this->app->bind(
            SchemaNormalizationManagerInterface::class,
            static function (Application $app, array $params) use ($normalizer) {
                $sortedProcessors = TopSort::sort($normalizer);
                $enabledProcessors = null;
                if (array_key_exists('enabled', $params)) {
                    $enabledProcessors = (array)$params['enabled'];
                }
                $normalizerClasses = [];
                foreach ($sortedProcessors as $key) {
                    $normalizerClasses[] = $app->get($normalizer[$key]['class']);
                }

                return new SchemaNormalizationManager($normalizerClasses, $enabledProcessors);
            }
        );
    }

    protected function registerExecutor(): void
    {
        $this->app->bind(SchemaMigrationExecutorInterface::class, function ($app, $params) {
            if (!array_key_exists('generator', $params)) {
                throw new \InvalidArgumentException('missing key generator in params.');
            }
            return new SchemaMigrationExecutor(
                $params['generator'],
                $params['normalizer'] ?? null
            );
        });
    }
}
