<?php


namespace Tests\Unit\Generator;


use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompilerInterface;
use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolver;
use Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
    protected $generator;

    public function setUp(): void
    {
        parent::setUp();
        /**
         * @var DatabaseManager $dbManager
         */
        $dbManager = $this->app->get('db');
        $doctrine = $dbManager->connection()->getDoctrineConnection();
        $resolver = new DefinitionResolver($doctrine, []);
        $compiler = $this->app->make(MigrationCompilerInterface::class);
        $generator = new MigrationGenerator($resolver, $compiler);
        $this->generator = $generator;
    }

    public function testSetAndGetResolverAreSame(): void
    {
        $doctrine = $this->generator->getResolver()->getDoctrineConnection();
        $resolver = new DefinitionResolver($doctrine, []);

        $this->generator->setResolver($resolver);
        $gotResolver = $this->generator->getResolver();
        $this->assertSame($resolver, $gotResolver);
    }
}