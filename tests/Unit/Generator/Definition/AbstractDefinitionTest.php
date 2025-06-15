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
        $this->definition = new class extends AbstractDefinition {

            protected function generateData(): array
            {
                return [];
            }
        };
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
     * @param  string  $attributeName
     * @param  bool  $expectedResult
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


    public function testGetAttributeByNameWorks(): void
    {
        $value = uniqid('value', true);
        $this->definition->addAttribute('test', $value);

        $this->assertSame($value, $this->definition->getAttributeByName('test'));
    }

    public function testGetAttributeByNameReturnsNull(): void
    {
        $this->assertNull($this->definition->getAttributeByName('test'));
    }

    public function testSetAndGetSchemaAreSame(): void
    {
        $schema = $this->getDoctrineSchemaManager($this->getDatabaseManager());

        $this->definition->setSchema($schema);
        $gotSchema = $this->definition->getSchema();
        $this->assertSame($schema, $gotSchema);
    }

    /**
     * @param  bool  $expectedResult
     * @testWith    [true]
     *              [false]
     */
    public function testHasSchema(bool $expectedResult): void
    {
        if (true === $expectedResult) {
            $schema = $this->getDoctrineConnection($this->getDatabaseManager())->createSchemaManager();
            $this->definition->setSchema($schema);
        }

        $this->assertSame($expectedResult, $this->definition->hasSchema());
    }

    public function testGenerateThrowsRuntimeException(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('missing Schema on Definition!');
        $this->definition->generate();
    }
}