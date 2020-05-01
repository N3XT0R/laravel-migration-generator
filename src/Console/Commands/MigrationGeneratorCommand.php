<?php

namespace N3XT0R\MigrationGenerator\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Composer;
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
        {{--database= : The database connection to use}}';

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
        $database = $this->option('database') ?? config('database.default');
        $this->prepareDatabase($database);
        $schemaParser = $this->getLaravel()->make(SchemaParserInterface::class);
        $schemaParser->setConnectionByName($database);
        $tables = $schemaParser->getSortedTablesFromSchema(
            $this->getMigrator()->resolveConnection($database)->getDatabaseName()
        );

        foreach ($tables as $table) {
        }
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