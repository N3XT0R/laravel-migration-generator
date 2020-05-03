<?php

use N3XT0R\MigrationGenerator\Service\Generator\Definition;

return [
    'definitions' => [
        'table' => [
            'class' => Definition\TableDefinition::class,
            'requires' => [],
        ],
        'index' => [
            'class' => Definition\IndexDefinition::class,
            'requires' => ['table'],
        ],
        'foreignKey' => [
            'class' => Definition\ForeignKeyDefinition::class,
            'requires' => ['table'],
        ],
    ],
];