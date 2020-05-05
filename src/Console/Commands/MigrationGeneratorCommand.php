<?php

namespace N3XT0R\MigrationGenerator\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Composer;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;

class MigrationGeneratorCommand extends MigrateMakeCommand
{

    use ConfirmableTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'migrate:regenerate {{--table= : specific table}} 
        {{--database= : The database connection to use}} '; //later for 1.1 : {{--force : force re-init in migrations-table}}

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Migration file(s) by database Schema';

    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * MigrationGeneratorCommand constructor.
     * @param MigrationCreator $creator
     * @param Composer $composer
     * @param Migrator $migrator
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer, Migrator $migrator = null)
    {
        parent::__construct($creator, $composer);
        if (null === $migrator) {
            /**
             * @var Migrator $migrator
             */
            $migrator = app()->make('migrator');
        }
        $this->setMigrator($migrator);
    }

    public function setMigrator(Migrator $migrator): void
    {
        $this->migrator = $migrator;
    }

    public function getMigrator(): Migrator
    {
        return $this->migrator;
    }

    public function handle(): void
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $table = (string)$this->option('table');
        $force = false;
        //$force = (bool)$this->option('force');
        $connectionName = $this->option('database') ?? config('database.default');
        $this->prepareDatabase($connectionName);

        $schemaParser = $this->getLaravel()->make(SchemaParserInterface::class);
        $schemaParser->setConnectionByName($connectionName);

        if (!empty($table)) {
            $this->createMigrationForSingleTable($schemaParser, $connectionName, $table);
        } else {
            $this->createMigrationsForWholeSchema($schemaParser, $connectionName);
        }

        if (true === $force) {
            /**
             * @todo reinitialize migrations table
             */
        }
    }


    protected function createMigrationForSingleTable(
        SchemaParserInterface $schemaParser,
        string $connectionName,
        string $table
    ): void {
        /**
         * @var MigrationGenerator $generator
         */
        $generator = $this->getLaravel()->make(
            MigrationGeneratorInterface::class,
            ['connectionName' => $connectionName]
        );

        $database = $this->getMigrator()->resolveConnection($connectionName)->getDatabaseName();
        $tables = $schemaParser->getTablesFromSchema(
            $database
        );
        if (!in_array($table, $tables, true)) {
            $this->error('Table "' . $table . '" not exists in Schema "' . $database . '"');
        } else {
            if (true === $generator->generateMigrationForTable($database, $table)) {
                /**
                 * @todo
                 */
            } else {
                $this->error('there occurred an error by creating migration for ' . $table);
                $this->error(implode(', ', $generator->getErrorMessages()));
            }
        }
    }

    protected function createMigrationsForWholeSchema(SchemaParserInterface $schemaParser, string $connectionName): void
    {
        /**
         * @var MigrationGenerator $generator
         */
        $generator = $this->getLaravel()->make(
            MigrationGeneratorInterface::class,
            ['connectionName' => $connectionName]
        );

        $database = $this->getMigrator()->resolveConnection($connectionName)->getDatabaseName();
        $tables = $schemaParser->getSortedTablesFromSchema(
            $database
        );
        $bar = $this->output->createProgressBar(count($tables));
        $bar->setFormat('verbose');
        $bar->start();

        foreach ($tables as $table) {
            if (true === $generator->generateMigrationForTable($database, $table)) {
                $bar->advance();
            } else {
                $this->error('there occurred an error by creating migration for ' . $table);
                $this->error(implode(', ', $generator->getErrorMessages()));
                break;
            }
        }

        $bar->finish();
        $this->line('');
    }


    /**
     * Prepare the migration database for running.
     *
     * @param string|null $database
     * @return void
     */
    protected function prepareDatabase(string $database = null): void
    {
        $migrator = $this->getMigrator();
        $migrator->setConnection($database);

        if (!$migrator->repositoryExists()) {
            $this->call(
                'migrate:install',
                ['--database' => $database]
            );
        }
    }
}