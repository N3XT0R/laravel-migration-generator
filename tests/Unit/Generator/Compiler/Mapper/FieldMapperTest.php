<?php


namespace Tests\Unit\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\FieldMapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use Tests\TestCase;

class FieldMapperTest extends TestCase
{
    protected $mapper;

    public function setUp(): void
    {
        parent::setUp();
        $this->mapper = new FieldMapper();
    }

    public function testMapWorksOnlyWithFieldEntities(): void
    {
        $this->assertCount(0, $this->mapper->map([1, 2, 3]));
    }

    public function testMapWorksWithoutArgumentsAndWithoutOptions(): void
    {
        $field = new FieldEntity();
        $field->setType('bigInteger');
        $field->setColumnName('id');

        $data = [$field];

        $result = $this->mapper->map($data);
        $this->assertCount(1, $result);
        $this->assertStringContainsString("\$table->bigInteger('id');", $result[0]);
    }

    public function argumentProvider(): array
    {
        return [
            [
                [
                    true,
                ],
                ', true'
            ],
            [
                [
                    false,
                ],
                ', false'
            ],
            [
                [
                    true,
                    1
                ],
                ", true, 1"
            ],
            [
                [
                    false,
                    0
                ],
                ", false, 0"
            ],
            [
                [
                    false,
                    0,
                    'string'
                ],
                ", false, 0, 'string'"
            ],
        ];
    }

    /**
     * @param array $arguments
     * @param string $expectedResult
     * @dataProvider argumentProvider
     */
    public function testMapWorksWithArgumentsAndWithoutOptions(array $arguments, string $expectedResult): void
    {
        $field = new FieldEntity();
        $field->setType('bigInteger');
        $field->setColumnName('id');
        $field->setArguments($arguments);

        $data = [$field];

        $result = $this->mapper->map($data);
        $this->assertCount(1, $result);
        $this->assertStringContainsString(sprintf("\$table->bigInteger(%s);", "'id'" . $expectedResult), $result[0]);
    }
}