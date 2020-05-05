<?php


namespace Tests\Integration\Service\Generator\Definition;


use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\TableDefinition;
use Tests\DbTestCase;

class TableDefinitionTest extends DbTestCase
{
    protected $definition;

    public function setUp(): void
    {
        parent::setUp();
        /**
         * @var DatabaseManager $dbManager
         */
        $dbManager = $this->app->get('db');
        $doctrine = $dbManager->connection()->getDoctrineConnection();
        $schema = $doctrine->getSchemaManager();

        $definition = new TableDefinition();
        $definition->setSchema($schema);
        $definition->addAttribute('tableName', 'fields_test');

        $this->definition = $definition;
    }

    public function testGenerateResultShouldWork(): array
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(12, $result);
        $this->assertContainsOnlyInstancesOf(FieldEntity::class, $result);

        return $result;
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testBigIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $bigInteger
         */
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
        $this->assertCount(4, $bigInteger->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'comment' => null,
                'unsigned' => true,
                'default' => null,
            ],
            $bigInteger->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testSmallIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['small_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('small_int', $field->getColumnName());
        $this->assertEquals('smallInteger', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(3, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => true,
                'comment' => null,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testMediumIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['medium_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('medium_int', $field->getColumnName());
        $this->assertEquals('integer', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(3, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'comment' => null,
                'default' => null,
            ],
            $field->getOptions()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testTinyIntegerIsCorrect(array $result): void
    {
        /**
         * @var FieldEntity $field
         */
        $field = $result['tiny_int'];
        $this->assertEquals('fields_test', $field->getTable());
        $this->assertEquals('tiny_int', $field->getColumnName());
        $this->assertEquals('boolean', $field->getType());
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(3, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => false,
                'comment' => 'my tiny int',
                'default' => '1',
            ],
            $field->getOptions()
        );
    }
}