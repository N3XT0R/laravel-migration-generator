<?php


namespace Tests\Unit\Generator;


use N3XT0R\MigrationGenerator\Service\Generator\MigrationGenerator;
use N3XT0R\MigrationGenerator\Service\Generator\Resolver\DefinitionResolver;
use Tests\TestCase;

class MigrationGeneratorTest extends TestCase
{
    protected $generator;

    public function setUp(): void
    {
        parent::setUp();
        $resolver = new DefinitionResolver();
        $generator = new MigrationGenerator();


        $this->generator = $generator;
    }
}