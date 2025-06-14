<?php


namespace Tests\Integration\Service\Generator\Definition;


use Doctrine\DBAL\DriverManager;
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
        $dbConfig = $dbManager->connection()->getConfig();
        $connectionParams  = [
            'dbname' => $dbConfig['database'],
            'user' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'host' => $dbConfig['host'],
            'driver' => 'pdo_mysql',
        ];
        $doctrine = DriverManager::getConnection($connectionParams);
        $schema = $doctrine->createSchemaManager();

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
     * @param array $result
     * @depends testGenerateResultShouldWork
     */
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