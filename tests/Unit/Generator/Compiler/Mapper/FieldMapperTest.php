<?php


namespace Tests\Unit\Generator\Compiler\Mapper;


use N3XT0R\MigrationGenerator\Service\Generator\Compiler\Mapper\FieldMapper;
use N3XT0R\MigrationGenerator\Service\Generator\Definition\Entity\FieldEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

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

    public static function argumentProvider(): array
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
     * @param  array  $arguments
     * @param  string  $expectedResult
     */
    #[DataProvider('argumentProvider')]
    public function testMapWorksWithArgumentsAndWithoutOptions(array $arguments, string $expectedResult): void
    {
        $field = new FieldEntity();
        $field->setType('bigInteger');
        $field->setColumnName('id');
        $field->setArguments($arguments);

        $data = [$field];

        $result = $this->mapper->map($data);
        $this->assertCount(1, $result);
        $this->assertStringContainsString(sprintf("\$table->bigInteger(%s);", "'id'".$expectedResult), $result[0]);
    }

    public static function optionProvider(): array
    {
        return [
            [
                [
                    'default' => 'CURRENT_TIMESTAMP',
                ],
                "->default(DB::raw('CURRENT_TIMESTAMP'))"
            ],
            [
                [
                    'default' => '1',
                ],
                "->default('1')"
            ],
            [
                [
                    'default' => 'string',
                ],
                "->default('string')"
            ],
            [
                [
                    'unsigned' => true,
                ],
                "->unsigned()"
            ],
            [
                [
                    'nullable' => true,
                ],
                "->nullable()"
            ],
            [
                [
                    'comment' => 'test',
                ],
                "->comment('test')"
            ],
        ];
    }

    /**
     * @param  array  $options
     * @param  string  $expectedResult
     */
    #[DataProvider('optionProvider')]
    public function testMapWorksWithArgumentsAndWithOptions(array $options, string $expectedResult): void
    {
        $field = new FieldEntity();
        $field->setType('bigInteger');
        $field->setColumnName('id');
        $field->setArguments([true]);
        $field->setOptions($options);

        $data = [$field];

        $result = $this->mapper->map($data);
        $this->assertCount(1, $result);
        $this->assertStringContainsString(
            sprintf("\$table->bigInteger(%s)%s;", "'id', true", $expectedResult),
            $result[0]
        );
    }
}