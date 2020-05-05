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

    public function testGenerateResultShouldWork(): void
    {
        $this->definition->generate();
        $result = $this->definition->getResult();
        $this->assertCount(11, $result);
        $this->assertContainsOnlyInstancesOf(FieldEntity::class, $result);
    }
}