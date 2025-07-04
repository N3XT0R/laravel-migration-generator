<?php

declare(strict_types=1);

namespace Generator\Compiler\Mapper;

use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\PrimaryKeyMapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\IndexEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\PrimaryKeyEntity;
use PHPUnit\Framework\TestCase;

class PrimaryKeyMapperTest extends TestCase
{
    protected PrimaryKeyMapper $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = new PrimaryKeyMapper();
    }

    public function testMapPrimaryKeysWorks(): void
    {
        $this->assertCount(1, $this->mapper->map([new PrimaryKeyEntity()]));
    }

    public function testMapNotPrimaryKeysNotWorks(): void
    {
        $this->assertCount(0, $this->mapper->map([new IndexEntity()]));
    }

    public function testMapCombinedPrimaryKeyWorks(): void
    {
        $pk = new PrimaryKeyEntity();
        $pk->setName('combined');
        $pk->setColumns(['id', 'role_id']);
        $result = $this->mapper->map([$pk]);
        $this->assertStringContainsString("\$table->primary(['id', 'role_id'], 'combined');", $result[0]);
    }

    public function testMapSingleNamedPrimaryKeyWorks(): void
    {
        $pk = new PrimaryKeyEntity();
        $pk->setName('PRIMARY');
        $pk->setColumns(['id']);
        $result = $this->mapper->map([$pk]);
        $this->assertStringContainsString("\$table->primary(['id'], 'PRIMARY');", $result[0]);
    }

    public function testMapSingleNotNamedPrimaryKeyWorks(): void
    {
        $pk = new PrimaryKeyEntity();
        $pk->setColumns(['id']);
        $result = $this->mapper->map([$pk]);
        $this->assertStringContainsString("\$table->primary(['id']);", $result[0]);
    }
}