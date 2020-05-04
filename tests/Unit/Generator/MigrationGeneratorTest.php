<?php


namespace Tests\Unit\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\MigrationGeneratorInterface;
use Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
    protected $generator;

    public function setUp(): void
    {
        parent::setUp();
        $this->generator = $this->app->get(MigrationGeneratorInterface::class);
    }
}