<?php


namespace Tests\Unit\Sort;


use N3XT0R\MigrationGenerator\Service\Generator\Sort\TopSort;
use Tests\TestCase;

class TopSortTest extends TestCase
{

    public function testSort(): void
    {
        $data = [
            'table3' => [
                'class' => 'xy',
                'requires' => ['table2'],
            ],
            'table' => [
                'class' => 'xy',
                'requires' => [],
            ],
            'table2' => [
                'class' => 'xy',
                'requires' => ['table'],
            ],

        ];

        $result = TopSort::sort($data);
        $this->assertEquals(
            [
                'table',
                'table2',
                'table3',
            ],
            $result
        );
    }
}