<?php

namespace N3XT0R\MigrationGenerator\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Composer;
use Psy\Util\Mirror;

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
        $this->prepareDatabase();
    }


    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase(): void
    {
        $migrator = $this->getMigrator();
        $migrator->setConnection($this->option('database'));

        if (!$migrator->repositoryExists()) {
            $this->call(
                'migrate:install',
                ['--database' => $this->option('database')]
            );
        }
    }
}