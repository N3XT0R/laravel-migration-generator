<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;

class ConstraintProcessor implements ProcessorInterface
{
    public function __invoke(ResultEntity $result): ResultEntity
    {
        return $result;
    }

}