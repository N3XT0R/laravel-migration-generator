<?php

use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition;

return [
    'definitions' => [
        'table' => [
            'class' => Definition\TableDefinition::class,
            'requires' => [],
        ],
        'foreignKey' => [
            'class' => Definition\ForeignKeyDefinition::class,
            'requires' => ['table'],
        ],
        'index' => [
            'class' => Definition\IndexDefinition::class,
            'requires' => ['table', 'foreignKey'],
        ],
    ],
    'mapper' => [
        'table' => [
            'class' => Mapper\FieldMapper::class,
            'requires' => [],
        ],

        'foreignKey' => [
            'class' => Mapper\ForeignKeyMapper::class,
            'requires' => ['table'],
        ],
        'index' => [
            'class' => Mapper\IndexMapper::class,
            'requires' => ['table', 'foreignKey'],
        ],
    ],

];