<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;
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
     * @var ProcessorInterface[]
     */
    protected array $processors = [];

    /**
     * @var string[]|null
     */
    protected ?array $enabledProcessors = null;

    /**
     * @param iterable<ProcessorInterface> $processors
     */
    public function __construct(iterable $processors = [], ?array $enabledProcessors = null)
    {
        $this->setEnabledProcessors($enabledProcessors);
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    /**
     * Adds a new processor to the normalization chain.
     */
    public function addProcessor(ProcessorInterface $processor): void
    {
        if (is_array($this->getEnabledProcessors())) {
            $key = $processor->getKey();
            if (!in_array($key, $this->getEnabledProcessors(), true)) {
                return;
            }
        }
        $this->processors[] = $processor;
    }

    /**
     * Returns all registered processors.
     * @return ProcessorInterface[]|iterable
     */
    public function getProcessors(): iterable
    {
        return $this->processors;
    }

    /**
     * Replaces all processors.
     */
    public function setProcessors(iterable $processors = []): void
    {
        $this->processors = [];
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    public function getEnabledProcessors(): ?array
    {
        return $this->enabledProcessors;
    }

    public function setEnabledProcessors(?array $enabledProcessors): void
    {
        $this->enabledProcessors = $enabledProcessors;
    }

    /**
     * Executes all processors on the given schema result using a shared context.
     *
     * @param ResultEntity $result
     * @return ResultEntity
     */
    public function normalize(ResultEntity $result): ResultEntity
    {
        $context = new NormalizationContext($result);
        $processors = $this->getProcessors();

        foreach ($processors as $processor) {
            $updated = $processor->process($context);
            $context->update($updated);
        }

        return $context->getCurrent();
    }
}
