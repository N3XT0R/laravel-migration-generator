<?php


namespace Tests\Unit\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\AbstractMapper;
use Tests\TestCase;

class AbstractMapperTest extends TestCase
{
    protected $abstractMapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->abstractMapper = $this->getMockForAbstractClass(AbstractMapper::class);
    }

    public function chainMethodProvider(): array
    {
        return [
            [
                [
                    'test()',
                    'test2()',
                    'test3()',
                ],
                '$table->test()->test2()->test3();',
            ],
            [
                [
                    'nullable()',
                ],
                '$table->nullable();',
            ],
        ];
    }

    /**
     * @param array $methods
     * @param string $expectedResult
     * @dataProvider chainMethodProvider
     */
    public function testChainMethodsToStringWorks(array $methods, string $expectedResult): void
    {
        $this->assertStringContainsString($expectedResult, $this->abstractMapper->chainMethodsToString($methods));
    }
}