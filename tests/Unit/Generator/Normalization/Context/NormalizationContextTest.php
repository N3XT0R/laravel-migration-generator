<?php

namespace Tests\Unit\Normalization\Context;

use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Context\NormalizationContext;
use PHPUnit\Framework\TestCase;

class NormalizationContextTest extends TestCase
{
    public function testInitialStateIsConsistent(): void
    {
        $entity = new ResultEntity();
        $entity->setTableName('test');
        $entity->setResults(['test' => ['foo' => 'bar']]);

        $context = new NormalizationContext($entity);

        $this->assertSame($entity, $context->getCurrent());
        $this->assertNotSame($entity, $context->getOriginal());
        $this->assertNotSame($entity, $context->getPrevious());

        $this->assertEquals($entity->getResults(), $context->getOriginal()->getResults());
        $this->assertEquals($entity->getResults(), $context->getPrevious()->getResults());
    }

    public function testUpdateSetsPreviousAndCurrent(): void
    {
        $initial = new ResultEntity();
        $initial->setResults(['t1' => ['a' => 1]]);
        $context = new NormalizationContext($initial);

        $updated = new ResultEntity();
        $updated->setResults(['t1' => ['a' => 2]]);
        $context->update($updated);

        $this->assertSame($updated, $context->getCurrent());
        $this->assertNotSame($updated, $context->getPrevious());
        $this->assertEquals(['a' => 1], $context->getPrevious()->getResults()['t1']);
        $this->assertEquals(['a' => 2], $context->getCurrent()->getResults()['t1']);
    }

    public function testGetTableResultsReturnsExpectedData(): void
    {
        $entity = new ResultEntity();
        $entity->setResults([
            'users' => ['id' => 'field'],
            'posts' => ['id' => 'field']
        ]);

        $context = new NormalizationContext($entity);

        $this->assertEquals(['id' => 'field'], $context->getTableResults('users'));
        $this->assertEquals([], $context->getTableResults('nonExisting'));
    }

    public function testHasChangedDetectsDifference(): void
    {
        $entity = new ResultEntity();
        $entity->setResults(['table' => ['a' => 1]]);
        $context = new NormalizationContext($entity);

        $this->assertFalse($context->hasChanged());

        $modified = new ResultEntity();
        $modified->setResults(['table' => ['a' => 2]]);
        $context->update($modified);

        $this->assertTrue($context->hasChanged());
    }

    public function testDiffTableDetectsAddedAndRemovedKeys(): void
    {
        $entity = new ResultEntity();
        $entity->setResults(['example' => ['a' => 1, 'b' => 2]]);

        $context = new NormalizationContext($entity);

        $modified = new ResultEntity();
        $modified->setResults(['example' => ['b' => 2, 'c' => 3]]);
        $context->update($modified);

        $diff = $context->diffTable('example');
        $this->assertEquals(['c'], $diff['added_keys']);
        $this->assertEquals(['a'], $diff['removed_keys']);
    }
}
