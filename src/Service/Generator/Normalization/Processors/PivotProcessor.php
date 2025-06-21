<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

class PivotProcessor implements ProcessorInterface
{
    public function __invoke(ResultEntity $result, ?ResultEntity $previousResult = null): ResultEntity
    {
        return $result;
    }

}