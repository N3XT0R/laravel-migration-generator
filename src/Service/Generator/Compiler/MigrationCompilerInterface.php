<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

interface MigrationCompilerInterface
{

    public function getRenderedTemplate(): string;

    public function generateByResult(ResultEntity $resultEntity, string $customMigrationClass = ''): void;

    public function writeToDisk(string $name, string $path): bool;
}