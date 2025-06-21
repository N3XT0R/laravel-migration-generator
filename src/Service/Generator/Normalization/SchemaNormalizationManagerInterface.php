<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\ProcessorInterface;

interface SchemaNormalizationManagerInterface
{
    public function __construct(iterable $processors = []);


    public function addProcessor(ProcessorInterface $processor): void;

    public function normalize(ResultEntity $result): mixed;

    public function getProcessors(): iterable;

    public function setProcessors(iterable $processors = []): void;
}