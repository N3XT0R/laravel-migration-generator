<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;

use Doctrine\DBAL\Connection as DoctrineConnection;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\DefinitionInterface;
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
        if (false === $schemaManager->tablesExist($table)) {
            throw new \InvalidArgumentException('Table ' . $table . ' not exists!');
        }
        $definitionResult = [];

        foreach ($sortedDefinitions as $name) {
            $definitionClass = $this->getDefinitionByName($name);
            if (null !== $schemaManager && $definitionClass instanceof DefinitionInterface) {
                $dependencies = $definitions[$name]['requires'];
                $definitionClass->setAttributes(
                    [
                        'database' => $schema,
                        'tableName' => $table,
                    ]
                );

                foreach ($dependencies as $dependency) {
                    if (array_key_exists($dependency, $definitionResult[$table])) {
                        $definitionClass->addAttribute($dependency, $definitionResult[$table][$dependency]);
                    }
                }

                $definitionClass->setSchema($schemaManager);
                $definitionClass->generate();

                if (!array_key_exists($table, $definitionResult)) {
                    $definitionResult[$table] = [];
                }

                $definitionResult[$table][$name] = $definitionClass->getResult();
            }
        }

        $result = new ResultEntity();
        $result->setTableName($table);
        $result->setResults($definitionResult);

        return $result;
    }
}