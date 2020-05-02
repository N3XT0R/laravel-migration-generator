<?php

use N3XT0R\MigrationGenerator\Service\Generator\Definition;

return [
    'table' => [
        'class' => Definition\TableDefinition::class,
        'dependsOn' => null,
    ],
    'index' => [
        'class' => Definition\IndexDefinition::class,
        'dependsOn' => 'table',
    ],
    'foreignKey' => [
        'class' => Definition\ForeignKeyDefinition::class,
        'dependsOn' => 'table',
    ],
];