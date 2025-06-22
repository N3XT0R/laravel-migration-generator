<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity;

abstract class AbstractIndexEntity
{
    protected string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}