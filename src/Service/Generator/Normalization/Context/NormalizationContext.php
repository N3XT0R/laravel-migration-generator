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
        // Deep clone for immutability of original state
        $this->original = clone $initial;
        $this->previous = clone $initial;
        $this->current = $initial;
    }

    /**
     * Returns the original unmodified schema result.
     */
    public function getOriginal(): ResultEntity
    {
        return $this->original;
    }

    /**
     * Returns the last state before the current processor ran.
     */
    public function getPrevious(): ResultEntity
    {
        return $this->previous;
    }

    /**
     * Returns the current schema state.
     */
    public function getCurrent(): ResultEntity
    {
        return $this->current;
    }

    /**
     * Updates the current state with a new result and stores the previous.
     */
    public function update(ResultEntity $new): void
    {
        $this->previous = clone $this->current;
        $this->current = $new;
    }

    /**
     * Returns the current table result for a given table name.
     */
    public function getTableResults(string $tableName): array
    {
        return $this->current->getResults()[$tableName] ?? [];
    }
}
