<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Resolver;


use Doctrine\DBAL\Connection as DoctrineConnection;

interface DoctrineTypeMappingsInterface
{
    public function registerDoctrineTypeMappings(DoctrineConnection $doctrineConnection): void;
}