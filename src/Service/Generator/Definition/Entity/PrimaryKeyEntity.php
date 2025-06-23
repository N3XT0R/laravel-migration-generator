<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;

class PrimaryKeyEntity extends AbstractIndexEntity
{

    protected array $columns = [];

    protected string $indexType = 'primary';

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }
}