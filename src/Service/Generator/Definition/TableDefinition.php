<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Definition;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Traits\AttributeAwareTrait;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Traits\AttributeAwareInterface;

class TableDefinition implements DefinitionInterface, AttributeAwareInterface
{
    use AttributeAwareTrait;
    
    protected $result;

    public function generate(): void
    {
    }
}