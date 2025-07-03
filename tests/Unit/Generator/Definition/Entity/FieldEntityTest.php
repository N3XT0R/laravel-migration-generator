<?php


namespace Tests\Unit\Generator\Definition\Entity;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use PHPUnit\Framework\TestCase;

class FieldEntityTest extends TestCase
{

    protected $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new FieldEntity();
    }

    public function testSetAndGetLocalTableAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setTable($value);
        $gotValue = $this->entity->getTable();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetColumnNameAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setColumnName($value);
        $gotValue = $this->entity->getColumnName();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetTypeAreSame(): void
    {
        $value = uniqid('value', true);
        $this->entity->setType($value);
        $gotValue = $this->entity->getType();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetOptionsAreSame(): void
    {
        $value = [uniqid('value', true)];
        $this->entity->setOptions($value);
        $gotValue = $this->entity->getOptions();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetArgumentsAreSame(): void
    {
        $value = [uniqid('value', true)];
        $this->entity->setArguments($value);
        $gotValue = $this->entity->getArguments();
        $this->assertSame($value, $gotValue);
    }
}