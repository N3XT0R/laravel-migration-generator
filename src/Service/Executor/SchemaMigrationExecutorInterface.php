<?php

declare(strict_types=1);

namespace N3XT0R\MigrationGenerator\Service\Executor;

use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;
use Symfony\Component\Console\Style\OutputStyle;

interface SchemaMigrationExecutorInterface
{

    public function run(
        SchemaParserInterface $schemaParser,
        string $connectionName,
        OutputStyle $output
    ): int;
}