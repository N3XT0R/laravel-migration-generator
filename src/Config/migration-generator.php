<?php

use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\PivotProcessor;

return [
    'config' => [
        'defaults' => [
            'normalizer' => [
                'enabled' => ['pivot'],
            ],
        ],
        'migration_dir' => database_path('migrations'),
    ],
    'definitions' => [
        'table' => [
            'class' => Definition\TableDefinition::class,
            'requires' => [],
        ],
        'primaryKey' => [
            'class' => Definition\PrimaryKeyDefinition::class,
            'requires' => ['table'],
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
        'primaryKey' => [
            'class' => Mapper\PrimaryKeyMapper::class,
            'requires' => ['table'],
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
    'normalizer' => [
        'pivot' => [
            'class' => PivotProcessor::class,
            'requires' => [],
        ],
    ],
];