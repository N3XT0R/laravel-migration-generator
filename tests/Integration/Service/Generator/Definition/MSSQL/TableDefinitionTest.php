<?php

namespace Service\Generator\Definition\MSSQL;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use PHPUnit\Framework\Attributes\Depends;
use Tests\Integration\Service\Generator\Definition\AbstractTableDefinitionTest;

class TableDefinitionTest extends AbstractTableDefinitionTest
{
    protected string $dbConnection = 'sqlsrv';

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

    #[Depends('testGenerateResultShouldWork')]
    public function testTinyIntegerIsCorrect(array $result): void
    {
        /** @var FieldEntity $field */
        $field = $result['tiny_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('tiny_int', $field->getColumnName());

        $this->assertTinyIntegerType($field);

        $this->assertCount(0, $field->getArguments());
        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => '1',
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    protected function assertDecimalType(FieldEntity $field): void
    {
        $this->assertEquals('decimal', $field->getType());
    }
}