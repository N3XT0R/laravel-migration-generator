<?php


namespace N3XT0R\MigrationGenerator\Service\Processor;


use Illuminate\Database\Migrations\Migrator;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;

interface MigrationProcessorInterface
{

    public function __construct(SchemaParserInterface $schemaParser, Migrator $migrator);

    public function setSchemaParser(SchemaParserInterface $schemaParser): void;

    public function getSchemaParser(): SchemaParserInterface;

    public function hasMessagesForType(string $type): bool;

    public function getMessagesByType(string $type): array;

    public function run(array $options): void;
}