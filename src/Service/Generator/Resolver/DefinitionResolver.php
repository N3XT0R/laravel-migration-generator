<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\DefinitionInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Sort\TopSort;

class DefinitionResolver extends AbstractResolver
{

    public function resolveTableSchema(string $schema, string $table): ResultEntity
    {
        $definitions = $this->getDefinitions();
        $sortedDefinitions = TopSort::sort($definitions);
        $connection = $this->getDoctrineConnection();

        $schemaManager = $connection->getSchemaManager();
        if (null !== $schemaManager && false === $schemaManager->tablesExist($table)) {
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