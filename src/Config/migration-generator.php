<?php

use N3XT0R\MigrationGenerator\Service\Generator\Definition;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;

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
    'mapper' => [
        'table' => [
            'class' => Mapper\FieldMapper::class,
            'requires' => [],
        ],
        'index' => [
            'class' => Mapper\IndexMapper::class,
            'requires' => ['table'],
        ],
    ],
];