<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;

class ConstraintProcessor implements ProcessorInterface
{
    public function process(NormalizationContext $context): ResultEntity
    {
        $result = $context->getCurrent();

        foreach ($result->getResults() as $table => $tableData) {
            // Beispiel: Normalize FK-Definitionsstruktur
            if (isset($tableData['foreign_keys'])) {
                // ... modifiziere $tableData ...
                //$result->getResults()[$table]['foreign_keys'] = $this->normalizeFks($tableData['foreign_keys']);
            }
        }

        $context->update($result);
        return $result;
    }

}