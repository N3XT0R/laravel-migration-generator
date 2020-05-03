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
                $result[] = $this->generate($field);
            }
        }


        return $result;
    }

    protected function generate(FieldEntity $fieldEntity): string
    {
        $argumentString = '';
        $arguments = $fieldEntity->getArguments();
        $options = $fieldEntity->getOptions();
        foreach ($arguments as $argument) {
            $argumentString .= "', '" . $argument;
        }

        $methods = [
            $fieldEntity->getType() . "('" . $fieldEntity->getColumnName() . $argumentString . "')",
        ];

        if (array_key_exists('nullable', $options) && true === $options['nullable']) {
            $methods[] = 'nullable()';
        }

        if (array_key_exists('default', $options) && null !== $options['default']) {
            $methods[] = "default('" . $options['default'] . "')";
        }

        return $this->chainMethodsToString($methods);
    }
}