<?php

namespace N3XT0R\MigrationGenerator\Facades;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Facade;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserFactoryInterface;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserInterface;

/**
 * @method static SchemaParserInterface create(ConnectionInterface $connection)
 */
class SchemaParserFactoryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SchemaParserFactoryInterface::class;
    }
}