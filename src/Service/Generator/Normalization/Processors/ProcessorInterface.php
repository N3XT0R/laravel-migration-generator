<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

interface ProcessorInterface
{
    public function __invoke(ResultEntity $result): ResultEntity;
}