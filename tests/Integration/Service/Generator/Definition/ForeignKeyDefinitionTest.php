<?php


namespace Tests\Integration\Service\Generator\Definition;


use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\ForeignKeyDefinition;
use Tests\DbTestCase;

class ForeignKeyDefinitionTest extends DbTestCase
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

        $definition = new ForeignKeyDefinition();
        $definition->setSchema($schema);
        $definition->addAttribute('tableName', 'foreign_table');

        $this->definition = $definition;
    }

    public function testGenerateResultWithoutTableReturnsEmptyArray(): void
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(0, $result);
    }

    public function testGenerateResultShouldWork(): array
    {
        $this->definition->addAttribute('table', ['dummy']);
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(ForeignKeyEntity::class, $result);

        return $result;
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testForeignKeyWorks(array $result): void
    {
        /**
         * @var ForeignKeyEntity $foreignKey
         */
        $foreignKey = current($result);
        $this->assertEquals('fields_test_id', $foreignKey->getLocalColumn());
        $this->assertEquals('foreign_table', $foreignKey->getLocalTable());
        $this->assertEquals('id', $foreignKey->getReferencedColumn());
        $this->assertEquals('fields_test', $foreignKey->getReferencedTable());
        $this->assertEquals('SET NULL', $foreignKey->getOnDelete());
        $this->assertEquals('CASCADE', $foreignKey->getOnUpdate());
    }
}