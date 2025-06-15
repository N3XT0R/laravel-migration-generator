<?php

namespace N3XT0R\MigrationGenerator\Service\Parser;


use Illuminate\Database\ConnectionInterface;

interface SchemaParserFactoryInterface
{
    public static function create(ConnectionInterface $connection): SchemaParserInterface;
}