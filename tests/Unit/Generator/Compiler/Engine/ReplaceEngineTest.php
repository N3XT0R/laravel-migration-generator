<?php


namespace Tests\Unit\Generator\Compiler\Engine;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Engine\ReplaceEngine;
use Tests\TestCase;

class ReplaceEngineTest extends TestCase
{
    protected $engine;

    public function setUp(): void
    {
        parent::setUp();
        $this->engine = new ReplaceEngine();
    }

    public function testGetPopulatesData(): void
    {
        $data = $this->engine->get(
            dirname(__DIR__, 5) . '/src/Stubs/CreateTableStub.stub',
            [
                'columns' => 'test'
            ]
        );
        $this->assertStringNotContainsString('{{$columns}}', $data);
    }
}