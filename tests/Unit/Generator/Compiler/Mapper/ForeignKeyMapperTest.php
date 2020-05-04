<?php


namespace Tests\Unit\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\ForeignKeyMapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;
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

    public function testMapWithOneForeignKey(): void
    {
        $foreignKey = new ForeignKeyEntity();
        $foreignKey->setLocalColumn('test');
        $foreignKey->setLocalTable('table');
        $foreignKey->setReferencedColumn('id');
        $foreignKey->setReferencedTable('reference');

        $result = $this->mapper->map([$foreignKey]);
        $this->assertCount(1, $result);
        $this->assertStringContainsString(
            '$table->foreign(\'test\')->references(\'id\')->on(\'reference\');',
            $result[0]
        );
    }
}