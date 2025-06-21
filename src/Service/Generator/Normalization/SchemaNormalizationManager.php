<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\ProcessorInterface;

/**
 * Coordinates the execution of schema normalization processors.
 *
 * Each Processor implements a specific part of the normalization pipeline
 * and is executed in a fixed order.
 *
 * This manager ensures that the normalization process is consistent
 * and reusable, while remaining open for extension.
 */
class SchemaNormalizationManager implements SchemaNormalizationManagerInterface
{
    /**
     * @var ProcessorInterface|\Closure[]
     */
    protected array $processors = [];

    /**
     * SchemaNormalizationManager constructor.
     *
     * @param iterable|ProcessorInterface[] $processors
     */
    public function __construct(iterable $processors = [])
    {
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    /**
     * Adds a new processor to the normalization chain.
     */
    public function addProcessor(ProcessorInterface|\Closure $processor): void
    {
        $this->processors[] = $processor;
    }

    public function getProcessors(): iterable
    {
        return $this->processors;
    }

    public function setProcessors(iterable $processors = []): void
    {
        $this->processors = $processors;
    }

    /**
     * Executes all processors on the given schema result.
     *
     * @param ResultEntity $result
     * @return ResultEntity
     */
    public function normalize(ResultEntity $result): ResultEntity
    {
        $processors = $this->getProcessors();
        foreach ($processors as $processor) {
            if (!is_callable($processor)) {
                throw new \LogicException('Processor is not callable.');
            }
            $result = $processor($result);
        }

        return $result;
    }
}