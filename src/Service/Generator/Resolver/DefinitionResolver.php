<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;

use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\DefinitionInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\AbstractIndexEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Sort\TopSort;

class DefinitionResolver extends AbstractResolver implements DoctrineTypeMappingsInterface
{
    public function registerDoctrineTypeMappings(DoctrineConnection $doctrineConnection): void
    {
        $databasePlatform = $doctrineConnection->getDatabasePlatform();

        if ($databasePlatform) {
            /**
             * @todo implement own types for doctrineRegistry
             */
        }
    }


    public function resolveTableSchema(string $schema, string $table): ResultEntity
    {
        $definitions = $this->getDefinitions();
        $sortedDefinitions = TopSort::sort($definitions);
        $connection = $this->getDoctrineConnection();
        $schemaManager = $connection->createSchemaManager();

        $this->assertTableExists($schemaManager, $table);

        $definitionResult = $this->generateDefinitionResult(
            $schemaManager,
            $sortedDefinitions,
            $definitions,
            $schema,
            $table
        );

        return $this->buildResultEntity($table, $definitionResult);
    }


    protected function assertTableExists($schemaManager, string $table): void
    {
        if (!$schemaManager->tablesExist($table)) {
            throw new \InvalidArgumentException("Table {$table} not exists!");
        }
    }

    protected function generateDefinitionResult(
        AbstractSchemaManager $schemaManager,
        array $sortedDefinitions,
        array $definitions,
        string $schema,
        string $table
    ): array {
        $definitionResult = [];

        foreach ($sortedDefinitions as $name) {
            $definitionClass = $this->getDefinitionByName($name);

            if (!$definitionClass instanceof DefinitionInterface) {
                continue;
            }

            $definitionClass->setAttributes([
                'database' => $schema,
                'tableName' => $table,
            ]);

            foreach ($definitions[$name]['requires'] as $dependency) {
                if (isset($definitionResult[$table][$dependency])) {
                    $definitionClass->addAttribute($dependency, $definitionResult[$table][$dependency]);
                }
            }

            $definitionClass->setSchema($schemaManager);
            $definitionClass->generate();

            $definitionResult[$table][$name] = $definitionClass->getResult();
        }

        return $definitionResult;
    }

    protected function buildResultEntity(string $table, array $definitionResult): ResultEntity
    {
        $result = new ResultEntity();
        $result->setTableName($table);
        $result->setResults($this->getUniqueIndexes($table, $definitionResult));

        return $result;
    }


    protected function getUniqueIndexes(string $table, array $definitionResult): array
    {
        // Prepare output
        $result = [];

        // Flatten all AbstractIndexEntitys into a flat list by name
        $indexEntitiesByName = [];

        foreach ($definitionResult[$table] as $type => $definitions) {
            foreach ($definitions as $key => $definition) {
                // If not index entity, pass through unmodified
                if (!$definition instanceof AbstractIndexEntity) {
                    $result[$type][$key] = $definition;
                    continue;
                }

                $name = $definition->getName();
                $indexType = $definition->getIndexType();

                // Group index-type entities by name
                if (!isset($indexEntitiesByName[$name])) {
                    $indexEntitiesByName[$name] = [];
                }

                $indexEntitiesByName[$name][] = $definition;
            }
        }

        // Resolve index-name conflicts: keep foreignKey if present
        foreach ($indexEntitiesByName as $name => $entities) {
            // Determine if there's a foreignKey among them
            $preferred = null;
            foreach ($entities as $entity) {
                if ($entity->getIndexType() === 'foreignKey') {
                    $preferred = $entity;
                    break;
                }
            }

            // If no foreignKey, pick first one (arbitrary fallback)
            if (!$preferred) {
                $preferred = $entities[0];
            }

            $indexType = $preferred->getIndexType();
            $result[$indexType][$name] = $preferred;
        }

        return [$table => $result];
    }

}