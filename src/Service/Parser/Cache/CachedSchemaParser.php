<?php

declare(strict_types=1);

namespace N3XT0R\MigrationGenerator\Service\Parser\Cache;

use N3XT0R\MigrationGenerator\Service\Parser\AbstractSchemaParser;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;

class CachedSchemaParser extends AbstractSchemaParser implements SchemaParserInterface
{
    public function getTablesFromSchema(string $schema): array
    {
        // TODO: Implement getTablesFromSchema() method.
    }

    protected function getForeignKeyConstraints(string $schema, string $tableName): array
    {
        // TODO: Implement getForeignKeyConstraints() method.
    }

    protected function getRefNameByConstraintName(string $schema, string $constraintName): string
    {
        // TODO: Implement getRefNameByConstraintName() method.
    }

}