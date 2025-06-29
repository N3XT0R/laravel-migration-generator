<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;

abstract class AbstractIndexEntity
{
    protected string $name = '';

    protected string $indexType = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIndexType(): string
    {
        return $this->indexType;
    }

    public function setIndexType(string $indexType): void
    {
        $this->indexType = $indexType;
    }

    
}