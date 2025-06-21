<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;

class PrimaryKeyProcessor implements ProcessorInterface
{
    public function process(NormalizationContext $context): ResultEntity
    {
        $current = $context->getCurrent();


        return $current;
    }

}