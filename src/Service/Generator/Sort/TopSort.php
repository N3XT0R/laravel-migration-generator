<?php


namespace N3XT0R\MigrationGenerator\Service\Generator\Sort;

use MJS\TopSort\Implementations\GroupedStringSort;

class TopSort
{
    public static function sort(array $array, string $dependencyKey = 'requires'): array
    {
        $topSort = new GroupedStringSort();
        foreach ($array as $key => $data) {
            $topSort->add($key, $key, $data[$dependencyKey]);
        }

        return $topSort->sort();
    }
}