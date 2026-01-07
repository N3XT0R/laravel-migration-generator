<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;

use Doctrine\DBAL\Connection as DoctrineConnection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
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
            if ($databasePlatform instanceof MySQLPlatform) {
                $databasePlatform->registerDoctrineTypeMapping('enum', 'string');
            }
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
        // Step 1: Separate index-like definitions
        [$indexEntitiesByName, $result] = $this->extractIndexEntities($definitionResult[$table]);

        // Step 2: Resolve conflicts (e.g. foreignKey vs. index) but retain original group
        $this->mergeResolvedIndexes($result, $indexEntitiesByName);

        return [$table => $result];
    }


    protected function extractIndexEntities(array $definitionsByType): array
    {
        $indexEntitiesByName = [];
        $result = [];

        foreach ($definitionsByType as $type => $definitions) {
            foreach ($definitions as $key => $definition) {
                if (!$definition instanceof AbstractIndexEntity) {
                    $result[$type][$key] = $definition;
                    continue;
                }

                $name = $definition->getName();
                $indexEntitiesByName[$name][] = ['entity' => $definition, 'originType' => $type];
            }
        }

        return [$indexEntitiesByName, $result];
    }


    protected function mergeResolvedIndexes(array &$result, array $indexEntitiesByName): void
    {
        foreach ($indexEntitiesByName as $name => $candidates) {
            $preferred = null;
            foreach ($candidates as $item) {
                if ($item['entity']->getIndexType() === 'foreignKey') {
                    $preferred = $item;
                    break;
                }
            }

            // fallback if no foreignKey
            if (!$preferred) {
                $preferred = $candidates[0];
            }

            $originType = $preferred['originType'];
            $result[$originType][$name] = $preferred['entity'];
        }
    }


}