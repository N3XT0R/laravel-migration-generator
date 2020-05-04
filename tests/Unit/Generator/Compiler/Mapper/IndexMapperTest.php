<?php


namespace Tests\Unit\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\IndexMapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;
use Tests\TestCase;

class IndexMapperTest extends TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = new IndexMapper();
    }

    public function testMapWithAnyOtherDataThanIndexShouldNotWork(): void
    {
        $data = [1, 2, 3];
        $this->assertCount(0, $this->mapper->map($data));
    }

    /**
     * @param string $type
     * @testWith    ["index"]
     *              ["unique"]
     */
    public function testMapWithSingleIndexWorks(string $type): void
    {
        $index = new IndexEntity();
        $index->setType('index');
        $index->setColumns(['test']);
        $index->setName('testIndex');

        $result = $this->mapper->map([$index]);
        $this->assertCount(1, $result);

        $this->assertStringContainsString('$table->index(\'test\', \'testIndex\');', $result[0]);
    }
}