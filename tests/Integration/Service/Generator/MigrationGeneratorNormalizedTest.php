<?php

namespace Service\Generator;

use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\Processors\PivotProcessor;
use N3XT0R\MigrationGenerator\Service\Generator\Normalization\SchemaNormalizationManager;
use Tests\DbTestCase;

class MigrationGeneratorNormalizedTest extends DbTestCase
{

    protected MigrationGeneratorInterface $generator;

    protected array $migrations = [
        'Database/Migrations/Features/schema-normalization',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->generator = $this->app->make(
            MigrationGeneratorInterface::class,
            ['connectionName' => $this->getDatabaseFromEnv()]
        );
    }

    public function testGenerateMigrationForTable(): void
    {
        $path = $this->resourceFolder . 'ExpectedMigrations/';
        $this->generator->setMigrationDir($path);
        $schemaNormalizationManager = new SchemaNormalizationManager([new PivotProcessor()]);
        $this->generator->setNormalizationManager($schemaNormalizationManager);
        $result = $this->generator->generateMigrationForTable($this->getDatabaseFromEnv(), 'role_customer');
        dd($this->generator->getErrorMessages());
        $this->assertTrue($result);
        $files = $this->generator->getMigrationFiles();
        foreach ($files as $file) {
            $this->assertFileExists($path . DIRECTORY_SEPARATOR . $file);
        }
    }
}