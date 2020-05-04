<?php


namespace Tests\Unit\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\ForeignKeyMapper;
use Tests\TestCase;

class ForeignKeyMapperTest extends TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = new ForeignKeyMapper();
    }

    public function testMapWithAnyOtherDataThanForeignKeysShouldNotWork(): void
    {
        $data = [1, 2, 3];
        $this->assertCount(0, $this->mapper->map($data));
    }
}