<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

interface ProcessorInterface
{
    public function process(ResultEntity $result): ResultEntity;
}