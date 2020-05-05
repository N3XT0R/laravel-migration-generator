<?php


namespace Tests\Integration\Service\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use Tests\DbTestCase;

class MigrationGeneratorTest extends DbTestCase
{
    protected $generator;

    public function setUp(): void
    {
        parent::setUp();
        $this->generator = $this->app->make(MigrationGeneratorInterface::class);
    }

    public function testGenerateMigrationForTable(): void
    {
    }
}