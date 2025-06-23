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
        $flatIndexEntities = $this->extractIndexEntitiesWithOrigin($definitionResult[$table]);
        $filteredIndexes = $this->filterDuplicateIndexesByName($flatIndexEntities);
        $rebuilt = $this->rebuildDefinitionResult($definitionResult[$table], $filteredIndexes);

        return [$table => $rebuilt];
    }


    protected function extractIndexEntitiesWithOrigin(array $definitions): array
    {
        $indexEntitiesByName = [];

        foreach ($definitions as $originType => $entries) {
            foreach ($entries as $definition) {
                if (!$definition instanceof AbstractIndexEntity) {
                    continue;
                }

                $name = $definition->getName();

                $indexEntitiesByName[$name][] = [
                    'entity' => $definition,
                    'originType' => $originType,
                ];
            }
        }

        return $indexEntitiesByName;
    }

    protected function filterDuplicateIndexesByName(array $indexEntitiesByName): array
    {
        $filtered = [];

        foreach ($indexEntitiesByName as $name => $entries) {
            $primary = null;
            $foreign = null;
            $fallback = null;

            foreach ($entries as $entry) {
                $type = $entry['entity']->getIndexType();

                if ($type === 'primary') {
                    $primary = $entry;
                } elseif ($type === 'foreignKey') {
                    $foreign = $entry;
                } elseif (!$fallback) {
                    $fallback = $entry;
                }
            }

            if ($primary) {
                $filtered[$name] = $primary;
            } elseif ($foreign) {
                $filtered[$name] = $foreign;
            } elseif ($fallback) {
                $filtered[$name] = $fallback;
            }
        }

        return $filtered;
    }

    protected function rebuildDefinitionResult(array $original, array $filteredIndexes): array
    {
        $result = [];

        // Erstmal alles übernehmen, was kein AbstractIndexEntity ist
        foreach ($original as $type => $entries) {
            foreach ($entries as $key => $entry) {
                if (!$entry instanceof AbstractIndexEntity) {
                    $result[$type][$key] = $entry;
                }
            }
        }

        // Jetzt die gefilterten Index-Elemente einfügen, an Ursprungsposition
        foreach ($filteredIndexes as $name => $entry) {
            $origin = $entry['originType'];
            $result[$origin][$name] = $entry['entity'];
        }

        return $result;
    }


}