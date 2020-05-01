<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use Doctrine\DBAL\Schema\AbstractSchemaManager;

interface DefinitionInterface
{

    public function setSchema(AbstractSchemaManager $schema): void;

    public function getSchema(): AbstractSchemaManager;

    public function hasSchema(): bool;

    public function generate(): void;

    public function getResult(): array;

    public function setResult(array $result): void;
}