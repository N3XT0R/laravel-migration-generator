<?php


namespace Tests\Unit\Generator\Definition;


use N3XT0R\MigrationGenerator\Service\Generator\Definition\AbstractDefinition;
use Tests\TestCase;

class AbstractDefinitionTest extends TestCase
{
    protected $definition;

    public function setUp(): void
    {
        parent::setUp();
        $this->definition = $this->getMockForAbstractClass(AbstractDefinition::class);
    }

    public function testSetAndGetAttributesAreSame(): void
    {
        $value = [uniqid('value', true)];
        $this->definition->setAttributes($value);
        $gotValue = $this->definition->getAttributes();
        $this->assertSame($value, $gotValue);
    }

    public function testSetAndGetResultAreSame(): void
    {
        $value = [uniqid('value', true)];
        $this->definition->setResult($value);
        $gotValue = $this->definition->getResult();
        $this->assertSame($value, $gotValue);
    }

    /**
     * @param string $attributeName
     * @param bool $expectedResult
     * @testWith ["test", true]
     *           ["test2", false]
     */
    public function testHasAttribute(string $attributeName, bool $expectedResult): void
    {
        if ($expectedResult) {
            $this->definition->addAttribute($attributeName, 'test');
        }

        $this->assertSame($expectedResult, $this->definition->hasAttribute($attributeName));
    }
}