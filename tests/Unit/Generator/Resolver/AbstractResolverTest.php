<?php


namespace Tests\Unit\Generator\Resolver;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\ResultEntity;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\IndexDefinition;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\AbstractResolver;
use Tests\TestCase;

class AbstractResolverTest extends TestCase
{
    protected $resolver;

    public function setUp(): void
    {
        parent::setUp();
        $doctrine = $this->getDoctrineConnection($this->getDatabaseManager());
        $this->resolver = new class($doctrine, []) extends AbstractResolver {

            public function resolveTableSchema(string $schema, string $table): ResultEntity
            {
                return new ResultEntity();
            }
        };
    }

    public function testSetAndGetDefinitionsAreSame(): void
    {
        $definitions = [uniqid('test', true) => time()];
        $this->resolver->setDefinitions($definitions);
        $gotDefinitions = $this->resolver->getDefinitions();
        $this->assertSame($definitions, $gotDefinitions);
    }

    public function testGetDefinitionByNameWorks(): void
    {
        $definitions = ['name' => ['class' => IndexDefinition::class]];
        $this->resolver->setDefinitions($definitions);
        $this->assertInstanceOf($definitions['name']['class'], $this->resolver->getDefinitionByName('name'));
    }

    public function testAddDefinitionWorks(): void
    {
        $this->assertCount(0, $this->resolver->getDefinitions());
        $this->resolver->addDefinition('test', ['class' => 'test']);
        $this->assertCount(1, $this->resolver->getDefinitions());
    }

    /**
     * @param  bool  $expectedResult
     * @testWith    [true]
     *              [false]
     */
    public function testHasDefinition(bool $expectedResult): void
    {
        if ($expectedResult) {
            $this->resolver->addDefinition('test', ['class' => IndexDefinition::class]);
        }

        $this->assertSame($expectedResult, $this->resolver->hasDefinition('test'));
    }
}