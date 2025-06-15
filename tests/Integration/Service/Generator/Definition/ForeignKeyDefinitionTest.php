<?php


namespace Tests\Integration\Service\Generator\Definition;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\ForeignKeyDefinition;
use PHPUnit\Framework\Attributes\Depends;
use Tests\DbTestCase;

class ForeignKeyDefinitionTest extends DbTestCase
{
    protected $definition;

    public function setUp(): void
    {
        parent::setUp();
        $schema = $this->getDoctrineConnection($this->getDatabaseManager())->createSchemaManager();

        $definition = new ForeignKeyDefinition();
        $definition->setSchema($schema);
        $definition->addAttribute('tableName', 'foreign_table');

        $this->definition = $definition;
    }

    public function testGenerateResultWithoutTableReturnsEmptyArray(): void
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        self::assertCount(0, $result);
    }

    public function testGenerateResultShouldWork(): array
    {
        $this->definition->addAttribute('table', ['dummy']);
        $this->definition->generate();
        $result = $this->definition->getResult();
        self::assertCount(1, $result);
        self::assertContainsOnlyInstancesOf(ForeignKeyEntity::class, $result);

        return $result;
    }

    /**
     * @param  array  $result
     */
    #[Depends('testGenerateResultShouldWork')]
    public function testForeignKeyWorks(array $result): void
    {
        /**
         * @var ForeignKeyEntity $foreignKey
         */
        $foreignKey = current($result);
        self::assertEquals('fields_test_id', $foreignKey->getLocalColumn());
        self::assertEquals('foreign_table', $foreignKey->getLocalTable());
        self::assertEquals('id', $foreignKey->getReferencedColumn());
        self::assertEquals('fields_test', $foreignKey->getReferencedTable());
        self::assertEquals('SET NULL', $foreignKey->getOnDelete());
        self::assertEquals('CASCADE', $foreignKey->getOnUpdate());
    }
}