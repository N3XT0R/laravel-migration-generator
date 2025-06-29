<?php

namespace N3XT0R\MigrationGenerator\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Config;
use N3XT0R\MigrationGenerator\Service\Generator\DTO\MigrationTimingDto;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManager;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManagerInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;

class MigrationGeneratorCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'migrate:regenerate
         {--database= : The database connection to use} '; //later for 1.1 : {{--force : force re-init in migrations-table}}

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

    public function __construct()
    {
        $this->extendSignatureWithNormalizers();
        parent::__construct();
        $this->setMigrator(app()->make('migrator'));
    }

    public function setMigrator(Migrator $migrator): void
    {
        $this->migrator = $migrator;
    }

    public function getMigrator(): Migrator
    {
        return $this->migrator;
    }

    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $enabled = $this->resolveEnabledNormalizers();
        if (!$this->validateNormalizers($enabled)) {
            return Command::FAILURE;
        }

        if (count($enabled) === 0) {
            $enabled = null;
        }

        $force = false;
        //$force = (bool)$this->option('force');
        $connectionName = $this->option('database') ?? config('database.default');
        $this->prepareDatabase($connectionName);

        /**
         * @var SchemaParserInterface $schemaParser
         */
        $schemaParser = $this->getLaravel()->make(
            SchemaParserInterface::class,
            [
                'connectionName' => $connectionName
            ]
        );

        $this->createMigrationsForWholeSchema($schemaParser, $connectionName, $enabled);

        return Command::SUCCESS;
    }

    protected function createMigrationsForWholeSchema(
        SchemaParserInterface $schemaParser,
        string $connectionName,
        ?array $enabledNormalizer
    ): void {
        $laravel = $this->getLaravel();
        /**
         * @var MigrationGenerator $generator
         */
        $generator = $laravel->make(
            MigrationGeneratorInterface::class,
            ['connectionName' => $connectionName]
        );

        /**
         * @var SchemaNormalizationManager $normalizer
         */
        $normalizer = $laravel->make(
            SchemaNormalizationManagerInterface::class,
            ['enabled' => $enabledNormalizer]
        );
        $generator->setNormalizationManager($normalizer);

        $database = $this->getMigrator()->resolveConnection($connectionName)->getDatabaseName();
        $tables = $schemaParser->getSortedTablesFromSchema(
            $database
        );
        $tableAmount = count($tables);
        $bar = $this->output->createProgressBar($tableAmount);
        $bar->setFormat('verbose');
        $bar->start();
        $migrationTimingDto = new MigrationTimingDto();
        $migrationTimingDto->setMaxAmount($tableAmount);
        $migrationTimingDto->setTimestamp(time());

        foreach ($tables as $num => $table) {
            $migrationTimingDto->setCurrentAmount($num);
            if (true === $generator->generateMigrationForTable($database, $table, $migrationTimingDto)) {
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

    protected function extendSignatureWithNormalizers(): void
    {
        $normalizers = array_keys(Config::get('migration-generator.normalizer', []));

        if (!empty($normalizers)) {
            $choices = implode(',', $normalizers);
            $this->signature .= ' {--normalizer=* : Enabled normalizers (available: ' . $choices . ')}';
        }
    }

    protected function resolveEnabledNormalizers(): array
    {
        $input = $this->option('normalizer');
        $config = config('migration-generator.config.defaults.normalizer.enabled', []);

        return !empty($input) ? (array)$input : (array)$config;
    }

    protected function validateNormalizers(array $enabled): bool
    {
        $result = true;
        $available = array_keys(config('migration-generator.normalizer', []));
        $invalid = array_diff($enabled, $available);

        if (!empty($invalid)) {
            $this->error('Invalid normalizer(s): ' . implode(', ', $invalid));
            $result = false;
        }

        return $result;
    }
}