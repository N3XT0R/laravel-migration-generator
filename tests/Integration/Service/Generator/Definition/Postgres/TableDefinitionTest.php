<?php

namespace Service\Generator\Definition\Postgres;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use Tests\Integration\Service\Generator\Definition\AbstractTableDefinitionTest;

class TableDefinitionTest extends AbstractTableDefinitionTest
{
    protected string $dbConnection = 'pgsql';

    protected function assertBigIntegerOptions(FieldEntity $field): void
    {
        // Bei Postgres z.B. nur 2 Optionen statt 3 (laut Fehler)
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    protected function assertTinyIntegerType(FieldEntity $field): void
    {
        // Postgres mapped tinyInteger zu boolean
        $this->assertEquals('smallInteger', $field->getType());
    }

    protected function assertDecimalType(FieldEntity $field): void
    {
        // Postgres verwendet "decimal" statt "unsignedDecimal"
        $this->assertEquals('decimal', $field->getType());
    }
}