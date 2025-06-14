<?php


namespace Tests\Unit\Generator\Resolver;


use Doctrine\DBAL\DriverManager;
use Illuminate\Database\DatabaseManager;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\IndexDefinition;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\AbstractResolver;
use Tests\TestCase;

class AbstractResolverTest extends TestCase
{
    protected $resolver;

    public function setUp(): void
    {
        parent::setUp();
        /**
         * @var DatabaseManager $dbManager
         */
        $dbManager = $this->app->get('db');
        $dbConfig = $dbManager->connection()->getConfig();
        $connectionParams  = [
            'dbname' => $dbConfig['database'],
            'user' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'host' => $dbConfig['host'],
            'driver' => 'pdo_mysql',
        ];
        $doctrine = DriverManager::getConnection($connectionParams);
        $this->resolver = $this->getMockForAbstractClass(
            AbstractResolver::class,
            [
                $doctrine,
                [],
            ]
        );
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
     * @param bool $expectedResult
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