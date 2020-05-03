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
        $argumentString = '';
        $arguments = $fieldEntity->getArguments();
        $options = $fieldEntity->getOptions();
        foreach ($arguments as $argument) {
            if (null !== $argument) {
                $argumentString .= ', ';
                if (is_bool($argument)) {
                    $argumentString .= $argument ? 'true' : 'false';
                } else {
                    $argumentString .= "'" . $argument . "'";
                }
            }
        }

        $methods = [
            $fieldEntity->getType() . "('" . $fieldEntity->getColumnName() . "'" . $argumentString . ")",
        ];

        if (array_key_exists('default', $options) && null !== $options['default']) {
            if ('CURRENT_TIMESTAMP' === $options['default']) {
                $default = "default(DB::raw('CURRENT_TIMESTAMP'))";
            } else {
                $default = "default('" . $options['default'] . "')";
            }

            $methods[] = $default;
        }

        if (array_key_exists('unsigned', $options) && true === $options['unsigned']) {
            $methods[] = "unsigned()";
        }

        if (array_key_exists('nullable', $options) && true === $options['nullable']) {
            $methods[] = 'nullable()';
        }

        if (array_key_exists('comment', $options) && !empty($options['comment'])) {
            $methods[] = "comment('" . addcslashes($options['comment'], "\\'") . "')";
        }

        return $this->chainMethodsToString($methods);
    }
}