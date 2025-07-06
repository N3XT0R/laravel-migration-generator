<?php

declare(strict_types=1);

namespace N3XT0R\MigrationGenerator\Service\Executor;

use Illuminate\Support\Facades\DB;
use N3XT0R\MigrationGenerator\Service\Generator\DTO\MigrationTimingDto;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManagerInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Style\OutputStyle;

class SchemaMigrationExecutor implements SchemaMigrationExecutorInterface
{
    protected MigrationGeneratorInterface $generator;

    protected ?SchemaNormalizationManagerInterface $normalizer = null;

    public function __construct(
        MigrationGeneratorInterface $generator,
        ?SchemaNormalizationManagerInterface $normalizer = null
    ) {
        $this->setGenerator($generator);
        $this->setNormalizer($normalizer);
    }

    public function getGenerator(): MigrationGeneratorInterface
    {
        return $this->generator;
    }

    public function setGenerator(MigrationGeneratorInterface $generator): void
    {
        $this->generator = $generator;
    }

    public function getNormalizer(): ?SchemaNormalizationManagerInterface
    {
        return $this->normalizer;
    }

    public function setNormalizer(?SchemaNormalizationManagerInterface $normalizer): void
    {
        $this->normalizer = $normalizer;
    }

    public function run(
        SchemaParserInterface $schemaParser,
        string $connectionName,
        OutputStyle $output
    ): int {
        $result = CommandAlias::SUCCESS;
        $generator = $this->getGenerator();
        $normalizer = $this->getNormalizer();
        if ($normalizer) {
            $generator->setNormalizationManager($normalizer);
        }

        $database = DB::connection($connectionName)->getDatabaseName();
        $tables = $schemaParser->getSortedTablesFromSchema($database);

        $bar = $output->createProgressBar(count($tables));
        $bar->setFormat('verbose');
        $bar->start();

        $dto = new MigrationTimingDto();
        $dto->setMaxAmount(count($tables));
        $dto->setTimestamp(time());

        foreach ($tables as $i => $table) {
            $dto->setCurrentAmount($i);
            if ($generator->generateMigrationForTable($database, $table, $dto)) {
                $bar->advance();
            } else {
                $output->error("Error creating migration for $table");
                $output->error(implode(', ', $this->generator->getErrorMessages()));
                $result = CommandAlias::FAILURE;
                break;
            }
        }

        $bar->finish();
        $output->writeln('');
        return $result;
    }
}