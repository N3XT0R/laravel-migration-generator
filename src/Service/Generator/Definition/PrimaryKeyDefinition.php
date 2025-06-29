<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;

class PrimaryKeyDefinition extends AbstractDefinition
{
    protected function generateData(): array
    {
        $table = $this->getAttributeByName('tableName');
        $schema = $this->getSchema();

        $result = [];
        
        $tableDetails = $schema->introspectTable($table);
        $pk = $tableDetails->getPrimaryKey();

        if ($pk !== null) {
            $entity = new PrimaryKeyEntity();
            $entity->setColumns($pk->getColumns());
            $entity->setName($pk->getName());
            $result[] = $entity;
        }

        return $result;
    }

}