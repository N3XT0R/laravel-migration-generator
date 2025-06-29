<?php

namespace Tests\Integration\Console\Commands;

use Illuminate\Support\Facades\File;
use Tests\DbTestCase;

class MigrationGeneratorCommandTest extends DbTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('migration-generator.config.migration_dir', $this->migrationPath);

        if (!File::exists($this->migrationPath)) {
            File::makeDirectory($this->migrationPath);
        }

        File::cleanDirectory($this->migrationPath);
    }

    public function testCommandGeneratesMigrationFiles(): void
    {
        config()->set('migration-generator.normalizer.enabled', []);
        config()->set('migration-generator.defaults.normalizer', []);
        $this->artisan('migrate:regenerate', ['--database' => $this->getDatabaseFromEnv()])
            ->assertExitCode(0);
        $files = File::files($this->migrationPath);
        self::assertNotEmpty($files, 'No Migrations generated');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        //File::cleanDirectory($this->migrationPath);
    }
}