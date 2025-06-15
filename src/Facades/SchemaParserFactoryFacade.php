<?php

namespace N3XT0R\MigrationGenerator\Facades;

use Illuminate\Support\Facades\Facade;
use N3XT0R\MigrationGenerator\Service\Parser\SchemaParserFactoryInterface;

class SchemaParserFactoryFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SchemaParserFactoryInterface::class;
    }
}