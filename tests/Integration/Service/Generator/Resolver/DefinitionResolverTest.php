<?php


namespace Tests\Integration\Service\Generator\Resolver;


use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolverInterface;
use Tests\DbTestCase;

class DefinitionResolverTest extends DbTestCase
{
    protected $resolver;

    public function setUp(): void
    {
        parent::setUp();
        $doctrine = $this->getDoctrineConnection($this->getDatabaseManager());
        $this->resolver = $this->app->make(DefinitionResolverInterface::class, ['connection' => $doctrine]);
    }

    public function testResolveTableSchemaWorksCorrect(): void
    {
        $result = $this->resolver->resolveTableSchema('testing', 'fields_test');
        $this->assertTrue($result->hasResultForTable('fields_test'));
    }

    public function testMakeDefinitionResolverWithoutParametersThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('missing key connection in params.');
        $this->app->make(DefinitionResolverInterface::class);
    }
}