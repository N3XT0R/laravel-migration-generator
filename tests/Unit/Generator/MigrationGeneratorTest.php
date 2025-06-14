<?php


namespace Tests\Unit\Generator;


use Doctrine\DBAL\DriverManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\View\Factory as ViewFactory;
use N3XT0R\MigrationGenerator\Service\Generator\Compiler\MigrationCompiler;
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
        $dbConfig = $dbManager->connection()->getConfig();

        $connectionParams  = [
            'dbname' => $dbConfig['database'],
            'user' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'host' => $dbConfig['host'],
            'driver' => 'pdo_mysql',
        ];
        $doctrine = DriverManager::getConnection($connectionParams);
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

    public function testSetAndGetCompilerAreSame(): void
    {
        $compiler = new MigrationCompiler($this->app->make(ViewFactory::class), $this->app->make('files'));
        $this->generator->setCompiler($compiler);
        $gotCompiler = $this->generator->getCompiler();
        $this->assertSame($compiler, $gotCompiler);
    }

    public function testSetAndGetMigrationDirIsSame(): void
    {
        $migrationDir = uniqid('/', true);
        $this->generator->setMigrationDir($migrationDir);
        $gotDir = $this->generator->getMigrationDir();
        $this->assertSame($migrationDir, $gotDir);
    }

    public function testSetAndGetErrorMessagesAreSame(): void
    {
        $data = [time() => time()];
        $this->generator->setErrorMessages($data);
        $gotData = $this->generator->getErrorMessages();
        $this->assertSame($data, $gotData);
    }

    public function testAddErrorMessageWorks(): void
    {
        $this->assertCount(0, $this->generator->getErrorMessages());
        $this->generator->addErrorMessage('test');
        $this->assertCount(1, $this->generator->getErrorMessages());
    }

    public function testSetAndGetMigrationFilesAreSame(): void
    {
        $files = [uniqid('Test', true) => time()];
        $this->generator->setMigrationFiles($files);
        $gotFiles = $this->generator->getMigrationFiles();
        $this->assertSame($files, $gotFiles);
    }
    
}