<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;

class FieldMapper extends AbstractMapper
{
    public function map(array $data): array
    {
        $result = [];
        foreach ($data as $field) {
            if ($field instanceof FieldEntity) {
                $result[] = $this->generateField($field);
            }
        }

        return $result;
    }

    protected function generateField(FieldEntity $fieldEntity): string
    {
        $argumentString = $this->renderArguments($fieldEntity->getArguments());

        $methods = [
            $fieldEntity->getType() . "('" . $fieldEntity->getColumnName() . "'" . $argumentString . ")",
            ...$this->getFluentOptions($fieldEntity->getOptions()),
        ];

        return $this->chainMethodsToString($methods);
    }

    private function renderArguments(array $arguments): string
    {
        $args = [];

        foreach ($arguments as $arg) {
            if ($arg === null) {
                continue;
            }

            if (is_bool($arg)) {
                $args[] = $arg ? 'true' : 'false';
            } elseif (is_int($arg)) {
                $args[] = $arg;
            } else {
                $args[] = "'" . $arg . "'";
            }
        }

        return count($args) > 0 ? ', ' . implode(', ', $args) : '';
    }

    private function getFluentOptions(array $options): array
    {
        $methods = [];

        if (!empty($options['default'])) {
            $methods[] = $options['default'] === 'CURRENT_TIMESTAMP'
                ? "default(DB::raw('CURRENT_TIMESTAMP'))"
                : "default('" . $options['default'] . "')";
        }

        if (!empty($options['unsigned'])) {
            $methods[] = 'unsigned()';
        }

        if (!empty($options['nullable'])) {
            $methods[] = 'nullable()';
        }

        if (!empty($options['comment'])) {
            $methods[] = "comment('" . addcslashes($options['comment'], "\\'") . "')";
        }

        return $methods;
    }
}