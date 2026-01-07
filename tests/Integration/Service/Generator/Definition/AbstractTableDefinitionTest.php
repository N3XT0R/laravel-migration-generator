<?php

namespace Tests\Integration\Service\Generator\Definition;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\TableDefinition;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;
use PHPUnit\Framework\Attributes\Depends;
use Tests\DbTestCase;

abstract class AbstractTableDefinitionTest extends DbTestCase
{

    protected string $dbConnection = '';

    protected TableDefinition $definition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skipUnlessDatabase($this->dbConnection);
        $doctrine = $this->getDoctrineConnection($this->getDatabaseManager());
        $this->app->make(DefinitionResolverInterface::class, ['connection' => $doctrine]);
        $schema = $doctrine->createSchemaManager();

        $definition = new TableDefinition();
        $definition->setSchema($schema);
        $definition->addAttribute('tableName', 'fields_test');

        $this->definition = $definition;
    }

    public function testGenerateResultShouldWork(): array
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(15, $result);
        $this->assertContainsOnlyInstancesOf(FieldEntity::class, $result);
        foreach (array_keys($result) as $key) {
            $this->assertIsString($key);
        }

        return $result;
    }

    #[Depends('testGenerateResultShouldWork')]
    public function testBigIntegerIsCorrect(array $result): void
    {
        /** @var FieldEntity $bigInteger */
        $bigInteger = $result['id'];
        $this->assertEquals('fields_test', $bigInteger->getTable());
        $this->assertEquals('id', $bigInteger->getColumnName());
        $this->assertEquals('bigInteger', $bigInteger->getType());
        $this->assertCount(1, $bigInteger->getArguments());
        $this->assertSame(
            [
                'autoIncrement' => true,
            ],
            $bigInteger->getArguments()
        );

        // Achtung: Anzahl der Optionen ist DB-spezifisch, Methode in Child überschreiben
        $this->assertBigIntegerOptions($bigInteger);
    }

    protected function assertBigIntegerOptions(FieldEntity $field): void
    {
        // Default (MySQL, MSSQL)
        $this->assertCount(3, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
                'unsigned' => true,
            ],
            $field->getOptions()
        );
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
        $this->assertCount(3, $field->getOptions());
        $this->assertSame(
            [
                'default' => '1',
                'nullable' => false,
                'comment' => 'my tiny int',
            ],
            $field->getOptions()
        );
    }

    protected function assertTinyIntegerType(FieldEntity $field): void
    {
        $this->assertEquals('boolean', $field->getType());
    }

    #[Depends('testGenerateResultShouldWork')]
    public function testDecimalIsCorrect(array $result): void
    {
        /** @var FieldEntity $field */
        $field = $result['decimal_value'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('decimal_value', $field->getColumnName());

        $this->assertDecimalType($field);

        $this->assertCount(2, $field->getArguments());
        $this->assertSame(
            [
                'total' => 2,
                'places' => 1,
            ],
            $field->getArguments()
        );

        $this->assertCount(2, $field->getOptions());
        $this->assertSame(
            [
                'default' => null,
                'nullable' => false,
            ],
            $field->getOptions()
        );
    }

    protected function assertDecimalType(FieldEntity $field): void
    {
        // Default (MySQL, MSSQL)
        $this->assertEquals('unsignedDecimal', $field->getType());
    }

}
