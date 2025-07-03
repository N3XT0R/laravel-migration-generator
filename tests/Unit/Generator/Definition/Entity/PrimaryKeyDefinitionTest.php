<?php

namespace Generator\Definition\Entity;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;
use PHPUnit\Framework\TestCase;

class PrimaryKeyDefinitionTest extends TestCase
{
    protected PrimaryKeyEntity $entity;

    public function setUp(): void
    {
        parent::setUp();
        $this->entity = new PrimaryKeyEntity();
    }

    public function testSetGetColumnsWorks(): void
    {
        $columns = ['test', 'test2'];
        $this->entity->setColumns($columns);
        $gotColumns = $this->entity->getColumns();
        $this->assertSame($columns, $gotColumns);
    }
}