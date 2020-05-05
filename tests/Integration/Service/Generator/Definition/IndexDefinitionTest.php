<?php


namespace Tests\Integration\Service\Generator\Definition;


use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\IndexDefinition;
use Tests\DbTestCase;

class IndexDefinitionTest extends DbTestCase
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

        $definition = new IndexDefinition();
        $definition->setSchema($schema);

        $definition->addAttribute('tableName', 'fields_test');

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
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(IndexEntity::class, $result);

        return $result;
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testIndexWorks(array $result): void
    {
        /**
         * @var IndexEntity $index
         */
        $index = $result['testi'];
        $this->assertEquals('index', $index->getType());
        $this->assertEquals('testi', $index->getName());
        $this->assertEquals(
            [
                'any_date'
            ],
            $index->getColumns()
        );
    }

    /**
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
    public function testUniqueIndexWorks(array $result): void
    {
        /**
         * @var IndexEntity $index
         */
        $index = $result['fields_test_medium_int_unique'];
        $this->assertEquals('unique', $index->getType());
        $this->assertEquals(
            [
                'medium_int'
            ],
            $index->getColumns()
        );
    }


}