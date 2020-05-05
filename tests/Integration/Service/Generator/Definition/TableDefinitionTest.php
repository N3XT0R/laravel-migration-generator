<?php


namespace Tests\Integration\Service\Generator\Definition;


use Doctrine\DBAL\Schema\MySqlSchemaManager;
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
        $this->assertCount(11, $result);
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
        $this->assertCount(0, $field->getArguments());
        $this->assertCount(4, $field->getOptions());
        $this->assertSame(
            [
                'nullable' => true,
                'comment' => null,
                'unsigned' => false,
                'default' => null,
            ],
            $field->getOptions()
        );
    }
}