<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper;


abstract class AbstractMapper implements MapperInterface
{
    public function chainMethodsToString(array $methods): string
    {
        $result = '';
        if (0 !== count($methods)) {
            $result = '$table';
            foreach ($methods as $method) {
                $result .= '->' . $method;
            }
            $result .= ';' . PHP_EOL;
        }


        return $result;
    }
}