<?php


namespace N3XT0R\MigrationGenerator\Service\Generator;

use Illuminate\Support\Facades\DB;

class MigrationGenerator
{

    protected $doctrineConnection;

    public function __construct(string $connectionName)
    {
        $connection = DB::connection($connectionName)->getDoctrineConnection();
        var_dump($connection);
    }

    public function setDoctrineConnection($connection)
    {
    }
}