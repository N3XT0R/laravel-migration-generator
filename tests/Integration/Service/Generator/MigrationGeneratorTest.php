<?php


namespace Tests\Integration\Service\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use Tests\DbTestCase;

class MigrationGeneratorTest extends DbTestCase
{
    protected MigrationGeneratorInterface $generator;

    public function setUp(): void
    {
        parent::setUp();
        $this->generator = $this->app->make(
            MigrationGeneratorInterface::class,
            ['connectionName' => $this->getDatabaseFromEnv()]
        );
    }


    public function testMakeGeneratorWithoutConnectionNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('missing key connectionName in params.');
        $this->app->make(
            MigrationGeneratorInterface::class
        );
    }

    public function testGenerateMigrationForTable(): void
    {
        $path = $this->resourceFolder.'ExpectedMigrations/';
        $this->generator->setMigrationDir($path);
        $result = $this->generator->generateMigrationForTable('testing', 'fields_test');
        $this->assertTrue($result);
        $files = $this->generator->getMigrationFiles();
        foreach ($files as $file) {
            $this->assertFileExists($path.DIRECTORY_SEPARATOR.$file);
        }
    }

    public function testGenerateMigrationForTableWithUnknownTableReturnsFalse(): void
    {
        $path = $this->resourceFolder.'ExpectedMigrations/';
        $this->generator->setMigrationDir($path);
        $result = $this->generator->generateMigrationForTable('testing', uniqid('test', true));
        $this->assertFalse($result);
        $this->assertCount(1, $this->generator->getErrorMessages());
    }

    public function tearDown(): void
    {
        $files = glob($this->resourceFolder.'ExpectedMigrations/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }
}