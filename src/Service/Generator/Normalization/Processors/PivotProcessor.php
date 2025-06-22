<?php

namespace N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;

class PivotProcessor implements ProcessorInterface
{
    public function process(NormalizationContext $context): ResultEntity
    {
        $result = $context->getCurrent();
        $results = $result->getResults();

        foreach ($results as $tableName => $definition) {
            $primary = $definition['primary'] ?? [];

            if (is_array($primary) && count($primary) > 1) {
                $definition['columns'] = [
                        'id' => [
                            'type' => 'int',
                            'primary' => true,
                            'autoIncrement' => true,
                        ]
                    ] + $definition['columns'];

                foreach ($primary as $column) {
                    if (isset($definition['columns'][$column])) {
                        unset($definition['columns'][$column]['primary']);
                        $definition['columns'][$column]['foreign'] = $this->guessForeignTarget($column);
                    }
                }

                // 3. Neue primary definition
                $definition['primary'] = ['id'];

                $results[$tableName] = $definition;
                dd($results);
                $result->setResults($results);
            }
        }

        $context->update($result);
        return $result;
    }

    private function guessForeignTarget(string $column): string
    {
        // z. B. user_id → users.id
        $name = rtrim($column, '_id') . 's';
        return "$name.id";
    }
}