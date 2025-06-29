<?php

namespace Generator\Definition\Entity;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\AbstractIndexEntity;
use Tests\TestCase;

class AbstractIndexEntityTest extends TestCase
{
    protected AbstractIndexEntity $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new class() extends AbstractIndexEntity {
        };
    }

    public function testSetGetNameIsSame(): void
    {
        $name = uniqid('TEST_', true);
        $this->entity->setName($name);
        $gotName = $this->entity->getName();
        self::assertSame($name, $gotName);
    }

    public function testSetGetIndexTypeIsSame(): void
    {
        $indexType = uniqid('TEST_', true);
        $this->entity->setIndexType($indexType);
        $gotIndexType = $this->entity->getIndexType();
        self::assertSame($indexType, $gotIndexType);
    }
}