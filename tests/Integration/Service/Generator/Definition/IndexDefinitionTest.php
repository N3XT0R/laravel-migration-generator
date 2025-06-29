<?php


namespace Tests\Integration\Service\Generator\Definition;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\IndexDefinition;
use PHPUnit\Framework\Attributes\Depends;
use Tests\DbTestCase;

class IndexDefinitionTest extends DbTestCase
{
    protected $definition;

    public function setUp(): void
    {
        parent::setUp();
        $schema = $this->getDoctrineConnection($this->getDatabaseManager())->createSchemaManager();
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
     */
    #[Depends('testGenerateResultShouldWork')]
    public function testIndexWorks(array $result): void
    {
        $index = $this->getIndexByName('testi', $result);
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
     */
    #[Depends('testGenerateResultShouldWork')]
    public function testUniqueIndexWorks(array $result): void
    {
        $index = $this->getIndexByName('fields_test_medium_int_unique', $result);
        $this->assertEquals('unique', $index->getType());
        $this->assertEquals(
            [
                'medium_int'
            ],
            $index->getColumns()
        );
    }

    protected function getIndexByName(string $name, array $result): ?IndexEntity
    {
        $indexResult = null;
        foreach ($result as $index) {
            if ($index->getName() === $name) {
                $indexResult = $index;
                break;
            }
        }
        self::assertInstanceOf(IndexEntity::class, $indexResult);
        return $indexResult;
    }


}