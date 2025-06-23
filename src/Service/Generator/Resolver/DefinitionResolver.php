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
        $result = [];

        // Flatten all AbstractIndexEntitys into flat list grouped by name
        $indexEntitiesByName = [];

        foreach ($definitionResult[$table] as $type => $definitions) {
            foreach ($definitions as $key => $definition) {
                if (!$definition instanceof AbstractIndexEntity) {
                    // Pass through unchanged
                    $result[$type][$key] = $definition;
                    continue;
                }

                $name = $definition->getName();

                // Save both the entity and its origin type
                $indexEntitiesByName[$name][] = [
                    'entity' => $definition,
                    'originType' => $type,
                ];
            }
        }

        // Resolve duplicates by name, with rules
        foreach ($indexEntitiesByName as $name => $entries) {
            $hasPrimary = false;
            $hasForeign = false;
            $primaryEntry = null;
            $foreignEntry = null;
            $fallbackEntry = null;

            foreach ($entries as $entry) {
                $entity = $entry['entity'];
                $indexType = $entity->getIndexType();

                if ($indexType === 'primary') {
                    $hasPrimary = true;
                    $primaryEntry = $entry;
                } elseif ($indexType === 'foreignKey') {
                    $hasForeign = true;
                    $foreignEntry = $entry;
                } elseif (!$fallbackEntry) {
                    $fallbackEntry = $entry;
                }
            }

            // Always keep primary
            if ($hasPrimary && $primaryEntry !== null) {
                $result[$primaryEntry['originType']][$name] = $primaryEntry['entity'];
            }

            // Prefer foreign if present and not already added
            if ($hasForeign && $foreignEntry !== null) {
                $result[$foreignEntry['originType']][$name] = $foreignEntry['entity'];
            } elseif ($fallbackEntry !== null && !$hasForeign && !$hasPrimary) {
                $result[$fallbackEntry['originType']][$name] = $fallbackEntry['entity'];
            }
        }

        return [$table => $result];
    }


}