<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

/**
 * Holds normalization state throughout the processing pipeline.
 *
 * Provides access to original, previous and current versions
 * of the result for comparative or cumulative normalization steps.
 */
class NormalizationContext
{
    private readonly ResultEntity $original;
    private ResultEntity $previous;
    private ResultEntity $current;

    public function __construct(ResultEntity $initial)
    {
        $this->original = clone $initial;
        $this->previous = clone $initial;
        $this->current = $initial;
    }

    public function getOriginal(): ResultEntity
    {
        return $this->original;
    }

    public function getPrevious(): ResultEntity
    {
        return $this->previous;
    }

    public function getCurrent(): ResultEntity
    {
        return $this->current;
    }

    public function update(ResultEntity $new): void
    {
        $this->previous = clone $this->current;
        $this->current = $new;
    }

    public function getTableResults(string $tableName): array
    {
        return $this->current->getResults()[$tableName] ?? [];
    }

    /**
     * Checks whether current differs from previous.
     */
    public function hasChanged(): bool
    {
        return $this->serializeEntity($this->getCurrent()) !== $this->serializeEntity($this->getPrevious());
    }

    /**
     * Returns basic diff information between current and previous for a specific table.
     * Shows added and removed keys (not deep diffs).
     */
    public function diffTable(string $tableName): array
    {
        $current = $this->getCurrent()->getResults()[$tableName] ?? [];
        $previous = $this->getPrevious()->getResults()[$tableName] ?? [];

        $added = array_diff_key($current, $previous);
        $removed = array_diff_key($previous, $current);

        return [
            'added_keys' => array_keys($added),
            'removed_keys' => array_keys($removed),
        ];
    }

    private function serializeEntity(ResultEntity $entity): string
    {
        return md5(json_encode($entity->getResults(), JSON_THROW_ON_ERROR));
    }
}
