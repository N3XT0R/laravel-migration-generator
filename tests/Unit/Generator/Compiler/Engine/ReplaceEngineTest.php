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

    public function testGetPopulatesDataWithString(): void
    {
        $columns = uniqid('test', true);
        $data = $this->engine->get(
            dirname(__DIR__, 5) . '/src/Stubs/CreateTableStub.stub',
            [
                'columns' => $columns
            ]
        );
        $this->assertStringNotContainsString('{{$columns}}', $data);
        $this->assertStringContainsString($columns, $data);
    }

    public function testGetPopulatesDataWithArray(): void
    {
        $data = $this->engine->get(
            dirname(__DIR__, 5) . '/src/Stubs/CreateTableStub.stub',
            [
                'columns' => [
                    1,
                    2,
                    3
                ]
            ]
        );
        $this->assertStringNotContainsString('{{$columns}}', $data);
        $this->assertStringContainsString(123, $data);
    }
}