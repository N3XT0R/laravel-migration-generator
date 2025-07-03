<?php


namespace Tests\Unit\Generator\Definition\Entity;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ForeignKeyEntity;
use PHPUnit\Framework\TestCase;

class ForeignKeyEntityTest extends TestCase
{
    protected $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new ForeignKeyEntity();
    }

    public function testSetAndGetNameAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setName($value);
        $gotValue = $this->entity->getName();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetLocalTableAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setLocalTable($value);
        $gotValue = $this->entity->getLocalTable();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetLocalColumnAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setLocalColumn($value);
        $gotValue = $this->entity->getLocalColumn();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetReferencedTableAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setReferencedTable($value);
        $gotValue = $this->entity->getReferencedTable();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetReferencedColumnAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setReferencedColumn($value);
        $gotValue = $this->entity->getReferencedColumn();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetOnUpdateAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setOnUpdate($value);
        $gotValue = $this->entity->getOnUpdate();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetOnDeleteAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setOnDelete($value);
        $gotValue = $this->entity->getOnDelete();
        $this->assertSame($value, $gotValue);
    }
}