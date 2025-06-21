<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

class NormalizationContext
{
    protected readonly ResultEntity $original;
    protected ResultEntity $previous;
    protected ResultEntity $current;

    public function __construct(ResultEntity $entity)
    {
        $this->original = clone $entity;
        $this->previous = clone $entity;
        $this->current = $entity;
    }

    public function getTableResults(string $tableName): array
    {
        return $this->current->getResults()[$tableName] ?? [];
    }

    public function getCurrent(): ResultEntity
    {
        return $this->current;
    }

    public function getPrevious(): ResultEntity
    {
        return $this->previous;
    }


    public function update(ResultEntity $new): void
    {
        $this->previous = clone $this->current;
        $this->current = $new;
    }
}