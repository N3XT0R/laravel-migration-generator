<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\ProcessorInterface;

interface SchemaNormalizationManagerInterface
{
    public function __construct(iterable $processors = []);


    public function addProcessor(ProcessorInterface $processor): void;

    public function normalize(ResultEntity $result): ResultEntity;

    public function getProcessors(): iterable;

    public function setProcessors(iterable $processors = []): void;

    public function setEnabledProcessors(?array $enabledProcessors): void;

    public function getEnabledProcessors(): ?array;
}